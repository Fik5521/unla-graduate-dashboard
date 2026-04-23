<?php

namespace Database\Seeders;

use App\Models\Lulusan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LulusanSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data JSON
        $json = File::get(database_path('seeders/Angkatan 2016-2021.json'));
        $data = json_decode($json, true);

        // Jika JSON dibungkus objek 'data', ambil array-nya
        $listMahasiswa = isset($data['data']) ? $data['data'] : $data;

        foreach ($listMahasiswa as $item) {
            // Ambil tahun masuk dari field 'angkatan'
            $tahunMasuk = (int) $item['angkatan'];

            // Ambil tahun lulus dari tanggal keluar
            $tahunLulus = Carbon::parse($item['tanggal_keluar'])->year;

            // --- LOGIC PERHITUNGAN SEMESTER (FIXED) ---
            // Rumus: (Tahun Lulus - Tahun Masuk) * 2
            $lamaStudi = ($tahunLulus - $tahunMasuk) * 2;

            // Penyesuaian Semester Gasal/Genap (Randomize agar data terlihat natural)
            // Karena biasanya lulus di sem 7, 8, 9, dst.
            $lamaStudi = $lamaStudi - rand(0, 1);

            // VALIDASI AKADEMIK SESUAI ATURAN KEMENDIKBUD
            if ($lamaStudi < 7) {
                $lamaStudi = 7;
            }
            if ($lamaStudi > 14) {
                $lamaStudi = 14;
            }

            // Simpan ke Database
            Lulusan::updateOrCreate(
                ['nim' => $item['nim']],
                [
                    'nama'        => $item['nama_mahasiswa'],
                    'prodi'       => $item['nama_program_studi'],
                    'fakultas'    => $this->getFakultas($item['nama_program_studi']),
                    'tahun_lulus' => $tahunLulus,
                    'ipk'         => (float) $item['ipk'],
                    'lama_studi'  => $lamaStudi, // Pakai variabel yang sudah divalidasi
                ]
            );
        }
    }

    // Helper sederhana untuk menentukan fakultas berdasarkan prodi
    private function getFakultas($prodi)
    {
        $prodi = strtolower($prodi);

        // 1. Pascasarjana (S2 & S3)
        if (str_contains($prodi, 'magister') || str_contains($prodi, 'doktor')) {
            return 'Pascasarjana';
        }

        // 2. Fakultas Teknik
        if (
            str_contains($prodi, 'informatika') || str_contains($prodi, 'industri') ||
            str_contains($prodi, 'sipil') || str_contains($prodi, 'elektro') ||
            str_contains($prodi, 'arsitektur')
        ) {
            return 'Fakultas Teknik';
        }

        // 3. Fakultas Ekonomi dan Bisnis
        if (
            str_contains($prodi, 'manajemen') || str_contains($prodi, 'akuntansi') ||
            str_contains($prodi, 'bisnis digital')
        ) {
            return 'Fakultas Ekonomi & Bisnis';
        }

        // 4. FISIP
        if (
            str_contains($prodi, 'pemerintahan') || str_contains($prodi, 'komunikasi') ||
            str_contains($prodi, 'kesejahteraan sosial') || str_contains($prodi, 'mkkp') ||
            str_contains($prodi, 'kepolisian')
        ) {
            return 'FISIP';
        }

        // 5. Fakultas Hukum
        if (str_contains($prodi, 'hukum')) {
            return 'Fakultas Hukum';
        }

        // 6. FKIP
        if (
            str_contains($prodi, 'pgsd') || str_contains($prodi, 'sekolah dasar') || str_contains($prodi, 'matematika') ||
            str_contains($prodi, 'pendidikan ekonomi')
        ) {
            return 'FKIP';
        }

        return 'Lainnya';
    }
}
