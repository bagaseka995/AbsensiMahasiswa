<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\MataKuliah;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DosenController extends Controller
{
    /**
     * Dashboard Dosen/Admin
     * Menampilkan ringkasan: jumlah MK, total mahasiswa, statistik global
     */
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $mataKuliah = MataKuliah::with('dosen')->get();
        } else {
            $mataKuliah = $user->mataKuliahDiampu()->get();
        }

        $mataKuliahIds = $mataKuliah->pluck('id');

        // Total mahasiswa
        $totalMahasiswa = DB::table('mahasiswa_mata_kuliah')
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->distinct('user_id')
            ->count('user_id');

        // Statistik absensi global
        $statsAbsensi = Absensi::whereIn('mata_kuliah_id', $mataKuliahIds)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'Alpa' THEN 1 ELSE 0 END) as alpa
            ")
            ->first();

        $persentaseHadir = $statsAbsensi->total > 0
            ? round(($statsAbsensi->hadir / $statsAbsensi->total) * 100, 1)
            : 0;

        // Ambil sesi pertemuan yang sedang aktif
        $sesiAktif = SesiAbsensi::where('is_active', true)
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->with(['mataKuliah', 'absensi'])
            ->get();

        return view('dosen.dashboard', compact(
            'user', 'mataKuliah', 'totalMahasiswa',
            'statsAbsensi', 'persentaseHadir', 'sesiAktif'
        ));
    }

    /**
     * Halaman sesi absensi / pertemuan perkuliahan
     * Dosen membuka pertemuan baru dan memantau kehadiran mahasiswa
     */
    public function sesiAbsensi()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $daftarMK = MataKuliah::with('dosen')->get();
            $sesiAktif = SesiAbsensi::where('is_active', true)
                ->with(['mataKuliah', 'dosen', 'absensi.mahasiswa'])
                ->get();
            $riwayatSesi = SesiAbsensi::where('is_active', false)
                ->with(['mataKuliah', 'dosen'])
                ->withCount('absensi')
                ->orderBy('id', 'desc')
                ->take(15)
                ->get();
        } else {
            $daftarMK = $user->mataKuliahDiampu()->with('dosen')->get();
            $sesiAktif = SesiAbsensi::where('is_active', true)
                ->where('dosen_id', $user->id)
                ->with(['mataKuliah', 'dosen', 'absensi.mahasiswa'])
                ->get();
            $riwayatSesi = SesiAbsensi::where('is_active', false)
                ->where('dosen_id', $user->id)
                ->with(['mataKuliah', 'dosen'])
                ->withCount('absensi')
                ->orderBy('id', 'desc')
                ->take(15)
                ->get();
        }

        foreach ($daftarMK as $mk) {
            $last = SesiAbsensi::where('mata_kuliah_id', $mk->id)->max('pertemuan_ke') ?? 0;
            $mk->next_pertemuan = $last + 1;
        }

        return view('dosen.sesi-absensi', compact('daftarMK', 'sesiAktif', 'riwayatSesi'));
    }

    /**
     * Buka pertemuan absensi baru untuk mata kuliah (nomor pertemuan dihitung otomatis 1, 2, dst)
     */
    public function bukaAbsensi(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'topik' => 'nullable|string|max:255',
        ], [
            'mata_kuliah_id.required' => 'Pilih mata kuliah terlebih dahulu.',
        ]);

        $user = Auth::user();
        $mk = MataKuliah::findOrFail($request->mata_kuliah_id);

        // Pastikan dosen hanya bisa buka untuk MK yang diampu
        if (!$user->isAdmin() && $mk->dosen_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak membuka pertemuan untuk mata kuliah ini.');
        }

        // Cek apakah ada sesi aktif untuk mata kuliah ini yang belum ditutup
        $sesiAktif = SesiAbsensi::where('mata_kuliah_id', $mk->id)
            ->where('is_active', true)
            ->first();

        if ($sesiAktif) {
            return back()->with('error', 'Pertemuan ke-' . $sesiAktif->pertemuan_ke . ' untuk ' . $mk->nama_mk . ' saat ini masih aktif. Tutup sesi tersebut terlebih dahulu sebelum membuka pertemuan baru.');
        }

        // Hitung otomatis nomor pertemuan berikutnya (dimulai dari 1, lalu 2, 3, dst.)
        $lastPertemuan = SesiAbsensi::where('mata_kuliah_id', $mk->id)->max('pertemuan_ke') ?? 0;
        $nextPertemuan = $lastPertemuan + 1;

        // Buat sesi pertemuan baru
        SesiAbsensi::create([
            'mata_kuliah_id' => $mk->id,
            'dosen_id' => $user->id,
            'pertemuan_ke' => $nextPertemuan,
            'topik' => $request->topik,
            'secret_key' => Str::random(32),
            'tanggal' => now()->toDateString(),
            'is_active' => true,
            'started_at' => now(),
        ]);

        return back()->with('success', 'Pertemuan ke-' . $nextPertemuan . ' untuk ' . $mk->nama_mk . ' berhasil dibuka! Mahasiswa sekarang sudah dapat melakukan absensi di halaman mereka.');
    }

    /**
     * Tutup sesi absensi pertemuan
     */
    public function tutupAbsensi(Request $request, SesiAbsensi $sesi)
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $sesi->dosen_id !== $user->id) {
            return back()->with('error', 'Anda tidak berhak menutup sesi pertemuan ini.');
        }

        $sesi->update([
            'is_active' => false,
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Pertemuan ke-' . $sesi->pertemuan_ke . ' untuk ' . $sesi->mataKuliah->nama_mk . ' berhasil ditutup. Mahasiswa tidak dapat lagi absen untuk pertemuan ini.');
    }

    /**
     * Rekap absensi per mata kuliah (ringkasan persentase dan riwayat absensi beserta bukti foto)
     */
    public function rekap(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $daftarMK = MataKuliah::with('dosen')->get();
        } else {
            $daftarMK = $user->mataKuliahDiampu()->with('dosen')->get();
        }

        $selectedMK = null;
        $rekapData = collect();
        $riwayatAbsensi = collect();

        if ($request->has('mata_kuliah_id') && $request->mata_kuliah_id) {
            $selectedMK = MataKuliah::findOrFail($request->mata_kuliah_id);

            // Pastikan dosen hanya bisa lihat MK yang diampu
            if (!$user->isAdmin() && $selectedMK->dosen_id !== $user->id) {
                abort(403);
            }

            // Ambil semua mahasiswa yang terdaftar di MK ini
            $mahasiswaList = $selectedMK->mahasiswa()->get();

            foreach ($mahasiswaList as $mhs) {
                $absensi = Absensi::where('user_id', $mhs->id)
                    ->where('mata_kuliah_id', $selectedMK->id)
                    ->get();

                $total = $absensi->count();
                $hadir = $absensi->where('status', 'Hadir')->count();
                $izin = $absensi->where('status', 'Izin')->count();
                $sakit = $absensi->where('status', 'Sakit')->count();
                $alpa = $absensi->where('status', 'Alpa')->count();
                $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

                $rekapData->push((object)[
                    'mahasiswa' => $mhs,
                    'total' => $total,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => $persentase,
                ]);
            }

            // Ambil seluruh catatan absensi mata kuliah ini, urutkan dari yang terbaru
            $riwayatAbsensi = Absensi::where('mata_kuliah_id', $selectedMK->id)
                ->with(['mahasiswa', 'sesiAbsensi'])
                ->orderBy('tanggal', 'desc')
                ->orderBy('waktu', 'desc')
                ->get();
        }

        return view('dosen.rekap', compact('daftarMK', 'selectedMK', 'rekapData', 'riwayatAbsensi'));
    }

    /**
     * Laporan persentase kehadiran per mata kuliah
     */
    public function laporan()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $mataKuliahList = MataKuliah::with('dosen')->get();
        } else {
            $mataKuliahList = $user->mataKuliahDiampu()->with('dosen')->get();
        }

        $laporanData = collect();

        foreach ($mataKuliahList as $mk) {
            $totalAbsensi = Absensi::where('mata_kuliah_id', $mk->id)->count();
            $hadir = Absensi::where('mata_kuliah_id', $mk->id)->where('status', 'Hadir')->count();
            $izin = Absensi::where('mata_kuliah_id', $mk->id)->where('status', 'Izin')->count();
            $sakit = Absensi::where('mata_kuliah_id', $mk->id)->where('status', 'Sakit')->count();
            $alpa = Absensi::where('mata_kuliah_id', $mk->id)->where('status', 'Alpa')->count();
            $totalMahasiswa = $mk->mahasiswa()->count();
            $persentase = $totalAbsensi > 0 ? round(($hadir / $totalAbsensi) * 100, 1) : 0;

            $laporanData->push((object)[
                'mata_kuliah' => $mk,
                'total_mahasiswa' => $totalMahasiswa,
                'total_absensi' => $totalAbsensi,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => $persentase,
            ]);
        }

        return view('dosen.laporan', compact('laporanData'));
    }
}
