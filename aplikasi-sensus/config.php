<?php
// config.php - Konfigurasi & Helper Aplikasi Sensus Penduduk Indonesia 2024

session_start();

// Konfigurasi Database MySQL
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'db_sensus');
define('DB_USER', 'root');
define('DB_PASS', '');

// Fungsi koneksi PDO MySQL
function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 3
            ]);
        } catch (PDOException $e) {
            $pdo = false;
        }
    }
    return $pdo;
}

// Jalur file CSV utama dan cadangan
define('PRIMARY_CSV_PATH', '/root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv');
define('FALLBACK_CSV_PATH', __DIR__ . '/data/sensus_penduduk_indonesia_2024_dummy.csv');

// Mendapatkan path file CSV yang aktif
function get_csv_filepath() {
    if (file_exists(PRIMARY_CSV_PATH) && is_readable(PRIMARY_CSV_PATH)) {
        return PRIMARY_CSV_PATH;
    }
    return FALLBACK_CSV_PATH;
}

// Header tabel resmi sesuai dengan kolom CSV
$CSV_COLUMNS = [
    'No',
    'No KK',
    'NIK',
    'Nama Lengkap',
    'Hubungan Keluarga',
    'Jenis Kelamin',
    'Tempat Lahir',
    'Tanggal Lahir',
    'Umur (Tahun)',
    'Agama',
    'Status Perkawinan',
    'Partisipasi Sekolah',
    'Pendidikan Tertinggi',
    'Kegiatan Utama',
    'Lapangan Pekerjaan',
    'Estimasi Pendapatan Bulanan (Rp)',
    'Jaminan Kesehatan',
    'Disabilitas',
    'Provinsi',
    'Kabupaten/Kota',
    'Kecamatan',
    'Kelurahan/Desa',
    'Alamat Domisili',
    'Status Kepemilikan Bangunan',
    'Luas Lantai (m2)',
    'Sumber Air Minum',
    'Daya Listrik',
    'Fasilitas Sanitasi'
];

// Opsi Kategori berdasarkan data sensus
$MASTER_OPTIONS = [
    'Hubungan Keluarga' => [
        'Kepala Keluarga',
        'Suami',
        'Istri',
        'Anak',
        'Orang Tua / Mertua',
        'Famili Lain'
    ],
    'Jenis Kelamin' => [
        'Laki-laki',
        'Perempuan'
    ],
    'Agama' => [
        'Islam',
        'Kristen Protestan',
        'Katolik',
        'Hindu',
        'Buddha',
        'Khonghucu'
    ],
    'Status Perkawinan' => [
        'Belum Kawin',
        'Kawin',
        'Cerai Hidup',
        'Cerai Mati'
    ],
    'Partisipasi Sekolah' => [
        'Belum Pernah Sekolah',
        'Masih Sekolah',
        'Tidak Bersekolah Lagi'
    ],
    'Pendidikan Tertinggi' => [
        'Tidak / Belum Sekolah',
        'PAUD / TK',
        'SD / Sederajat',
        'SMP / Sederajat',
        'SMA / SMK / MA',
        'Diploma (D1-D3)',
        'Sarjana (S1/D4)',
        'Magister (S2)',
        'Doktor (S3)'
    ],
    'Kegiatan Utama' => [
        'Bekerja',
        'Mengurus Rumah Tangga',
        'Sekolah',
        'Pensiunan / Tidak Bekerja',
        'Tidak Bekerja'
    ],
    'Lapangan Pekerjaan' => [
        'Pegawai Negeri Sipil (PNS)',
        'Karyawan BUMN',
        'Karyawan Swasta',
        'Wiraswasta / Pedagang',
        'Petani / Pekebun',
        'Guru / Dosen',
        'Tenaga Kesehatan (Dokter/Perawat)',
        'Teknisi / Pengrajin',
        'Buruh Harian Lepas',
        'Pengemudi Ojek Online / Transportasi',
        'Pensiunan',
        'Pelajar / Mahasiswa',
        'Mengurus Rumah Tangga',
        'Belum / Tidak Bekerja'
    ],
    'Jaminan Kesehatan' => [
        'BPJS PBI (Bantuan Pemerintah)',
        'BPJS Non-PBI / Mandiri',
        'Asuransi Swasta',
        'Tidak Memiliki'
    ],
    'Disabilitas' => [
        'Tidak',
        'Tunadaksa',
        'Tunanetra',
        'Tunarungu',
        'Tunawicara',
        'Disabilitas Mental/Intelektual'
    ],
    'Status Kepemilikan Bangunan' => [
        'Milik Sendiri',
        'Sewa / Kontrak',
        'Rumah Dinas',
        'Bebas Sewa (Milik Keluarga/Ortu)'
    ],
    'Sumber Air Minum' => [
        'PAM / Ledeng',
        'Sumur Bor / Pompa',
        'Sumur Terlindung',
        'Mata Air',
        'Air Kemasan / Isi Ulang'
    ],
    'Daya Listrik' => [
        'PLN 450 VA',
        'PLN 900 VA',
        'PLN 1300 VA',
        'PLN 2200 VA+',
        'Non-PLN / Tanpa Listrik'
    ],
    'Fasilitas Sanitasi' => [
        'Jamban Sendiri',
        'Jamban Bersama',
        'Bukan Jamban Sendiri'
    ]
];

