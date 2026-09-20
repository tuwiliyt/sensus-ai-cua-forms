# Aplikasi Formulir Sensus Penduduk Indonesia 2024 (PHP)

Aplikasi web sederhana berbasis PHP yang dirancang khusus untuk memproses dan menginput data sensus penduduk sesuai dengan format dan judul kolom file spreadsheet **`sensus_penduduk_indonesia_2024_dummy.csv`** (terletak di folder `downloads`).

---

## 📋 Struktur 28 Kolom Sensus yang Didukung

Aplikasi ini mengelompokkan 28 kolom tabel CSV ke dalam 5 bagian formulir yang terstruktur:

1. **Identitas Kependudukan & Hubungan Keluarga**
   - `No` (Nomor urut otomatis)
   - `No KK` (Nomor Kartu Keluarga - 16 digit)
   - `NIK` (Nomor Induk Kependudukan - 16 digit)
   - `Nama Lengkap`
   - `Hubungan Keluarga` (*Kepala Keluarga, Suami, Istri, Anak, Orang Tua / Mertua, Famili Lain*)

2. **Karakteristik Individu & Kelahiran**
   - `Jenis Kelamin` (*Laki-laki, Perempuan*)
   - `Tempat Lahir` (Auto-suggest kota/kabupaten)
   - `Tanggal Lahir` (Datepicker interaktif, format otomatis DD/MM/YYYY)
   - `Umur (Tahun)` (Perhitungan otomatis berdasarkan tanggal lahir)
   - `Agama` (*Islam, Kristen Protestan, Katolik, Hindu, Buddha, Khonghucu*)
   - `Status Perkawinan` (*Belum Kawin, Kawin, Cerai Hidup, Cerai Mati*)

3. **Pendidikan, Pekerjaan & Ekonomi**
   - `Partisipasi Sekolah` (*Belum Pernah Sekolah, Masih Sekolah, Tidak Bersekolah Lagi*)
   - `Pendidikan Tertinggi` (*SD, SMP, SMA, Diploma, Sarjana, Magister, dll.*)
   - `Kegiatan Utama` (*Bekerja, Mengurus Rumah Tangga, Sekolah, Pensiunan, Tidak Bekerja*)
   - `Lapangan Pekerjaan` (*PNS, Swasta, BUMN, Wiraswasta, Petani, Guru, Tenaga Kesehatan, dll.*)
   - `Estimasi Pendapatan Bulanan (Rp)` (Input angka dengan preview format Rupiah)
   - `Jaminan Kesehatan` (*BPJS PBI, BPJS Mandiri, Asuransi Swasta, Tidak Memiliki*)
   - `Disabilitas` (*Tidak, Tunadaksa, Tunanetra, Tunarungu, dll.*)

4. **Wilayah & Alamat Domisili**
   - `Provinsi` (Dropdown cascade terintegrasi)
   - `Kabupaten/Kota` (Dropdown cascade terintegrasi)
   - `Kecamatan` (Dropdown cascade terintegrasi)
   - `Kelurahan/Desa` (Dropdown cascade terintegrasi)
   - `Alamat Domisili` (Nama jalan, RT/RW, nomor rumah)

5. **Kondisi Bangunan & Fasilitas Sanitasi**
   - `Status Kepemilikan Bangunan` (*Milik Sendiri, Sewa / Kontrak, Rumah Dinas, Bebas Sewa*)
   - `Luas Lantai (m2)` (Angka dalam meter persegi)
   - `Sumber Air Minum` (*PAM / Ledeng, Sumur Bor, Sumur Terlindung, Air Kemasan, dll.*)
   - `Daya Listrik` (*PLN 450 VA, 900 VA, 1300 VA, 2200 VA+, Non-PLN*)
   - `Fasilitas Sanitasi` (*Jamban Sendiri, Jamban Bersama, Bukan Jamban Sendiri*)

---

## 🚀 Fitur Utama Aplikasi

- **Formulir Interaktif**:
  - Live 16-digit counter untuk No KK & NIK.
  - Perhitungan usia otomatis begitu tanggal lahir dipilih.
  - Live currency formatting Rupiah untuk kolom estimasi pendapatan.
  - Dropdown wilayah berjenjang (Provinsi → Kab/Kota → Kecamatan → Kelurahan).
- **Penyimpanan Langsung ke CSV**:
  - Data yang disubmit akan langsung disimpan dan ditambahkan (append) ke file `/root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv` serta file lokal aplikasi dengan penomoran urut otomatis (`No`).
- **Tabel Data & Pencarian**:
  - Tampilan daftar penduduk lengkap dengan paginasi.
  - Pencarian fleksibel berdasarkan nama, NIK, No KK, atau alamat.
  - Filter berdasarkan provinsi dan jenis kelamin.
  - Modal detail popup untuk memeriksa 28 kolom data lengkap setiap warga.
- **Statistik & Analitik**:
  - Diagram komposisi gender, sebaran usia, tingkat pendidikan, lapangan kerja, dan sumber air bersih menggunakan Chart.js.
- **Ekspor CSV**:
  - Download file spreadsheet CSV terbaru kapan saja.

---

## 🖥️ Cara Menjalankan Aplikasi

1. Jalankan script runner:
   ```bash
   bash /root/Desktop/aplikasi-sensus/start.sh
   ```
2. Atau jalankan PHP server secara manual:
   ```bash
   php -S 0.0.0.0:8000 -t /root/Desktop/aplikasi-sensus
   ```
3. Buka browser dan akses:
   ```
   http://127.0.0.1:8000
   ```
   Atau klik shortcut **Formulir Sensus Penduduk 2024** di Desktop.
