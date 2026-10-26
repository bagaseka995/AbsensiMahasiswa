<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\MataKuliah;
use App\Models\SesiAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MahasiswaController extends Controller
{
    /**
     * Dashboard Mahasiswa
     * Menampilkan ringkasan: jumlah MK, jadwal hari ini, statistik kehadiran
     */
    public function dashboard()
    {
        $user = Auth::user();
        $mataKuliah = $user->mataKuliah()->with('dosen')->get();

        // Hitung statistik kehadiran
        $totalAbsensi = $user->absensi()->count();
        $hadir = $user->absensi()->where('status', 'Hadir')->count();
        $izin = $user->absensi()->where('status', 'Izin')->count();
        $sakit = $user->absensi()->where('status', 'Sakit')->count();
        $alpa = $user->absensi()->where('status', 'Alpa')->count();
        $persentaseHadir = $totalAbsensi > 0 ? round(($hadir / $totalAbsensi) * 100, 1) : 0;

        // Jadwal hari ini
        $hariIni = $this->getHariIndonesia(now()->dayOfWeek);
        $jadwalHariIni = $mataKuliah->where('hari', $hariIni);

        // Ambil sesi aktif hari ini untuk mata kuliah yang diikuti
        $mataKuliahIds = $mataKuliah->pluck('id');
        $sesiAktifHariIni = SesiAbsensi::where('is_active', true)
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->with('mataKuliah')
            ->get();

        return view('mahasiswa.dashboard', compact(
            'user', 'mataKuliah', 'totalAbsensi',
            'hadir', 'izin', 'sakit', 'alpa',
            'persentaseHadir', 'jadwalHariIni', 'hariIni', 'sesiAktifHariIni'
        ));
    }

    /**
     * Halaman jadwal mata kuliah
     */
    public function jadwal()
    {
        $user = Auth::user();
        $mataKuliah = $user->mataKuliah()->with('dosen')->get();
        $hariUrut = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = $mataKuliah->groupBy('hari')->sortBy(function ($items, $key) use ($hariUrut) {
            return array_search($key, $hariUrut);
        });

        return view('mahasiswa.jadwal', compact('jadwalPerHari', 'hariUrut'));
    }

    /**
     * Halaman absensi mahasiswa
     * Menampilkan mata kuliah yang sedang aktif dibuka pertemuannya oleh dosen
     */
    public function absensi()
    {
        $user = Auth::user();
        $mataKuliah = $user->mataKuliah()->with('dosen')->get();

        // Ambil pertemuan/sesi aktif yang dibuka dosen untuk mata kuliah mahasiswa
        $mataKuliahIds = $mataKuliah->pluck('id');
        $sesiAktif = SesiAbsensi::where('is_active', true)
            ->whereIn('mata_kuliah_id', $mataKuliahIds)
            ->with(['mataKuliah.dosen'])
            ->get();

        // Cek sesi mana yang sudah diabsen oleh mahasiswa ini
        $sudahAbsenSesiIds = Absensi::where('user_id', $user->id)
            ->whereNotNull('sesi_absensi_id')
            ->pluck('sesi_absensi_id')
            ->toArray();

        // Ambil riwayat absensi terbaru
        $riwayatAbsensi = $user->absensi()
            ->with(['mataKuliah', 'sesiAbsensi'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->take(20)
            ->get();

        return view('mahasiswa.absensi', compact('mataKuliah', 'riwayatAbsensi', 'sesiAktif', 'sudahAbsenSesiIds'));
    }

    /**
     * Proses input absensi mahasiswa tanpa kode
     * Memerlukan bukti foto jika status Izin atau Sakit
     */
    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'sesi_absensi_id' => 'required|exists:sesi_absensi,id',
            'status' => 'required|in:Hadir,Izin,Sakit',
            'keterangan' => 'nullable|string|max:255',
            'bukti_foto' => 'required_if:status,Izin,Sakit|nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'sesi_absensi_id.required' => 'Silakan pilih pertemuan mata kuliah yang sedang aktif.',
            'status.required' => 'Silakan pilih status kehadiran Anda.',
            'bukti_foto.required_if' => 'Untuk status Izin atau Sakit, Anda wajib melampirkan bukti foto surat dokter atau surat izin.',
            'bukti_foto.image' => 'File bukti harus berupa gambar (JPG, PNG, atau WEBP).',
            'bukti_foto.max' => 'Ukuran file bukti foto maksimal 5 MB.',
        ]);

        $user = Auth::user();

        // Cari sesi absensi aktif yang dibuka oleh dosen
        $sesi = SesiAbsensi::with('mataKuliah')->findOrFail($request->sesi_absensi_id);

        if (!$sesi->is_active) {
            return back()->with('error', 'Sesi pertemuan untuk mata kuliah ini sudah ditutup oleh dosen.');
        }

        // Cek apakah mahasiswa terdaftar di mata kuliah ini
        if (!$user->mataKuliah()->where('mata_kuliah_id', $sesi->mata_kuliah_id)->exists()) {
            return back()->with('error', 'Anda tidak terdaftar di mata kuliah ini.');
        }

        // Cek apakah mahasiswa sudah melakukan absensi untuk pertemuan ini
        $sudahAbsen = Absensi::where('user_id', $user->id)
            ->where(function ($query) use ($sesi) {
                $query->where('sesi_absensi_id', $sesi->id)
                    ->orWhere(function ($q) use ($sesi) {
                        $q->where('mata_kuliah_id', $sesi->mata_kuliah_id)
                            ->where('pertemuan_ke', $sesi->pertemuan_ke)
                            ->whereDate('tanggal', $sesi->tanggal);
                    });
            })
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Anda sudah melakukan absensi untuk pertemuan ke-' . $sesi->pertemuan_ke . ' ini.');
        }

        // Handle upload file bukti foto untuk Izin atau Sakit
        $fotoPath = null;
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $fileName = 'bukti_' . ($user->nim ?? $user->id) . '_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destinationDir = public_path('uploads/bukti_absensi');

            if (!file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            $file->move($destinationDir, $fileName);
            $fotoPath = 'uploads/bukti_absensi/' . $fileName;
        }

        // Simpan absensi
        Absensi::create([
            'user_id' => $user->id,
            'mata_kuliah_id' => $sesi->mata_kuliah_id,
            'sesi_absensi_id' => $sesi->id,
            'pertemuan_ke' => $sesi->pertemuan_ke,
            'tanggal' => now()->toDateString(),
            'waktu' => now()->toTimeString(),
            'status' => $request->status,
            'bukti_foto' => $fotoPath,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Absensi berhasil dicatat! Status: ' . $request->status . ' untuk ' . $sesi->mataKuliah->nama_mk . ' (Pertemuan ke-' . $sesi->pertemuan_ke . ').');
    }

    /**
     * Konversi day of week ke nama hari Indonesia
     */
    private function getHariIndonesia(int $dayOfWeek): string
    {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $hari[$dayOfWeek];
    }
}
