<?php
// import.php - Halaman Import Spreadsheet CSV Massal
require_once __DIR__ . '/config.php';

$message = '';
$error = '';
$imported_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source_file = '';
    
    // Opsi 1: File diunggah melalui form
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $source_file = $_FILES['csv_file']['tmp_name'];
    } 
    // Opsi 2: Menggunakan file yang ada di server / data mentah
    elseif (isset($_POST['use_preset']) && $_POST['use_preset'] === 'raw_data') {
        $preset_path = __DIR__ . '/data/data_sumber_sensus_mentah.csv';
        if (file_exists($preset_path)) {
            $source_file = $preset_path;
        }
    }

    if (!empty($source_file) && file_exists($source_file)) {
        if (($handle = fopen($source_file, 'r')) !== FALSE) {
            // Handle BOM
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $headers = fgetcsv($handle, 4096, ',');
            if ($headers) {
                $headers = array_map(function($h) {
                    return trim(str_replace("\xEF\xBB\xBF", '', $h));
                }, $headers);

                while (($row = fgetcsv($handle, 4096, ',')) !== FALSE) {
                    if (empty(array_filter($row))) continue;
                    $row_dict = [];
                    foreach ($headers as $i => $h) {
                        $row_dict[$h] = isset($row[$i]) ? trim($row[$i]) : '';
                    }

                    // Format tanggal jika DD/MM/YYYY
                    $tgl = $row_dict['Tanggal Lahir'] ?? '';

                    $new_row = [
                        'No' => '',
                        'No KK' => $row_dict['No KK'] ?? '',
                        'NIK' => $row_dict['NIK'] ?? '',
                        'Nama Lengkap' => $row_dict['Nama Lengkap'] ?? '',
                        'Hubungan Keluarga' => $row_dict['Hubungan Keluarga'] ?? 'Kepala Keluarga',
                        'Jenis Kelamin' => $row_dict['Jenis Kelamin'] ?? 'Laki-laki',
                        'Tempat Lahir' => $row_dict['Tempat Lahir'] ?? '',
                        'Tanggal Lahir' => $tgl,
                        'Umur (Tahun)' => $row_dict['Umur (Tahun)'] ?? '0',
                        'Agama' => $row_dict['Agama'] ?? 'Islam',
                        'Status Perkawinan' => $row_dict['Status Perkawinan'] ?? 'Belum Kawin',
                        'Partisipasi Sekolah' => $row_dict['Partisipasi Sekolah'] ?? 'Tidak Bersekolah Lagi',
                        'Pendidikan Tertinggi' => $row_dict['Pendidikan Tertinggi'] ?? 'SMA / SMK / MA',
                        'Kegiatan Utama' => $row_dict['Kegiatan Utama'] ?? 'Bekerja',
                        'Lapangan Pekerjaan' => $row_dict['Lapangan Pekerjaan'] ?? 'Karyawan Swasta',
                        'Estimasi Pendapatan Bulanan (Rp)' => $row_dict['Estimasi Pendapatan Bulanan (Rp)'] ?? '0',
                        'Jaminan Kesehatan' => $row_dict['Jaminan Kesehatan'] ?? 'Tidak Memiliki',
                        'Disabilitas' => $row_dict['Disabilitas'] ?? 'Tidak',
                        'Provinsi' => $row_dict['Provinsi'] ?? '',
                        'Kabupaten/Kota' => $row_dict['Kabupaten/Kota'] ?? '',
                        'Kecamatan' => $row_dict['Kecamatan'] ?? '',
                        'Kelurahan/Desa' => $row_dict['Kelurahan/Desa'] ?? '',
                        'Alamat Domisili' => $row_dict['Alamat Domisili'] ?? '',
                        'Status Kepemilikan Bangunan' => $row_dict['Status Kepemilikan Bangunan'] ?? 'Milik Sendiri',
                        'Luas Lantai (m2)' => $row_dict['Luas Lantai (m2)'] ?? '36',
                        'Sumber Air Minum' => $row_dict['Sumber Air Minum'] ?? 'PAM / Ledeng',
                        'Daya Listrik' => $row_dict['Daya Listrik'] ?? 'PLN 1300 VA',
                        'Fasilitas Sanitasi' => $row_dict['Fasilitas Sanitasi'] ?? 'Jamban Sendiri'
                    ];

                    if (append_csv_record($new_row)) {
                        $imported_count++;
                    }
                }
                fclose($handle);
                $message = "Berhasil mengimpor <strong>{$imported_count} data penduduk</strong> secara massal ke dalam database MySQL dan spreadsheet CSV!";
            } else {
                $error = "Header kolom berkas CSV tidak valid atau kosong.";
            }
        } else {
            $error = "Gagal membuka berkas CSV yang diunggah.";
        }
    } else {
        $error = "Silakan pilih berkas CSV untuk diimpor.";
    }
}

require_once __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-primary">
                    <i class="bi bi-file-earmark-arrow-up-fill me-2"></i>Import Data Spreadsheet CSV Massal
                </h4>
                <p class="text-muted mb-0 small">
                    Mengimpor ratusan baris data kependudukan secara otomatis ke database MySQL dan berkas spreadsheet sensus.
                </p>
            </div>
            <a href="data.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Data
            </a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill fs-3 text-success"></i>
                    <div><?= $message ?></div>
                </div>
                <div class="mt-2">
                    <a href="data.php" class="btn btn-success btn-sm me-2">Lihat Data Penduduk</a>
                    <a href="statistik.php" class="btn btn-outline-success btn-sm">Lihat Statistik</a>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-octagon-fill fs-4 text-danger"></i>
                    <div><strong>Gagal!</strong> <?= $error ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Kartu Pilihan Import -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-cloud-upload me-2 text-primary"></i>Pilihan 1: Unggah Berkas Spreadsheet CSV
            </div>
            <div class="card-body p-4">
                <form action="import.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Pilih Berkas CSV dari Komputer:</label>
                        <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                        <div class="form-text">Mendukung format <code>.csv</code> dengan pemisah koma dan encoding UTF-8.</div>
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold">
                        <i class="bi bi-upload me-1"></i> Mulai Unggah & Import
                    </button>
                </form>
            </div>
        </div>

        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-hdd-network me-2 text-success"></i>Pilihan 2: Import dari Berkas Sumber Sensus di Server (200 Data)
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Gunakan berkas data mentah <code>data_sumber_sensus_mentah.csv</code> (200 record) yang telah tersedia di server untuk mengisi database secara otomatis dengan satu klik.
                </p>
                <form action="import.php" method="POST">
                    <input type="hidden" name="use_preset" value="raw_data">
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="bi bi-play-circle-fill me-1"></i> Import 200 Data Mentah Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Script Otomasi CLI -->
        <div class="card card-custom bg-light border">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark"><i class="bi bi-terminal-fill me-2 text-secondary"></i>Pilihan 3: Jalankan Lewat Agen Otomasi Python (CLI)</h6>
                <p class="text-muted small mb-2">
                    Untuk pemrosesan berkecepatan tinggi dengan indikator progres visual, Anda juga dapat menjalankan script agen batch:
                </p>
                <div class="bg-dark text-white p-3 rounded font-monospace small">
                    python3 /root/Desktop/aplikasi-sensus/batch_fill_agent.py
                </div>
                <div class="text-muted small mt-2">
                    * Mampu memproses 200 data hanya dalam waktu ~12 detik (16 record/detik).
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
