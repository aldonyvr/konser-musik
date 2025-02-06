<?php

namespace Database\Seeders;

use App\Models\Konser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KonserSeeder extends Seeder
{
    public function run()
    {
        Konser::create([
            'title' => 'Arditho Pramono',
            'tanggal' => '2025-12-14',
            'lokasi' => 'YOGYAKARTA',
            'jam' => '10.00',
            'tiket_tersedia' => '150',
            'image' => '/media/musik/ardhito.png',
            // 'kontak' => '0821173612731',
            'deskripsi' => 'Ardhito Pramono Live in Yogyakarta
Nikmati malam penuh keajaiban musik dengan penampilan langsung dari penyanyi dan penulis lagu kenamaan Indonesia, Ardhito Pramono! Konser ini akan berlangsung pada 14 Desember 2024, bertempat di Yogyakarta, salah satu kota budaya yang memancarkan pesona unik.

Dalam konser ini, Ardhito akan membawakan lagu-lagu hits dari album-album populer seperti "Bitterlove", "Cigarettes of Ours", dan "Fine Today". Tidak hanya itu, ia juga akan mempersembahkan beberapa lagu yang jarang dibawakan di panggung, memberikan pengalaman eksklusif untuk para penggemar setianya.

Konser ini dirancang dengan konsep intim namun tetap megah, menciptakan suasana yang hangat dan mendalam. Anda akan disuguhkan dengan tata lampu spektakuler dan visual artistik yang mendukung keindahan musikalitas Ardhito terjangkau, hanya Rp 50.000, konser ini adalah kesempatan sempurna untuk menikmati musik berkualitas tanpa harus menguras kantong.

Acara akan dimulai tepat pukul 20:00 WIB, jadi pastikan Anda datang lebih awal untuk merasakan atmosfer semarak sebelum pertunjukan dimulai. Lokasi konser dipilih secara strategis dengan fasilitas lengkap seperti area parkir luas, tempat duduk nyaman, dan stand makanan lokal yang akan memanjakan selera Anda selama acara berlangsung.

Dengan jumlah tiket terbatas, hanya 150 kursi yang tersedia, pastikan Anda segera memesan tiket sebelum kehabisan! Konser ini juga didukung oleh pengamanan ketat dan protokol kesehatan yang sesuai untuk memastikan pengalaman Anda tetap aman dan nyaman.

Jangan lewatkan kesempatan langka untuk menyaksikan secara langsung salah satu musisi terbaik Indonesia yang selalu berhasil memukau para penontonnya dengan suara khas dan kepribadian yang hangat. Ayo, ajak teman dan keluarga Anda untuk bersama-sama menciptakan kenangan indah di konser Ardhito Pramono Live in Yogyakarta!',
        ]);
        Konser::create([
            'title' => 'Hindia Music Festival',
            'tanggal' => '2024-11-24',
            'lokasi' => 'SURABAYA',
            'tiket_tersedia' => '150',
            'jam' => '19.00',
            'image' => '/media/musik/baskara.png',
            // 'kontak' => '0821173612731',
            'deskripsi' => 'Rasakan pengalaman musik yang tak terlupakan di Hindia Music Festival, sebuah perayaan musik yang akan mengguncang kota Surabaya pada 24 November 2024! Acara ini akan menampilkan Baskara Putra, musisi yang dikenal dengan proyek solonya, Hindia, yang membawa warna baru dalam dunia musik Indonesia dengan lirik-lirik mendalam dan aransemen musik yang emosional.

Festival ini akan dimulai pukul 19:00 WIB dan berlangsung di lokasi yang strategis di pusat kota  50.000, Anda akan disuguhkan dengan penampilan spektakuler Hindia yang membawakan lagu-lagu hits seperti "Secukupnya", "Membasuh", hingga "Rumah ke Rumah".

Acara ini dirancang untuk menciptakan suasana festival yang meriah dengan panggung megah, tata suara berkualitas tinggi, serta dekorasi visual yang artistik. Anda juga dapat menikmati berbagai aktivitas lain seperti food bazaar, area foto instagenic, dan merchandise eksklusif dari Hindia.

Dengan kapasitas terbatas 150 tiket, jangan lewatkan kesempatan untuk menjadi bagian dari malam magis ini! Pastikan Anda memesan tiket sekarang dan bersiaplah untuk malam penuh kenangan bersama Hindia dan para penggemar lainnya.

',
        ]);
        Konser::create([
            'title' => 'Sal Priadi',
            'tanggal' => '2024-11-29',
            'lokasi' => 'SURABAYA',
            'tiket_tersedia' => '150',
            'jam' => '20.00 ',
            'image' => '/media/musik/sal priadi.png',
            // 'kontak' => '081234273171',
            'deskripsi' => 'Bersiaplah untuk terhanyut dalam alunan suara romantis dari Sal Priadi dalam konser istimewa yang akan digelar di Surabaya pada 29 November  75.000, konser ini akan menjadi salah satu perayaan musik terbaik yang tak boleh Anda lewatkan.

Sal Priadi, yang dikenal dengan lagu-lagunya yang penuh emosi seperti "Amin Paling Serius" dan "Ikat Aku di Tulang Belikatmu", akan membawa Anda ke dunia musik yang penuh kehangatan dan keintiman. Acara dimulai pukul 20:00 WIB dengan lokasi yang nyaman dan menyenangkan untuk semua penonton.

Selain penampilan utama, Anda juga dapat menikmati berbagai aktivitas menarik di area konser, termasuk bazar makanan dan minuman, serta area santai yang dirancang untuk memberikan pengalaman terbaik. Dengan kapasitas penonton hanya 150 kursi, ini adalah kesempatan langka untuk merasakan konser dengan suasana yang dekat dan personal.',
        ]);
        Konser::create([
            'title' => 'KUNTO AJI',
            'tanggal' => '2024-11-30',
            'lokasi' => 'JAKARTA',
            'jam' => '20.00 ',
            'tiket_tersedia' => '150',
            'image' => '/media/musik/kuntoaji.png',
            // 'kontak' => '085526315261',
            'deskripsi' => 'Siapkan diri Anda untuk malam penuh magis bersama Kunto Aji, salah satu musisi terbaik di Indonesia! Konser ini akan berlangsung pada 30 November 2024 di Jakarta Rp 100.000.

Dalam konser ini, Kunto Aji akan membawakan lagu-lagu inspiratifnya yang telah menjadi soundtrack kehidupan banyak orang, seperti "Rehat", "Pilu Membiru", dan "Mantra Mantra". Dengan suara khas dan pesan yang mendalam dalam setiap liriknya, Anda dijamin akan terinspirasi dan terhubung secara emosional sepanjang malam.

Acara ini dirancang dengan konsep modern dan intim, menawarkan tata lampu yang memukau dan panggung yang dirancang untuk membawa penonton lebih dekat dengan Kunto Aji. Dengan kapasitas hanya 150 tiket, pastikan Anda memesan tiket segera untuk menjadi bagian dari momen spesial ini.',
        ]);
        Konser::create([
            'title' => 'Nadin Amizah',
            'tanggal' => '2025-03-10',
            'lokasi' => 'SURABAYA',
            'jam' => '20.00 ',
            'tiket_tersedia' => '150',
            'image' => '/media/musik/nadin.png',
            // 'kontak' => '087327362731',
            'deskripsi' => 'Jangan lewatkan konser Nadin Amizah yang akan digelar di Surabaya pada 10 Januari 2025! Penyanyi dengan suara lembut nan penuh emosi ini siap memukau Anda dengan lagu-lagunya yang indah dan penuh makna.

Konser ini akan menjadi perjalanan musikal yang mendalam, di mana Nadin akan membawakan lagu-lagu hits seperti "Rumpang", "Sorai", dan "Beranjak  60.000, Anda akan mendapatkan pengalaman konser yang luar biasa di lokasi yang nyaman dengan fasilitas lengkap.

Acara ini dimulai pukul 20:00 WIB dan dirancang untuk menciptakan suasana yang hangat dan intim. Nikmati tata panggung yang megah, sound system berkualitas tinggi, dan dekorasi yang akan memperkuat suasana magis dari setiap lagu yang dibawakan Nadin.

',
        ]);

        Konser::create([
            'title' => 'Lana Del Rey',
            'tanggal' => '2025-01-24',
            'lokasi' => 'JAKARTA',
            'jam' => '20.00 ',
            'tiket_tersedia' => '150',
            'image' => '/media/musik/ldr.png',
            // 'kontak' => '08426341232',
            'deskripsi' => 'Penggemar musik di Jakarta bersiaplah untuk malam yang luar biasa bersama Lana Del Rey! Penyanyi dan penulis lagu legendaris asal Amerika ini akan menggelar konser eksklusif pada 24 Januari  65.000, ini adalah kesempatan langka untuk menikmati suara melankolis dan lirik puitis yang telah memikat dunia.

Konser ini akan menampilkan lagu-lagu hits dari Lana Del Rey, seperti "Summertime Sadness", "Young and Beautiful", dan "Born to Die". Dengan panggung megah, tata cahaya artistik, dan aransemen musik live yang memukau, Lana akan membawa penonton ke dunia sinematik yang penuh emosi.

Acara dimulai pukul 20:00 WIB di lokasi eksklusif di Jakarta yang dirancang untuk memberikan pengalaman terbaik bagi para penonton. Dengan kapasitas terbatas 150 tiket, pastikan Anda segera memesan tiket dan merasakan magisnya suara Lana Del Rey secara langsung.',
        ]);
        Konser::create([
            'title' => 'The 1975',
            'tanggal' => '2025-02-15',
            'lokasi' => 'JAKARTA',
            'jam' => '20.00',
            'tiket_tersedia' => '150',
            'image' => '/media/musik/the1975.png',
            // 'kontak' => '08632642731',
            'deskripsi' => 'Siapkan diri Anda untuk malam penuh energi dan kenangan bersama The 1975, salah satu band pop rock terpopuler di dunia! Konser ini akan diadakan di Jakarta pada 15 Februari , hanya Rp 50.000.

Band asal Inggris ini akan membawakan lagu-lagu hits mereka yang dikenal dengan gaya eksperimental dan lirik yang menyentuh, seperti "Somebody Else", "Love It If We Made It", dan "The Sound". Dengan panggung yang spektakuler, visual yang memikat, dan tata suara yang memukau, konser ini akan menjadi pengalaman yang tak terlupakan bagi para penggemar.

Konser dimulai pukul 20:00 WIB di lokasi yang eksklusif dan nyaman untuk penonton. Selain penampilan musik yang epik, Anda juga dapat menikmati area foto, stan merchandise eksklusif, dan berbagai aktivitas menarik lainnya. Dengan hanya 150 tiket yang tersedia, pastikan Anda memesan tiket sekarang untuk menjadi bagian dari momen bersejarah ini.

',
        ]);
        Konser::create([
            'title' => 'Cigarettes after sex',
            'tanggal' => '2025-12-14',
            'lokasi' => 'JAKARTA',
            'jam' => '20.00',
            'tiket_tersedia' => '150',
            'image' => '/media/musik/cas.png',
            // 'kontak' => '086723467236',
            'deskripsi' => 'Nikmati malam penuh atmosfer melankolis dan romantis bersama Cigarettes After Sex, band dream pop asal Amerika yang telah memikat hati banyak penggemar di seluruh dunia. Konser ini akan berlangsung di Jakarta pada 14  50.000.

Band ini dikenal dengan suara lembut dan lirik-lirik yang penuh emosi, menjadikan setiap lagu mereka seperti puisi yang dinyanyikan. Dalam konser ini, mereka akan membawakan hits seperti "Nothing’s Gonna Hurt You Baby", "K.", dan "Apocalypse". Dengan tata suara berkualitas tinggi dan pencahayaan yang menenangkan, pengalaman mendengarkan musik mereka akan terasa begitu intim dan tak terlupakan.

Acara dimulai pukul 20:00 WIB, dan lokasi konser dirancang untuk menciptakan suasana nyaman bagi semua penonton. Dengan kapasitas terbatas 150 tiket, pastikan Anda tidak melewatkan kesempatan untuk menikmati malam magis bersama Cigarettes After Sex.',
        ]);

    }
}
