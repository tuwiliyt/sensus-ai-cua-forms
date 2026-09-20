# Panduan & Tutorial Pengisian Formulir Sensus Penduduk
### Menggunakan Konsep Automasi AI CUA-S1-FORMS & Antarmuka Web Manual

Tutorial ini disusun berdasarkan referensi video edukasi [CUA S1 Forms: Jev-Like for GUI Form Filling Model Locally](https://www.youtube.com/watch?v=Rzgd-y3mCPs) dan repositori resmi Hugging Face [cua-ai/cua-s1-forms](https://huggingface.co/cua-ai/cua-s1-forms).

---

## 🧠 Konsep Dasar CUA-S1-FORMS (System One Form Filling)

Model **CUA-S1-FORMS** (ukuran ~2.8 MB, 706k parameter) menggunakan paradigma **"System One"** (pemikiran cepat dan intuitif):
1. **Bukan Text Generator biasa:** Model ini tidak menghasilkan teks kata-demi-kata seperti ChatGPT/Claude yang lambat.
2. **Option Scorer Paralel:** Model ini mengevaluasi setiap elemen form pada GUI bersama kumpulan entitas data dokumen (KTP/KK), lalu memberikan probabilitas aksi dalam **satu putaran cepat (*single forward pass*)**:
   - `FILL <value>` (Mengisi input teks / angka / tanggal)
   - `SELECT <option>` (Memilih opsi dropdown)
   - `CHECK` (Mencentang radio button / checkbox)
   - `CLICK` (Mengeklik tombol simpan / submit)
   - `SKIP` (Mengabaikan kolom yang opsional atau sudah terisi)

---

## 🛠️ Step-by-Step Implementasi & Uji Coba

### Metode 1: Pengisian Otomatis Menggunakan Agen CUA (`auto_fill_agent.py`)

Kami telah membuat skrip agen otomasi berbasis arsitektur CUA di [`/root/Desktop/aplikasi-sensus/auto_fill_agent.py`](file:///root/Desktop/aplikasi-sensus/auto_fill_agent.py).

#### Langkah 1: Siapkan Data Entitas Penduduk
Data mentah penduduk (misalnya hasil pembacaan OCR KTP atau data JSON):
```json
{
  "no_kk": "3273010106240001",
  "nik": "3273011508920002",
  "nama_lengkap": "Rian Kurniawan, S.Kom.",
  "hubungan_keluarga": "Kepala Keluarga",
  "jenis_kelamin": "Laki-laki",
  "tempat_lahir": "Bandung",
  "tanggal_lahir": "1992-08-15",
  "agama": "Islam",
  "status_perkawinan": "Kawin",
  "partisipasi_sekolah": "Tidak Bersekolah Lagi",
  "pendidikan_tertinggi": "Sarjana (S1/D4)",
  "kegiatan_utama": "Bekerja",
  "lapangan_pekerjaan": "Karyawan Swasta",
  "estimasi_pendapatan": "8750000",
  "jaminan_kesehatan": "BPJS Non-PBI / Mandiri",
  "disabilitas": "Tidak",
  "provinsi": "Jawa Barat",
  "kabupaten_kota": "Kota Bandung",
  "kecamatan": "Coblong",
  "kelurahan_desa": "Dago",
  "alamat_domisili": "Jl. Cisitu Indah No. 18, RT 004/RW 002",
  "status_kepemilikan_bangunan": "Milik Sendiri",
  "luas_lantai": "72",
  "sumber_air_minum": "PAM / Ledeng",
  "daya_listrik": "PLN 1300 VA",
  "fasilitas_sanitasi": "Jamban Sendiri"
}
```

#### Langkah 2: Jalankan Eksekusi Agen Otomasi
Buka terminal dan jalankan:
```bash
python3 /root/Desktop/aplikasi-sensus/auto_fill_agent.py
```

Agen akan secara otomatis:
1. Memformat konteks 28 elemen formulir sensus.
2. Memilih nilai dokumen yang cocok untuk setiap input (`FILL`, `SELECT`, `CHECK`).
3. Menghitung umur secara dinamis dari tanggal lahir jika belum ditentukan.
4. Mengirimkan payload ke `http://127.0.0.1:8000/proses_simpan.php`.
5. Menyimpan data langsung ke database **MySQL `db_sensus`** dan file spreadsheet **CSV**.

---

### Metode 2: Pengisian Manual Melalui Antarmuka Web GUI

Jika Anda ingin mengisi data secara manual melalui browser:

1. **Buka Aplikasi Sensus di Browser:**
   - Buka alamat: **[http://127.0.0.1:8000/index.php](http://127.0.0.1:8000/index.php)**
   - Atau klik dua kali shortcut **`Formulir Sensus Penduduk 2024`** di Desktop.

2. **Isi Formulir Sesuai 5 Kelompok Data:**
   - **Bagian 1: Identitas & KK**
     - Masukkan **No KK** (16 digit angka). Perhatikan indikator panjang digit di bawah kolom (`16/16`).
     - Masukkan **NIK** (16 digit angka).
     - Masukkan **Nama Lengkap**.
     - Pilih **Hubungan Keluarga** (misal: *Kepala Keluarga*).
   - **Bagian 2: Kelahiran & Karakteristik**
     - Pilih **Jenis Kelamin** (*Laki-laki* atau *Perempuan*).
     - Ketik **Tempat Lahir**.
     - Pilih **Tanggal Lahir** pada pemilih kalender. Kolom **Umur (Tahun)** akan **terhitung otomatis**.
     - Pilih **Agama** dan **Status Perkawinan**.
   - **Bagian 3: Pendidikan & Ekonomi**
     - Pilih **Partisipasi Sekolah** & **Pendidikan Tertinggi**.
     - Pilih **Kegiatan Utama** & **Lapangan Pekerjaan**.
     - Masukkan **Estimasi Pendapatan Bulanan (Rp)**. Preview format Rupiah (misal: *Rp 8.750.000*) akan tampil secara live.
     - Pilih **Jaminan Kesehatan** & **Disabilitas**.
   - **Bagian 4: Wilayah Domisili**
     - Pilih **Provinsi** (misal: *Jawa Barat*).
     - Dropdown Kabupaten/Kota, Kecamatan, dan Kelurahan akan menyesuaikan daerah yang dipilih.
     - Masukkan **Alamat Domisili** (Nama jalan, RT/RW, nomor rumah).
   - **Bagian 5: Hunian & Sanitasi**
     - Pilih **Status Kepemilikan Bangunan**.
     - Masukkan **Luas Lantai (m2)**.
     - Pilih **Sumber Air Minum**, **Daya Listrik**, dan **Fasilitas Sanitasi**.

3. **Simpan Data:**
   - Klik tombol biru **"Simpan Data Sensus"**.
   - Halaman akan otomatis beralih ke daftar data dengan notifikasi hijau tanda berhasil.

---

## 🔍 Verifikasi Hasil Penyimpanan

Setelah mengisi formulir (baik via agen CUA maupun manual), Anda dapat memverifikasi bahwa data telah tersimpan di 3 tempat:

1. **Di Antarmuka Web Data Sensus:**
   - Buka [http://127.0.0.1:8000/data.php](http://127.0.0.1:8000/data.php)
   - Cari nama warga di kolom pencarian.
   - Klik tombol **"Detail"** untuk memeriksa 28 kolom data lengkap.

2. **Di phpMyAdmin:**
   - Buka [http://127.0.0.1:8000/phpmyadmin/](http://127.0.0.1:8000/phpmyadmin/)
   - Buka database **`db_sensus`** -> tabel **`penduduk`** -> tab **Jelajahi (Browse)**.
   - Record baru langsung masuk dengan ID urut.

3. **Di File Spreadsheet CSV:**
   - Buka berkas [`/root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv`](file:///root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv) menggunakan aplikasi **Gnumeric Spreadsheet**.
   - Baris data baru telah ditambahkan di baris paling bawah.