// Data hirarki wilayah dari dataset sensus
$MASTER_WILAYAH = [
    "Jawa Barat" => [
        "Kota Bandung" => [
            "Coblong" => ["Dago"]
        ],
        "Kab. Bogor" => [
            "Cibinong" => ["Pabuaran"]
        ]
    ],
    "DKI Jakarta" => [
        "Kota Jakarta Selatan" => [
            "Tebet" => ["Tebet Timur"]
        ]
    ],
    "Jawa Tengah" => [
        "Kota Semarang" => [
            "Banyumanik" => ["Srondol Kulon"]
        ]
    ],
    "Jawa Timur" => [
        "Kota Surabaya" => [
            "Gubeng" => ["Airlangga"]
        ]
    ],
    "Bali" => [
        "Kota Denpasar" => [
            "Denpasar Selatan" => ["Sanur Kaja"]
        ]
    ],
    "Sumatera Utara" => [
        "Kota Medan" => [
            "Medan Baru" => ["Padang Bulan"]
        ]
    ],
    "Sumatera Barat" => [
        "Kota Padang" => [
            "Padang Barat" => ["Flamboyan Baru"]
        ]
    ],
    "Kalimantan Timur" => [
        "Kota Balikpapan" => [
            "Balikpapan Kota" => ["Klandasan Ulu"]
        ]
    ],
    "Sulawesi Selatan" => [
        "Kota Makassar" => [
            "Panakkukang" => ["Pannampu"]
        ]
    ]
];

