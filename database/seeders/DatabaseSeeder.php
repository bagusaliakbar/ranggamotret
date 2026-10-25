<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Katalog;
use App\Models\Paket;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\Invoice;
use App\Models\Kursus;
use App\Models\Modul;
use App\Models\Materi;
use App\Models\Quiz;
use App\Models\QuizSoal;
use App\Models\Sertifikat;
use App\Models\Portofolio;
use App\Models\GaleriFoto;
use App\Models\Konsultasi;
use App\Models\Notifikasi;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Accounts for each Role (Owner & Pelanggan)
        $admin = User::create([
            'name' => 'Rangga Super Admin (Owner)',
            'email' => 'admin@ranggamotret.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'phone' => '081234567890',
            'address' => 'Jl. Studio Fotografi No. 1, Jakarta South',
        ]);

        $customer1 = User::create([
            'name' => 'Anisa Rahmawati',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
            'phone' => '085711223344',
            'address' => 'Kebayoran Baru, Jakarta Selatan',
        ]);

        $peserta1 = User::create([
            'name' => 'Rian Hidayat',
            'email' => 'peserta@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'peserta',
            'status' => 'active',
            'phone' => '087855667788',
            'address' => 'Dago, Bandung',
        ]);

        // 2. Seed Photography Services (Katalog)
        $layananWedding = Katalog::create([
            'nama' => 'Wedding Photography',
            'kategori' => 'Wedding',
            'deskripsi' => 'Abadikan momen bahagia pernikahan Anda dengan sentuhan sinematik dan timeless dari tim fotografer profesional RanggaMotret.',
            'harga_mulai' => 4500000,
            'thumbnail' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        $layananPrewedding = Katalog::create([
            'nama' => 'Prewedding Photography',
            'kategori' => 'Prewedding',
            'deskripsi' => 'Sesi foto prewedding dengan konsep romantis baik indoor studio maupun outdoor lokasi sesuai tempat impian Anda.',
            'harga_mulai' => 2500000,
            'thumbnail' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        $layananGraduation = Katalog::create([
            'nama' => 'Graduation Photography',
            'kategori' => 'Graduation',
            'deskripsi' => 'Dokumentasi wisuda eksklusif individu maupun keluarga untuk merayakan pencapaian akademik berharga.',
            'harga_mulai' => 850000,
            'thumbnail' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        $layananPortrait = Katalog::create([
            'nama' => 'Personal & Studio Portrait',
            'kategori' => 'Portrait',
            'deskripsi' => 'Foto portrait profesional untuk personal branding, model portfolio, maupun headshot studio.',
            'harga_mulai' => 750000,
            'thumbnail' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        $layananProduct = Katalog::create([
            'nama' => 'Product Photography',
            'kategori' => 'Product',
            'deskripsi' => 'Foto produk komersial resolusi tinggi untuk e-commerce, katalog marketplace, dan materi iklan sosial media.',
            'harga_mulai' => 1200000,
            'thumbnail' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        // 3. Seed Packages (Paket)
        $paketWeddingSilver = Paket::create([
            'layanan_id' => $layananWedding->id,
            'nama_paket' => 'Wedding Package Silver',
            'harga' => 4500000,
            'durasi' => '6 Jam',
            'jumlah_fotografer' => 1,
            'jumlah_foto' => 100,
            'fasilitas' => '1 Main Photographer, 100 Edited Photos, All RAW Files in USB, Flashdisk 16GB, 1 Album Photo 20x30 High Quality',
            'deskripsi' => 'Cocok untuk pesta pernikahan intim skala kecil atau akad nikah.',
            'status' => 'aktif',
        ]);

        $paketWeddingGold = Paket::create([
            'layanan_id' => $layananWedding->id,
            'nama_paket' => 'Wedding Package Gold Cinematic',
            'harga' => 7800000,
            'durasi' => 'Full Day (12 Jam)',
            'jumlah_fotografer' => 2,
            'jumlah_foto' => 250,
            'fasilitas' => '2 Photographers, 1 Videographer Cinematic, 250 Edited Photos, 1 Teaser Video 1 Min, 1 Full Film Video 15 Min, Magnetic Box Album 30x40',
            'deskripsi' => 'Paket dokumentasi pernikahan lengkap akad + resepsi malam hari.',
            'status' => 'aktif',
        ]);

        $paketPreweddingGold = Paket::create([
            'layanan_id' => $layananPrewedding->id,
            'nama_paket' => 'Prewedding Outdoor Scenic',
            'harga' => 2500000,
            'durasi' => '5 Jam',
            'jumlah_fotografer' => 1,
            'jumlah_foto' => 50,
            'fasilitas' => '1 Senior Photographer, 50 Retouched Photos, 2 Printed Frames 40x60cm, Professional MUA Included',
            'deskripsi' => 'Termasuk Make-Up Artist dan 2 cetakan bingkai foto besar.',
            'status' => 'aktif',
        ]);

        // 4. Seed Booking & Payments & Invoices (Reservasi)
        $booking1 = Reservasi::create([
            'nomor_reservasi' => 'RSV-' . date('Ymd') . '-001',
            'tipe' => 'fotografi',
            'user_id' => $customer1->id,
            'layanan_id' => $layananWedding->id,
            'paket_id' => $paketWeddingGold->id,
            'tanggal' => Carbon::now()->addDays(14)->format('Y-m-d'),
            'waktu' => '09:00:00',
            'lokasi' => 'Grand Ballroom Hotel Mulia, Senayan, Jakarta',
            'kebutuhan_khusus' => 'Fokus pada adat Jawa Solo Putri dan momen haru orang tua.',
            'status' => 'Dibayar',
        ]);

        $pembayaran1 = Pembayaran::create([
            'nomor_transaksi' => 'TRX-' . date('Ymd') . '-001',
            'user_id' => $customer1->id,
            'reservasi_id' => $booking1->id,
            'jumlah' => 7800000,
            'metode_pembayaran' => 'Midtrans Snap (BCA Virtual Account)',
            'tanggal_pembayaran' => Carbon::now()->subDays(1),
            'catatan' => 'Pembayaran lunas via Midtrans Snap',
            'status' => 'Dibayar',
        ]);

        Invoice::create([
            'nomor_invoice' => 'INV-' . date('Ymd') . '-001',
            'pembayaran_id' => $pembayaran1->id,
            'user_id' => $customer1->id,
            'reservasi_id' => $booking1->id,
            'subtotal' => 7800000,
            'diskon' => 0,
            'total' => 7800000,
            'status_pembayaran' => 'LUNAS',
        ]);

        // Proofing photos for booking1
        GaleriFoto::create([
            'reservasi_id' => $booking1->id,
            'foto_path' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
            'is_selected' => true,
            'catatan' => 'Dipilih untuk cetak album utama',
        ]);
        GaleriFoto::create([
            'reservasi_id' => $booking1->id,
            'foto_path' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80',
            'is_selected' => false,
            'catatan' => null,
        ]);

        // 5. Seed Courses (Kursus - Diampu langsung oleh Owner)
        $kursusDasar = Kursus::create([
            'nama' => 'Mastery Fotografi Dasar & Teknik Segitiga Exposure',
            'deskripsi' => 'Pelajari dasar-dasar ilmu fotografi dari nol. Menguasai ISO, Aperture, Shutter Speed, White Balance, dan komposisi visual terbaik.',
            'harga' => 750000,
            'durasi' => '4 Minggu',
            'level' => 'Pemula',
            'instruktur_id' => $admin->id,
            'thumbnail' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        $kursusPortrait = Kursus::create([
            'nama' => 'Portrait Photography & Studio Lighting Masterclass',
            'deskripsi' => 'Kuasai pencahayaan studio (Flash, Continuous, Softbox) dan posing model profesional untuk hasil portrait menakjubkan.',
            'harga' => 1250000,
            'durasi' => '6 Minggu',
            'level' => 'Menengah',
            'instruktur_id' => $admin->id,
            'thumbnail' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
            'status' => 'aktif',
        ]);

        // Modules for Kursus Dasar
        $modul1 = Modul::create([
            'kursus_id' => $kursusDasar->id,
            'judul' => 'Modul 1: Pengenalan Anatomi Kamera & Sensor',
            'urutan' => 1,
            'deskripsi' => 'Mengenal perbedaan DSLR vs Mirrorless, ukuran sensor Fullframe vs APS-C, dan fungsi tombol-tombol utama.',
        ]);

        $modul2 = Modul::create([
            'kursus_id' => $kursusDasar->id,
            'judul' => 'Modul 2: Rahasia Segitiga Exposure (Aperture, Shutter, ISO)',
            'urutan' => 2,
            'deskripsi' => 'Formula praktis menyeimbangkan tiga elemen cahaya untuk menciptakan bokeh indah atau membekukan gerakan cepat.',
        ]);

        // Materials for Modul 1
        Materi::create([
            'modul_id' => $modul1->id,
            'judul' => 'Panduan Komprehensif Jenis Kamera & Lensa',
            'tipe' => 'teks',
            'isi' => 'Kamera modern terbagi menjadi sistem DSLR dan Mirrorless. Pada materi ini Anda akan memahami karakteristik sistem optic, focal length (mm), dan aperture maksimum f-stop.',
            'urutan' => 1,
        ]);

        Materi::create([
            'modul_id' => $modul1->id,
            'judul' => 'Video Tutorial: Setting Kamera Mode Manual dari Nol',
            'tipe' => 'video',
            'video_url' => 'https://www.youtube.com/embed/V7z7BAZdt2M',
            'isi' => 'Tonton video di atas untuk melihat demonstrasi pengoperasian menu kamera secara langsung.',
            'urutan' => 2,
        ]);

        // Quiz for Modul 2
        $quiz1 = Quiz::create([
            'modul_id' => $modul2->id,
            'judul' => 'Quiz Evaluasi Segitiga Exposure',
            'durasi_menit' => 15,
            'passing_grade' => 70,
        ]);

        QuizSoal::create([
            'quiz_id' => $quiz1->id,
            'pertanyaan' => 'Manakah angka Aperture (f-stop) di bawah ini yang menghasilkan efek ruang tajam terluas (Depth of Field paling dalam)?',
            'pilihan_a' => 'f/1.4',
            'pilihan_b' => 'f/2.8',
            'pilihan_c' => 'f/5.6',
            'pilihan_d' => 'f/16',
            'jawaban_benar' => 'D',
            'pembahasan' => 'Semakin besar nilai f-stop (misal f/16), bukaan diafragma semakin sempit sehingga seluruh area depan hingga latar belakang menjadi tajam.',
            'bobot_nilai' => 50,
        ]);

        QuizSoal::create([
            'quiz_id' => $quiz1->id,
            'pertanyaan' => 'Jika Anda ingin membekukan gerakan tarian tradisional yang sangat cepat tanpa blur, setting Shutter Speed manakah yang paling cocok?',
            'pilihan_a' => '1/10 detik',
            'pilihan_b' => '1/30 detik',
            'pilihan_c' => '1/1000 detik',
            'pilihan_d' => '2 detik',
            'jawaban_benar' => 'C',
            'pembahasan' => 'Shutter speed sangat cepat seperti 1/1000 detik mampu membekukan aksi super cepat secara tajam.',
            'bobot_nilai' => 50,
        ]);

        // Pendaftaran Peserta1 di KursusDasar via Reservasi tipe kursus
        $reservasiKursus = Reservasi::create([
            'nomor_reservasi' => 'RSV-' . date('Ymd') . '-KRS01',
            'tipe' => 'kursus',
            'user_id' => $peserta1->id,
            'kursus_id' => $kursusDasar->id,
            'tanggal' => Carbon::now()->subDays(10)->format('Y-m-d'),
            'lokasi' => 'Online Classroom LMS',
            'status' => 'Dibayar',
        ]);

        $pembayaranKursus = Pembayaran::create([
            'nomor_transaksi' => 'TRX-KRS-' . $kursusDasar->id . '-' . $peserta1->id,
            'user_id' => $peserta1->id,
            'reservasi_id' => $reservasiKursus->id,
            'kursus_id' => $kursusDasar->id,
            'jumlah' => $kursusDasar->harga,
            'metode_pembayaran' => 'Midtrans Snap (Gateway)',
            'tanggal_pembayaran' => Carbon::now()->subDays(10),
            'status' => 'Dibayar',
        ]);

        Invoice::create([
            'nomor_invoice' => 'INV-KRS-' . date('Ymd') . '-001',
            'pembayaran_id' => $pembayaranKursus->id,
            'user_id' => $peserta1->id,
            'reservasi_id' => $reservasiKursus->id,
            'kursus_id' => $kursusDasar->id,
            'subtotal' => $kursusDasar->harga,
            'diskon' => 0,
            'total' => $kursusDasar->harga,
            'status_pembayaran' => 'LUNAS',
        ]);

        // Certificate sample
        Sertifikat::create([
            'user_id' => $peserta1->id,
            'kursus_id' => $kursusDasar->id,
            'nomor_sertifikat' => 'CERT-RM-' . date('Y') . '-0089',
            'kode_verifikasi' => 'VERIFY-RM-998822',
            'tanggal_terbit' => Carbon::now()->format('Y-m-d'),
            'nilai_akhir' => 92.50,
        ]);

        // 6. Seed Portfolios
        Portofolio::create([
            'judul' => 'Symphony of Love - Royal Javanese Wedding',
            'kategori' => 'Wedding',
            'foto' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
            'deskripsi' => 'Momen abadi kemewahan akad nikah bernuansa emas dan hijau mint di Jakarta.',
            'tanggal' => Carbon::now()->subMonths(2),
            'status_publikasi' => 'published',
        ]);

        Portofolio::create([
            'judul' => 'Golden Hour Sunset in Pine Forest',
            'kategori' => 'Prewedding',
            'foto' => 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80',
            'deskripsi' => 'Sesi foto hangat bertema nuansa alam pegunungan di Lembang Bandung.',
            'tanggal' => Carbon::now()->subMonths(1),
            'status_publikasi' => 'published',
        ]);

        Portofolio::create([
            'judul' => 'High-Fashion Studio Editorial Portrait',
            'kategori' => 'Portrait',
            'foto' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=800&q=80',
            'deskripsi' => 'PenggunaanRembrandt lighting untuk menonjolkan ekspresi tegas dan elegan.',
            'tanggal' => Carbon::now()->subWeeks(3),
            'status_publikasi' => 'published',
        ]);

        // 7. Seed Consultations
        Konsultasi::create([
            'user_id' => $customer1->id,
            'nama' => 'Anisa Rahmawati',
            'email' => 'customer@gmail.com',
            'phone' => '085711223344',
            'jenis_layanan' => 'Wedding',
            'konsep' => 'Adat Jawa Modern Sinematik',
            'lokasi' => 'Hotel Mulia Senayan',
            'tanggal' => Carbon::now()->addDays(14),
            'budget' => 8000000,
            'kebutuhan' => 'Mohon rekomendasi paket terbaik beserta videografer teaser 1 menit.',
            'tanggapan_admin' => 'Halo Kak Anisa! Kami merekomendasikan Paket Wedding Gold Cinematic yang sudah mencakup 2 Fotografer + Videografer Teaser.',
            'status' => 'replied',
        ]);

        // 8. Seed Notifikasi Sistem
        Notifikasi::create([
            'user_id' => $customer1->id,
            'judul' => 'Pembayaran Dikirim & Disetujui',
            'pesan' => 'Pembayaran untuk Booking BK-' . date('Ymd') . '-001 telah berhasil diverifikasi oleh admin.',
            'link' => '/customer/bookings',
            'is_read' => false,
        ]);

        Notifikasi::create([
            'user_id' => $peserta1->id,
            'judul' => 'Sertifikat Kelulusan Terbit!',
            'pesan' => 'Selamat! Sertifikat untuk kursus "Mastery Fotografi Dasar" telah diterbitkan.',
            'link' => '/student/certificates',
            'is_read' => false,
        ]);
    }
}
