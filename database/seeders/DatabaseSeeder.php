<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
                // TODO 8: Data tambahan Modul 4
        // Mata kuliah tambahan dengan semester dan SKS berbeda
        $if204 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Basis Data',
                'deskripsi' => 'Perancangan dan manajemen basis data.',
                'sks' => 3,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $if402 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Rekayasa Perangkat Lunak',
                'deskripsi' => 'Proses dan metodologi pengembangan perangkat lunak.',
                'sks' => 2,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // Mahasiswa tambahan
        $siti = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // Daftarkan mahasiswa ke kelas (status approved)
        $course1->students()->syncWithoutDetaching([
            $siti->id => ['status' => 'approved'],
        ]);
        $if204->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // Tugas perkuliahan
        $tugas1 = Assignment::forceCreate([
            'course_id' => $course1->id,
            'judul' => 'Tugas 1: Implementasi Middleware & Eloquent ORM',
            'deskripsi' => 'Jelaskan cara kerja Route Middleware untuk proteksi peran pengguna serta implementasi relasi Eloquent pada studi kasus CeLOE LMS.',
            'deadline' => now()->addDays(5),
        ]);

        $tugas2 = Assignment::forceCreate([
            'course_id' => $course1->id,
            'judul' => 'Tugas 2: CRUD Laravel',
            'deskripsi' => 'Buat aplikasi CRUD sederhana menggunakan Laravel.',
            'deadline' => now()->addDays(10),
        ]);

        $tugas3 = Assignment::forceCreate([
            'course_id' => $if204->id,
            'judul' => 'Tugas 1: Normalisasi',
            'deskripsi' => 'Lakukan normalisasi tabel sampai 3NF.',
            'deadline' => now()->addDays(10),
        ]);

        // Contoh pengumpulan tugas (sebagian sudah dinilai, sebagian belum)
        Submission::forceCreate([
            'assignment_id' => $tugas1->id,
            'user_id' => $mahasiswa1->id,
            'jawaban' => 'Middleware di Laravel berfungsi menyaring HTTP request sebelum mencapai controller. Pada CeLOE LMS, RoleMiddleware memeriksa apakah role pengguna sesuai dengan parameter route.',
            'nilai' => 80,
            'catatan_dosen' => 'Penjelasan sangat terstruktur dan tepat sasaran. Pertahankan!',
            'submitted_at' => now()->subDays(2),
        ]);

        Submission::forceCreate([
            'assignment_id' => $tugas1->id,
            'user_id' => $siti->id,
            'jawaban' => 'Relasi Eloquent menghubungkan model, misalnya MataKuliah hasMany Assignment dan Assignment belongsTo MataKuliah, sehingga data terkait dapat diakses lewat properti relasi.',
            'nilai' => 90,
            'catatan_dosen' => 'Pemahaman relasi sangat baik.',
            'submitted_at' => now()->subDay(),
        ]);

        Submission::forceCreate([
            'assignment_id' => $tugas2->id,
            'user_id' => $mahasiswa1->id,
            'jawaban' => 'Aplikasi CRUD sudah selesai dengan fitur tambah, ubah, dan hapus data.',
            'nilai' => 65,
            'catatan_dosen' => 'Lengkapi validasi input dan rapikan struktur kode.',
            'submitted_at' => now()->subDay(),
        ]);
    }
}