// Membaca seluruh data (dari MySQL db_sensus jika tersedia, fallback ke CSV)
function read_csv_records() {
    $pdo = get_db_connection();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM `penduduk` ORDER BY `id` ASC");
            $rows = $stmt->fetchAll();
            if ($rows !== false) {
                $records = [];
                foreach ($rows as $r) {
                    $records[] = [
                        'No' => (string)($r['no_urut'] ?? $r['id']),
                        'No KK' => (string)($r['no_kk'] ?? ''),
                        'NIK' => (string)($r['nik'] ?? ''),
                        'Nama Lengkap' => (string)($r['nama_lengkap'] ?? ''),
                        'Hubungan Keluarga' => (string)($r['hubungan_keluarga'] ?? ''),
                        'Jenis Kelamin' => (string)($r['jenis_kelamin'] ?? ''),
                        'Tempat Lahir' => (string)($r['tempat_lahir'] ?? ''),
                        'Tanggal Lahir' => (string)($r['tanggal_lahir'] ?? ''),
                        'Umur (Tahun)' => (string)($r['umur'] ?? '0'),
                        'Agama' => (string)($r['agama'] ?? ''),
                        'Status Perkawinan' => (string)($r['status_perkawinan'] ?? ''),
                        'Partisipasi Sekolah' => (string)($r['partisipasi_sekolah'] ?? ''),
                        'Pendidikan Tertinggi' => (string)($r['pendidikan_tertinggi'] ?? ''),
                        'Kegiatan Utama' => (string)($r['kegiatan_utama'] ?? ''),
                        'Lapangan Pekerjaan' => (string)($r['lapangan_pekerjaan'] ?? ''),
                        'Estimasi Pendapatan Bulanan (Rp)' => (string)($r['estimasi_pendapatan'] ?? '0'),
                        'Jaminan Kesehatan' => (string)($r['jaminan_kesehatan'] ?? ''),
                        'Disabilitas' => (string)($r['disabilitas'] ?? 'Tidak'),
                        'Provinsi' => (string)($r['provinsi'] ?? ''),
                        'Kabupaten/Kota' => (string)($r['kabupaten_kota'] ?? ''),
                        'Kecamatan' => (string)($r['kecamatan'] ?? ''),
                        'Kelurahan/Desa' => (string)($r['kelurahan_desa'] ?? ''),
                        'Alamat Domisili' => (string)($r['alamat_domisili'] ?? ''),
                        'Status Kepemilikan Bangunan' => (string)($r['status_kepemilikan_bangunan'] ?? ''),
                        'Luas Lantai (m2)' => (string)($r['luas_lantai'] ?? '0'),
                        'Sumber Air Minum' => (string)($r['sumber_air_minum'] ?? ''),
                        'Daya Listrik' => (string)($r['daya_listrik'] ?? ''),
                        'Fasilitas Sanitasi' => (string)($r['fasilitas_sanitasi'] ?? '')
                    ];
                }
                return $records;
            }
        } catch (Exception $e) {
            // Fallback ke CSV jika terjadi kesalahan query
        }
    }

    $filepath = get_csv_filepath();
    $records = [];
    if (!file_exists($filepath)) {
        return $records;
    }

    if (($handle = fopen($filepath, "r")) !== FALSE) {
        // Handle BOM UTF-8
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle, 4096, ",");
        if (!$headers) {
            fclose($handle);
            return $records;
        }

        // Trim spasi dan BOM dari header
        $headers = array_map(function($h) {
            return trim(str_replace("\xEF\xBB\xBF", '', $h));
        }, $headers);

        while (($row = fgetcsv($handle, 4096, ",")) !== FALSE) {
            if (empty(array_filter($row))) continue;
            $record = [];
            foreach ($headers as $i => $h) {
                $record[$h] = isset($row[$i]) ? trim($row[$i]) : '';
            }
            $records[] = $record;
        }
        fclose($handle);
    }
    return $records;
}

