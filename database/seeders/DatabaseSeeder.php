<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ====== ADMIN ======
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nim' => null,
            'kelas' => null,
            'program_studi' => null,
        ]);

        // ====== DOSEN ======
        $dosen1 = User::create([
            'name' => 'Dr. Ahmad Fauzi, M.Kom.',
            'email' => 'dosen@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim' => null,
            'kelas' => null,
            'program_studi' => 'Teknik Informatika',
        ]);

        $dosen2 = User::create([
            'name' => 'Prof. Siti Nurhaliza, M.Sc.',
            'email' => 'siti@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim' => null,
            'kelas' => null,
            'program_studi' => 'Teknik Informatika',
        ]);

        // ====== MAHASISWA ======
        $mahasiswa = [];
        $dataMahasiswa = [
            ['name' => 'Budi Santoso', 'email' => 'budi@student.ac.id', 'nim' => '2024001', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Sari Dewi', 'email' => 'sari@student.ac.id', 'nim' => '2024002', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Rudi Hartono', 'email' => 'rudi@student.ac.id', 'nim' => '2024003', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Ani Rahayu', 'email' => 'ani@student.ac.id', 'nim' => '2024004', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Dedi Prasetyo', 'email' => 'dedi@student.ac.id', 'nim' => '2024005', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Maya Sari', 'email' => 'maya@student.ac.id', 'nim' => '2024006', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Aditya Pratama', 'email' => 'aditya@student.ac.id', 'nim' => '2024007', 'kelas' => 'TI-B', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Bella Safitri', 'email' => 'bella@student.ac.id', 'nim' => '2024008', 'kelas' => 'TI-B', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Dimas Anggara', 'email' => 'dimas@student.ac.id', 'nim' => '2024009', 'kelas' => 'TI-B', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Eka Putri Handayani', 'email' => 'eka@student.ac.id', 'nim' => '2024010', 'kelas' => 'TI-B', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Fajar Ramadhan', 'email' => 'fajar@student.ac.id', 'nim' => '2024011', 'kelas' => 'TI-B', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Gita Gutawa', 'email' => 'gita@student.ac.id', 'nim' => '2024012', 'kelas' => 'TI-B', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Hendra Setiawan', 'email' => 'hendra@student.ac.id', 'nim' => '2024013', 'kelas' => 'TI-C', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Indah Permatasari', 'email' => 'indah@student.ac.id', 'nim' => '2024014', 'kelas' => 'TI-C', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Joko Widodo', 'email' => 'joko@student.ac.id', 'nim' => '2024015', 'kelas' => 'TI-C', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Kartika Cahyani', 'email' => 'kartika@student.ac.id', 'nim' => '2024016', 'kelas' => 'TI-C', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Lukman Hakim', 'email' => 'lukman@student.ac.id', 'nim' => '2024017', 'kelas' => 'TI-C', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Nadya Utami', 'email' => 'nadya@student.ac.id', 'nim' => '2024018', 'kelas' => 'TI-C', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Reza Fahlevi', 'email' => 'reza@student.ac.id', 'nim' => '2024019', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
            ['name' => 'Zahra Amelia', 'email' => 'zahra@student.ac.id', 'nim' => '2024020', 'kelas' => 'TI-A', 'program_studi' => 'Teknik Informatika'],
        ];

        foreach ($dataMahasiswa as $data) {
            $mahasiswa[] = User::create(array_merge($data, [
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
            ]));
        }

        // ====== MATA KULIAH ======
        $mk1 = MataKuliah::create([
            'kode_mk' => 'IF101',
            'nama_mk' => 'Pemrograman Web',
            'dosen_id' => $dosen1->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruangan' => 'Lab Komputer 1',
            'sks' => 3,
        ]);

        $mk2 = MataKuliah::create([
            'kode_mk' => 'IF102',
            'nama_mk' => 'Basis Data',
            'dosen_id' => $dosen1->id,
            'hari' => 'Selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'ruangan' => 'Ruang 201',
            'sks' => 3,
        ]);

        $mk3 = MataKuliah::create([
            'kode_mk' => 'IF103',
            'nama_mk' => 'Algoritma & Struktur Data',
            'dosen_id' => $dosen2->id,
            'hari' => 'Rabu',
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'ruangan' => 'Ruang 302',
            'sks' => 3,
        ]);

        $mk4 = MataKuliah::create([
            'kode_mk' => 'IF104',
            'nama_mk' => 'Jaringan Komputer',
            'dosen_id' => $dosen2->id,
            'hari' => 'Kamis',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruangan' => 'Lab Jaringan',
            'sks' => 2,
        ]);

        $mk5 = MataKuliah::create([
            'kode_mk' => 'IF105',
            'nama_mk' => 'Rekayasa Perangkat Lunak',
            'dosen_id' => $dosen1->id,
            'hari' => 'Jumat',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruangan' => 'Ruang 101',
            'sks' => 3,
        ]);

        $allMK = [$mk1, $mk2, $mk3, $mk4, $mk5];

        // ====== DAFTARKAN MAHASISWA KE MATA KULIAH ======
        foreach ($mahasiswa as $mhs) {
            // Setiap mahasiswa ikut 3-5 mata kuliah
            $selectedMK = collect($allMK)->random(rand(3, 5));
            foreach ($selectedMK as $mk) {
                $mhs->mataKuliah()->attach($mk->id);
            }
        }

        // Catatan: Data Absensi dibiarkan kosong agar mulai bersih dari nol.
    }
}
