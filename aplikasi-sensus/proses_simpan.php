<?php
// proses_simpan.php - Memproses penyimpanan data formulir sensus ke CSV
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Ambil dan bersihkan data POST
$no_kk = clean_input($_POST['no_kk'] ?? '');
$nik = clean_input($_POST['nik'] ?? '');
$nama_lengkap = clean_input($_POST['nama_lengkap'] ?? '');
$hubungan_keluarga = clean_input($_POST['hubungan_keluarga'] ?? '');
$jenis_kelamin = clean_input($_POST['jenis_kelamin'] ?? '');
$tempat_lahir = clean_input($_POST['tempat_lahir'] ?? '');
$tanggal_lahir_raw = clean_input($_POST['tanggal_lahir'] ?? '');
$umur = clean_input($_POST['umur'] ?? '');
$agama = clean_input($_POST['agama'] ?? '');
$status_perkawinan = clean_input($_POST['status_perkawinan'] ?? '');
$partisipasi_sekolah = clean_input($_POST['partisipasi_sekolah'] ?? '');
$pendidikan_tertinggi = clean_input($_POST['pendidikan_tertinggi'] ?? '');
$kegiatan_utama = clean_input($_POST['kegiatan_utama'] ?? '');
$lapangan_pekerjaan = clean_input($_POST['lapangan_pekerjaan'] ?? '');
$estimasi_pendapatan = clean_input($_POST['estimasi_pendapatan'] ?? '0');
$jaminan_kesehatan = clean_input($_POST['jaminan_kesehatan'] ?? '');
$disabilitas = clean_input($_POST['disabilitas'] ?? 'Tidak');
$provinsi = clean_input($_POST['provinsi'] ?? '');
$kabupaten_kota = clean_input($_POST['kabupaten_kota'] ?? '');
$kecamatan = clean_input($_POST['kecamatan'] ?? '');
$kelurahan_desa = clean_input($_POST['kelurahan_desa'] ?? '');
$alamat_domisili = clean_input($_POST['alamat_domisili'] ?? '');
$status_kepemilikan_bangunan = clean_input($_POST['status_kepemilikan_bangunan'] ?? '');
$luas_lantai = clean_input($_POST['luas_lantai'] ?? '');
$sumber_air_minum = clean_input($_POST['sumber_air_minum'] ?? '');
$daya_listrik = clean_input($_POST['daya_listrik'] ?? '');
$fasilitas_sanitasi = clean_input($_POST['fasilitas_sanitasi'] ?? '');

// Validasi Sederhana
$errors = [];
if (strlen($no_kk) !== 16 || !ctype_digit($no_kk)) {
    $errors[] = "Nomor Kartu Keluarga (No KK) harus terdiri dari 16 digit angka.";
}
if (strlen($nik) !== 16 || !ctype_digit($nik)) {
    $errors[] = "Nomor Induk Kependudukan (NIK) harus terdiri dari 16 digit angka.";
}
if (empty($nama_lengkap)) {
    $errors[] = "Nama lengkap tidak boleh kosong.";
}

// Format Tanggal Lahir (dari YYYY-MM-DD menjadi DD/MM/YYYY sesuai format CSV)
$tanggal_lahir_formatted = $tanggal_lahir_raw;
if (!empty($tanggal_lahir_raw)) {
    $tgl_parts = explode('-', $tanggal_lahir_raw);
    if (count($tgl_parts) === 3) {
        $tanggal_lahir_formatted = $tgl_parts[2] . '/' . $tgl_parts[1] . '/' . $tgl_parts[0];
    }
}

// Jika umur kosong, hitung otomatis dari tanggal lahir
if ($umur === '' && !empty($tanggal_lahir_raw)) {
    $dob = new DateTime($tanggal_lahir_raw);
    $now = new DateTime();
    $diff = $now->diff($dob);
    $umur = (string)$diff->y;
}

if (!empty($errors)) {
    $_SESSION['flash_error'] = implode('<br>', $errors);
    header('Location: index.php');
    exit;
}

// Siapkan asosiasi data sesuai judul kolom tabel CSV
$new_record = [
    'No' => '', // Diisi otomatis oleh append_csv_record
    'No KK' => $no_kk,
    'NIK' => $nik,
    'Nama Lengkap' => $nama_lengkap,
    'Hubungan Keluarga' => $hubungan_keluarga,
    'Jenis Kelamin' => $jenis_kelamin,
    'Tempat Lahir' => $tempat_lahir,
    'Tanggal Lahir' => $tanggal_lahir_formatted,
    'Umur (Tahun)' => $umur,
    'Agama' => $agama,
    'Status Perkawinan' => $status_perkawinan,
    'Partisipasi Sekolah' => $partisipasi_sekolah,
    'Pendidikan Tertinggi' => $pendidikan_tertinggi,
    'Kegiatan Utama' => $kegiatan_utama,
    'Lapangan Pekerjaan' => $lapangan_pekerjaan,
    'Estimasi Pendapatan Bulanan (Rp)' => $estimasi_pendapatan,
    'Jaminan Kesehatan' => $jaminan_kesehatan,
    'Disabilitas' => $disabilitas,
    'Provinsi' => $provinsi,
    'Kabupaten/Kota' => $kabupaten_kota,
    'Kecamatan' => $kecamatan,
    'Kelurahan/Desa' => $kelurahan_desa,
    'Alamat Domisili' => $alamat_domisili,
    'Status Kepemilikan Bangunan' => $status_kepemilikan_bangunan,
    'Luas Lantai (m2)' => $luas_lantai,
    'Sumber Air Minum' => $sumber_air_minum,
    'Daya Listrik' => $daya_listrik,
    'Fasilitas Sanitasi' => $fasilitas_sanitasi
];

// Simpan ke file CSV
$saved = append_csv_record($new_record);

if ($saved) {
    $_SESSION['flash_msg'] = "Data sensus penduduk atas nama <strong>" . htmlspecialchars($nama_lengkap) . "</strong> (NIK: " . htmlspecialchars($nik) . ") berhasil disimpan ke spreadsheet CSV!";
    header('Location: data.php');
} else {
    $_SESSION['flash_error'] = "Gagal menyimpan data ke file CSV. Pastikan file memiliki izin tulis.";
    header('Location: index.php');
}
exit;
