<template>
  <div>
    <div class=" bg-dark">
      <footer class="footer">
        <div class="footer-content">
          <div class="footer-section">
            <NavLink :href="'/'" :active="false" class=" flex row"  style="font-family: 'Lobster', cursive; color: #ffcc00;">
              <div class="shrink-0 flex items-center">
              </div>
                  <div class="text-container">
                    <img src="/media/musik/logo.png" alt="Logo" class="logo w-50">
                  </div>
            </NavLink>
            <p class="text-white mt-2">Temukan konser terbaik dan pengalaman musik yang tidak terlupakan. Bergabunglah dengan kami untuk menikmati penampilan luar biasa dari artis favorit Anda.</p>
          </div>
          <div class="footer-section">
            <h3>Navigation</h3>
            <ul>
              <li><a href="/">Home</a></li>
              <li><a href="/KonserSaya">Konser Saya</a></li>
              <li><a href="/RiwayatTransaksi">Riwayat Transaksi</a></li>
              <li><a href="/Kontak">Kontak</a></li>
            </ul>
          </div>
          <div class="footer-section text-white contact-info">
            <h3>Informasi Kontak</h3>
            <p><i class="fa-solid fa-phone"></i> +62 812-3083-9195</p>
            <p><i class="fa-solid fa-envelope"></i> aldonyvr@gmail.com</p>
            <p><i class="fa-solid fa-map-marker-alt"></i> Jl. Klakah Rejo Gg.8 No.5 </p>
          </div>
          <div class="footer-section text-white">
            <h3>Daftar Konser Anda</h3>
            <div class="flex row">
            <p>Daftar konser terbaru dengan mudah melalui WhatsApp dan dapatkan info terkini!</p>
        
            <a href="https://wa.me/6285198362591" target="_blank" class="whatsapp-link mb-10">
              <i class="fab fa-whatsapp " style="font-size: 50px; color: #32CD32;"></i> 
            </a>
          </div>
          </div>
        </div>
        <div class="footer-bottom text-white">
          <p>Copyright © 2024 All rights reserved | Block is made with ❤️ by Mudassar Chaudhry</p>
          <div class="social-links">
            <!-- <a href="https://wa.me/6285198362591" target="_blank" aria-label="WhatsApp" class="">
              <i class="fab fa-whatsapp text-2xl text-green-500 " ></i>
            </a> -->
            
            <a href="https://wa.me/6285198362591" aria-label="WhatsApp">
              <img src="/media/musik/whatsapp.png" alt="WhatsApp" style="height: 28px; width: 29px;" />
            </a>
            <a href="#" aria-label="Facebook">
              <img src="/media/musik/facebook.png" alt="Facebook" style="height: 30px; width: 31px;" />
            </a>
            <a href="#" aria-label="Twitter">
              <img src="/media/musik/twitter.png" alt="Twitter" style="height: 27px; width: 29px;"/>
            </a>
            <a href="https://www.instagram.com/gkimanukan?igsh=MXBhbGt5c2U4OW5ldA==" aria-label="Instagram">
              <img src="/media/musik/instagram.png" alt="Instagram" style="height: 24px; width: 25px;"/>
            </a>
            <a href="#" aria-label="LinkedIn">
              <img src="/media/musik/linkedin.png" alt="LinkedIn" style="height: 27px; width: 29px;"/>
            </a>
            <a href="https://youtube.com/@gkimanukan?si=CgU5-YpuDYOnGz5u" aria-label="YouTube">
              <img src="/media/musik/youtube.png" alt="YouTube" style="height: 27px; width: 36px;"/>
            </a>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>

</script>

<style scoped>

.contact-info p {
  margin: 10px 0;
}

.contact-info i {
  margin-right: 5px;
}

.footer {
  padding: 40px 20px;
  text-align: left;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}

.footer-section {
  flex: 1;
  margin: 0 20px;
}

.footer-section h2, .footer-section h3 {
  margin-bottom: 10px;
  color: #ffcc00;
}

.footer-section ul {
  list-style-type: none;
  padding: 0;
}

.footer-section ul li {
  margin: 5px 0;
}

.footer-section a {
  text-decoration: none;
  color: #ffcc00;
  transition: color 0.3s;
}

.footer-section a:hover {
  color: #ff9900;
}

.footer-bottom {
  text-align: center;
  /* margin-top: 20px; */
  padding-top: 10px;
  border-top: 1px solid #ffcc00;
}

.social-links a {
  margin: 0 10px;
  display: inline-block;
}

.social-links img {
  width: 24px;
  height: 24px;
  transition: transform 0.3s;
}
.whatsapp-link:hover {
  transition: transform 0.3s;
  transform: scale(1.2);
  width: 20px;
}

.social-links img:hover {
  transform: scale(1.2);
}

.small-text {
    display: block; /* Menempatkan 'MusicOn' di baris baru */
    font-size: 90%; /* Ukuran font lebih kecil */
}

.large-text {
    display: block; /* Menempatkan 'Stage' di baris baru */
    font-size: 130%; /* Ukuran font lebih besar */
}
</style>