// Menambahkan data baru ke MySQL db_sensus dan file CSV secara sinkron
function append_csv_record($new_row) {
    global $CSV_COLUMNS;
    $target_files = array_unique([PRIMARY_CSV_PATH, FALLBACK_CSV_PATH]);
    $success = false;

    // Tentukan nomor urut berikutnya
    $records = read_csv_records();
    $next_no = count($records) + 1;
    $new_row['No'] = (string)$next_no;

    // 1. Simpan ke MySQL jika koneksi aktif
    $pdo = get_db_connection();
    if ($pdo) {
        try {
            $sql = "INSERT INTO `penduduk` (
                `no_urut`, `no_kk`, `nik`, `nama_lengkap`, `hubungan_keluarga`, `jenis_kelamin`,
                `tempat_lahir`, `tanggal_lahir`, `umur`, `agama`, `status_perkawinan`,
                `partisipasi_sekolah`, `pendidikan_tertinggi`, `kegiatan_utama`, `lapangan_pekerjaan`,
                `estimasi_pendapatan`, `jaminan_kesehatan`, `disabilitas`, `provinsi`, `kabupaten_kota`,
                `kecamatan`, `kelurahan_desa`, `alamat_domisili`, `status_kepemilikan_bangunan`,
                `luas_lantai`, `sumber_air_minum`, `daya_listrik`, `fasilitas_sanitasi`
            ) VALUES (
                :no_urut, :no_kk, :nik, :nama_lengkap, :hubungan_keluarga, :jenis_kelamin,
                :tempat_lahir, :tanggal_lahir, :umur, :agama, :status_perkawinan,
                :partisipasi_sekolah, :pendidikan_tertinggi, :kegiatan_utama, :lapangan_pekerjaan,
                :estimasi_pendapatan, :jaminan_kesehatan, :disabilitas, :provinsi, :kabupaten_kota,
                :kecamatan, :kelurahan_desa, :alamat_domisili, :status_kepemilikan_bangunan,
                :luas_lantai, :sumber_air_minum, :daya_listrik, :fasilitas_sanitasi
            )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':no_urut' => $next_no,
                ':no_kk' => $new_row['No KK'] ?? '',
                ':nik' => $new_row['NIK'] ?? '',
                ':nama_lengkap' => $new_row['Nama Lengkap'] ?? '',
                ':hubungan_keluarga' => $new_row['Hubungan Keluarga'] ?? '',
                ':jenis_kelamin' => $new_row['Jenis Kelamin'] ?? '',
                ':tempat_lahir' => $new_row['Tempat Lahir'] ?? '',
                ':tanggal_lahir' => $new_row['Tanggal Lahir'] ?? '',
                ':umur' => (int)($new_row['Umur (Tahun)'] ?? 0),
                ':agama' => $new_row['Agama'] ?? '',
                ':status_perkawinan' => $new_row['Status Perkawinan'] ?? '',
                ':partisipasi_sekolah' => $new_row['Partisipasi Sekolah'] ?? '',
                ':pendidikan_tertinggi' => $new_row['Pendidikan Tertinggi'] ?? '',
                ':kegiatan_utama' => $new_row['Kegiatan Utama'] ?? '',
                ':lapangan_pekerjaan' => $new_row['Lapangan Pekerjaan'] ?? '',
                ':estimasi_pendapatan' => (int)preg_replace('/[^0-9]/', '', $new_row['Estimasi Pendapatan Bulanan (Rp)'] ?? '0'),
                ':jaminan_kesehatan' => $new_row['Jaminan Kesehatan'] ?? '',
                ':disabilitas' => $new_row['Disabilitas'] ?? 'Tidak',
                ':provinsi' => $new_row['Provinsi'] ?? '',
                ':kabupaten_kota' => $new_row['Kabupaten/Kota'] ?? '',
                ':kecamatan' => $new_row['Kecamatan'] ?? '',
                ':kelurahan_desa' => $new_row['Kelurahan/Desa'] ?? '',
                ':alamat_domisili' => $new_row['Alamat Domisili'] ?? '',
                ':status_kepemilikan_bangunan' => $new_row['Status Kepemilikan Bangunan'] ?? '',
                ':luas_lantai' => (int)($new_row['Luas Lantai (m2)'] ?? 0),
                ':sumber_air_minum' => $new_row['Sumber Air Minum'] ?? '',
                ':daya_listrik' => $new_row['Daya Listrik'] ?? '',
                ':fasilitas_sanitasi' => $new_row['Fasilitas Sanitasi'] ?? ''
            ]);
            $success = true;
        } catch (Exception $e) {
            // Jika gagal MySQL, tetap lanjutkan ke CSV
        }
    }

    // 2. Siapkan array sesuai urutan kolom CSV
    $row_ordered = [];
    foreach ($CSV_COLUMNS as $col) {
        $row_ordered[] = isset($new_row[$col]) ? $new_row[$col] : '';
    }

    foreach ($target_files as $file) {
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        // Jika file belum ada, tulis header terlebih dahulu
        $is_new = !file_exists($file) || filesize($file) === 0;

        $fp = fopen($file, 'a');
        if ($fp !== FALSE) {
            if ($is_new) {
                // Tulis BOM UTF-8 dan header
                fwrite($fp, "\xEF\xBB\xBF");
                fputcsv($fp, $CSV_COLUMNS);
            }
            fputcsv($fp, $row_ordered);
            fclose($fp);
            $success = true;
        }
    }
    return $success;
}

// Format Rupiah
function format_rupiah($angka) {
    $num = preg_replace('/[^0-9]/', '', $angka);
    if ($num === '' || $num === null) return 'Rp 0';
    return 'Rp ' . number_format((float)$num, 0, ',', '.');
}

// Sanitisasi Input
function clean_input($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}
