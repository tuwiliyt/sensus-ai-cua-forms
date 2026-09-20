# Otomasi Pengisian Formulir Kependudukan AI (CUA-S1-FORMS)
### Menjembatani Formulir Lapangan Eksternal dengan Sistem Sensus Internal Tertutup

[![Author](https://img.shields.io/badge/Author-Richie%20Octavian%20S.-blue.svg)](#penulis--afiliasi)
[![Affiliation](https://img.shields.io/badge/Affiliation-Panita%20Community%20Gorontalo-orange.svg)](#penulis--afiliasi)
[![Review Score](https://img.shields.io/badge/Peer%20Review-9.81%2F10.0%20(APPROVED)-success.svg)](#publikasi-ilmiah-resmi)
[![Model](https://img.shields.io/badge/AI%20Model-CUA--S1--FORMS%20(Safetensors)-purple.svg)](#arsitektur-model-ai)
[![Zenodo](https://img.shields.io/badge/Zenodo-Integration%20Ready-024c7e.svg)](https://zenodo.org/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](#lisensi)

---

## 📄 Publikasi Ilmiah Resmi

Karya tulis ilmiah populer dan laporan teknis lengkap tersedia dalam 2 versi bahasa:

1. 🇮🇩 **Edisi Bahasa Indonesia:**  
   [**Karya_Ilmiah_Model_AI_CUA_S1_Sensus.pdf**](./Karya_Ilmiah_Model_AI_CUA_S1_Sensus.pdf) *(15 Halaman, 2.47 MB, Peer Review: 9.81/10)*  
   *Judul: Otomasi Pengisian Formulir Kependudukan Menggunakan Model Kecerdasan Buatan CUA-S1-FORMS: Menjembatani Formulir Lapangan Eksternal dengan Sistem Sensus Internal Tertutup*

2. 🇬🇧 **English Edition:**  
   [**Scientific_Paper_CUA_S1_AI_Census_Form_Automation_EN.pdf**](./Scientific_Paper_CUA_S1_AI_Census_Form_Automation_EN.pdf) *(13 Pages, 2.44 MB)*  
   *Title: Automating Population Census Form-Filling Using the CUA-S1-FORMS Artificial Intelligence Model: Bridging External Field Surveys with Air-Gapped Internal Census Systems*

3. 🌐 **Portal Landing Page & Web Index:**  
   [**https://tuwiliyt.github.io/sensus-ai-cua-forms/**](https://tuwiliyt.github.io/sensus-ai-cua-forms/)  
   *(Dilengkapi Highwire Press Meta Tags & Schema.org JSON-LD untuk pengindeksan Google Scholar)*

---

## 👤 Penulis & Afiliasi

- **Penulis:** Richie Octavian S.
- **Afiliasi:** Pemerhati AI dari Panita Community Gorontalo (*AI Observer & Practitioner, Panita Community Gorontalo*)
- **Tanggal Rilis:** 20 September 2026

---

## 📌 Latar Belakang Masalah Lapangan

Dalam kegiatan sensus atau survei kependudukan skala besar di Indonesia:
1. **Pengumpulan Data Lapangan:** Petugas pencacah mengumpulkan data warga secara cepat di lapangan menggunakan formulir online umum (seperti Google Form, KoboToolbox, atau form mobile) karena mudah diakses dari perangkat HP petugas di mana saja.
2. **Sistem Sensus Terisolasi (Air-Gapped Intranet):** Database kependudukan resmi dan sistem sensus internal disimpan di server intranet lokal atau jaringan tertutup yang tidak terhubung langsung ke internet publik demi melindungi privasi NIK, data keluarga, dan keamanan siber nasional.
3. **Kendala Re-entry Manual (Data Bottleneck):** Akibat pemisahan jaringan ini, petugas administrasi di kantor harus mengetikkan ulang (*manual entry*) ratusan hingga ribuan baris data dari spreadsheet hasil Google Form ke sistem sensus internal satu per satu. Proses ini lambat, membosankan, dan rentan salah ketik (*human error*).
4. **Kelemahan Aturan Statis:** Menggunakan script pencocokan kata biasa (*regex/if-else*) mudah gagal jika label formulir berubah (misal: "No KK" vs "Nomor Kartu Keluarga", atau "Tanggal Lahir" vs "Date of Birth").

### 💡 Solusi: Model AI CUA-S1-FORMS
Repositori ini menghadirkan jembatan cerdas: berkas data lapangan yang diekspor diserahkan ke model AI **CUA-S1-FORMS** di komputer kantor. Model AI secara mandiri menganalisis struktur formulir sensus web, mencocokkan kolom dengan data warga berdasarkan pemahaman semantik, dan mengirimkan data secara akurat dan instan tanpa rekayasa backend ilegal.

---

## 🧠 Arsitektur Model AI

```
[Formulir HTML / Element] ---> [Byte Tokenizer (257 Token UTF-8)]
                                         |
                                         v
[Data Warga / Entity]    ---> [Option-Attention Byte Transformer]
                                         | (706,048 Parameter Safetensors)
                                         v
                              [Dot-Product Attention Scoring]
                                         |
                                         v
                              [Softmax Probabilitas & Argmax]
                                         |
                                         v
                              [Aksi Cerdas: FILL / CLICK]
                                         |
                                         v
                              [Headless HTTP POST Transmission]
```

- **Byte Tokenizer (257 Token):** Membaca teks pada tingkat bita (*raw UTF-8 bytes*), bukan kata kamus. Hal ini membuat AI kebal terhadap singkatan, ejaan tidak baku, maupun typo bahasa daerah.
- **Option Attention:** Menghitung keselarasan antara kolom target (*Query*) dengan seluruh opsi data warga (*Keys*) melalui perkalian titik terbobot.
- **Probabilitas Keyakinan (Confidence Score):** Setiap aksi diputuskan dengan nilai kepastian matematis tinggi (rata-rata 97.9%).
- **Headless HTTP POST vs GUI Visual:** AI bertindak sebagai pembuat keputusan semantik, sementara transmisi dilakukan melalui protokol HTTP standar secara headless (~16 data/detik) yang 160 kali lebih cepat daripada robot klik layar GUI konvensional.

---

## 📊 Hasil Pengujian & Evaluasi

| Komponen Formulir | Target Data KTP / KK | Nilai Keyakinan (Confidence) | Status Keputusan |
| :--- | :--- | :---: | :---: |
| **Policy # / No KK** | Nomor Kartu Keluarga | **100.0%** | MATCH (FILL) |
| **Date of Birth** | Tanggal Lahir | **100.0%** | MATCH (FILL) |
| **Street Address** | Alamat Lengkap Domisili | **100.0%** | MATCH (FILL) |
| **City / Kota** | Kabupaten / Kota | **99.9%** | MATCH (FILL) |
| **Insurance / BPJS** | Status Jaminan Kesehatan | **99.4%** | MATCH (FILL) |
| **Full Name** | Nama Lengkap Warga | **86.3%** | MATCH (FILL) |
| **Submit Button** | Kirim Formulir | **100.0%** | ACTION (CLICK) |

- **Uji Batch 200 Record:** Diselesaikan dalam ~60 detik dengan tingkat keberhasilan transmisi **100% (200/200)**.
- **Integritas Database:** Terverifikasi sinkron pada MySQL/MariaDB `db_sensus`, visualisasi phpMyAdmin, dan ekspor spreadsheet.

---

## 📂 Struktur Repositori

```
sensus-ai-cua-forms/
├── Karya_Ilmiah_Model_AI_CUA_S1_Sensus.pdf   # Karya Ilmiah Bahasa Indonesia (15 Hal)
├── Scientific_Paper_CUA_S1_AI_Census_Form_Automation_EN.pdf # Scientific Paper English (13 Pages)
├── TUTORIAL_CUA_S1_FORMS.txt                  # Panduan Lengkap untuk Orang Awam
├── sensus_penduduk_indonesia_2024_dummy.csv   # Dataset Sampel 200 Data Warga
├── index.html                                 # Landing Page Portal & Google Scholar Metadata
├── robots.txt                                 # Konfigurasi Perayap Mesin Pencari
├── sitemap.xml                                # Peta Situs Pengindeksan PDF & Artikel
│
├── aplikasi-sensus/                           # Sistem Aplikasi Web Sensus Penduduk
│   ├── config.php                             # Konfigurasi Koneksi MySQL & CSV
│   ├── index.php                              # Formulir Entri Data Kependudukan (28 Atribut)
│   ├── data.php                               # Tabel Data Kependudukan & Pencarian
│   ├── statistik.php                          # Dasbor Statistik Demografi Warga
│   ├── db_sensus.sql                          # Skema & Data Awal Database MySQL
│   ├── run_cua_model_test.py                  # Skrip Inferensi Model AI (Uji 1 Record)
│   ├── batch_cua_ai_filler.py                 # Skrip Pengisian Massal AI (200 Record)
│   ├── models/                                # Bobot Model AI CUA-S1 (~2.8 MB Safetensors)
│   │   └── model.safetensors
│   └── phpmyadmin/                            # Antarmuka Pengelolaan Database Visual
│
└── publikasi_ilmiah_cua/                      # Sumber Penyusunan Publikasi Ilmiah
    ├── naskah_karya_ilmiah.md                 # Naskah Lengkap Markdown
    ├── generate_diagrams.py                   # Generator Diagram Resolusi Tinggi (300 DPI)
    ├── generate_pdf_id_richie.py              # Skrip Kompilasi PDF Edisi Indonesia
    ├── generate_pdf_en_richie.py              # Skrip Kompilasi PDF Edisi Inggris
    └── images/                                # Diagram Teknis Resmi (300 DPI)
        ├── diagram_1_alur_sistem.png
        ├── diagram_2_arsitektur_ai.png
        └── diagram_3_evaluasi_model.png
```

---

## 🚀 Panduan Menjalankan Sistem

### 1. Prasyarat Lingkungan
- **Python 3.10+** dengan pustaka: `torch`, `safetensors`, `requests`, `matplotlib`, `reportlab`
- **PHP 8.x**
- **MySQL / MariaDB**

### 2. Menjalankan Server Sensus Lokal
```bash
# Masuk ke direktori aplikasi
cd aplikasi-sensus

# Jalankan web server internal PHP
php -S 0.0.0.0:8000
```
Buka browser di `http://localhost:8000` untuk melihat formulir web sensus internal.

### 3. Menguji Inferensi Model AI (1 Record)
```bash
python3 run_cua_model_test.py
```
Skrip akan memuat bobot `models/model.safetensors`, menjalankan inferensi *Option Attention*, mencocokkan kolom formulir dengan data KTP, dan menampilkan skor keyakinan sebelum mengirim data.

### 4. Menjalankan Pengisian Otomatis Massal (Batch 200 Record)
```bash
python3 batch_cua_ai_filler.py
```
200 baris data dari `sensus_penduduk_indonesia_2024_dummy.csv` akan diproses dan diinput secara cerdas ke database sensus internal dalam waktu kurang lebih 60 detik.

---

## 📚 Sitasi & Kutipan (BibTeX)

Jika Anda merujuk atau menggunakan penelitian ini dalam kajian akademik, silakan gunakan format sitasi berikut:

```bibtex
@article{octavian2026cuas1,
  title={Otomasi Pengisian Formulir Kependudukan Menggunakan Model Kecerdasan Buatan CUA-S1-FORMS: Menjembatani Formulir Lapangan Eksternal dengan Sistem Sensus Internal Tertutup},
  author={Octavian S., Richie},
  journal={Publikasi Ilmiah Panita Community Gorontalo},
  volume={1},
  number={1},
  pages={1--15},
  year={2026},
  publisher={Panita Community Gorontalo},
  url={https://tuwiliyt.github.io/sensus-ai-cua-forms/Karya_Ilmiah_Model_AI_CUA_S1_Sensus.pdf}
}
```

---

## ⚖️ Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).
Dokumen karya ilmiah dan infografis dilisensikan di bawah [Creative Commons Attribution 4.0 International (CC BY 4.0)](https://creativecommons.org/licenses/by/4.0/).
