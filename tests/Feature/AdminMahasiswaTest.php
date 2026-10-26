<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MataKuliah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;
use Tests\TestCase;

class AdminMahasiswaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }
    public function test_admin_can_view_mahasiswa_page(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/mahasiswa');
        $response->assertStatus(200);
        $response->assertSee('Kelola Data Mahasiswa');
    }

    public function test_admin_can_create_new_mahasiswa(): void
    {
        $admin = User::where('role', 'admin')->first();
        $mk = MataKuliah::first();

        $payload = [
            'name' => 'Testing Mahasiswa Baru',
            'email' => 'testingbaru@student.ac.id',
            'nim' => '2024999',
            'kelas' => 'TI-Z',
            'program_studi' => 'Teknik Informatika',
            'password' => 'password',
            'mata_kuliah_ids' => [$mk->id],
        ];

        $response = $this->actingAs($admin)->post('/admin/mahasiswa', $payload);
        $response->assertRedirect('/admin/mahasiswa');

        $this->assertDatabaseHas('users', [
            'email' => 'testingbaru@student.ac.id',
            'nim' => '2024999',
            'role' => 'mahasiswa',
        ]);
    }
}
