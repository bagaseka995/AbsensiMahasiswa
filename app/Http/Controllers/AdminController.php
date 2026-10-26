<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Tampilkan daftar seluruh mahasiswa
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'mahasiswa')->with('mataKuliah');

        // Filter pencarian nama / nim / email
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nim', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $mahasiswa = $query->orderBy('name', 'asc')->paginate(15)->withQueryString();
        $daftarMK = MataKuliah::orderBy('nama_mk', 'asc')->get();
        $daftarKelas = User::where('role', 'mahasiswa')
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        $totalMahasiswa = User::where('role', 'mahasiswa')->count();

        return view('admin.mahasiswa.index', compact('mahasiswa', 'daftarMK', 'daftarKelas', 'totalMahasiswa'));
    }

    /**
     * Simpan mahasiswa baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nim' => 'required|string|max:20|unique:users,nim',
            'kelas' => 'required|string|max:50',
            'program_studi' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'mata_kuliah_ids' => 'nullable|array',
            'mata_kuliah_ids.*' => 'exists:mata_kuliah,id',
        ], [
            'email.unique' => 'Email ini sudah terdaftar.',
            'nim.unique' => 'NIM ini sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'kelas' => $validated['kelas'],
            'program_studi' => $validated['program_studi'],
            'role' => 'mahasiswa',
            'password' => Hash::make($request->filled('password') ? $request->password : 'password'),
        ]);

        if (!empty($validated['mata_kuliah_ids'])) {
            $user->mataKuliah()->sync($validated['mata_kuliah_ids']);
        }

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', "Mahasiswa '{$user->name}' (NIM: {$user->nim}) berhasil ditambahkan.");
    }

    /**
     * Perbarui data mahasiswa
     */
    public function update(Request $request, User $mahasiswa)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $mahasiswa->id,
            'nim' => 'required|string|max:20|unique:users,nim,' . $mahasiswa->id,
            'kelas' => 'required|string|max:50',
            'program_studi' => 'required|string|max:100',
            'password' => 'nullable|string|min:6',
            'mata_kuliah_ids' => 'nullable|array',
            'mata_kuliah_ids.*' => 'exists:mata_kuliah,id',
        ], [
            'email.unique' => 'Email ini sudah terdaftar oleh pengguna lain.',
            'nim.unique' => 'NIM ini sudah terdaftar oleh pengguna lain.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim' => $validated['nim'],
            'kelas' => $validated['kelas'],
            'program_studi' => $validated['program_studi'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $mahasiswa->update($updateData);

        if (isset($validated['mata_kuliah_ids'])) {
            $mahasiswa->mataKuliah()->sync($validated['mata_kuliah_ids']);
        } else {
            $mahasiswa->mataKuliah()->detach();
        }

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', "Data mahasiswa '{$mahasiswa->name}' berhasil diperbarui.");
    }

    /**
     * Hapus mahasiswa
     */
    public function destroy(User $mahasiswa)
    {
        $nama = $mahasiswa->name;
        // Hapus relasi mata kuliah dan absensi
        $mahasiswa->mataKuliah()->detach();
        $mahasiswa->absensi()->delete();
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', "Mahasiswa '{$nama}' berhasil dihapus dari sistem.");
    }
}
