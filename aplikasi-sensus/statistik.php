<?php
// statistik.php - Dashboard Analitik & Statistik Sensus Penduduk
require_once __DIR__ . '/config.php';

$records = read_csv_records();
$total = count($records);

// Hitung data agregasi
$gender_count = ['Laki-laki' => 0, 'Perempuan' => 0];
$agama_count = [];
$pendidikan_count = [];
$pekerjaan_count = [];
$provinsi_count = [];
$air_count = [];
$bangunan_count = [];

$total_umur = 0;
$total_pendapatan = 0;
$valid_pendapatan_count = 0;

$kelompok_umur = [
    '0 - 14 th (Anak)' => 0,
    '15 - 24 th (Muda)' => 0,
    '25 - 54 th (Produktif)' => 0,
    '55+ th (Lansia)' => 0
];

foreach ($records as $r) {
    // Gender
    $jk = $r['Jenis Kelamin'] ?? '';
    if (isset($gender_count[$jk])) {
        $gender_count[$jk]++;
    }

    // Agama
    $ag = $r['Agama'] ?? 'Lainnya';
    $agama_count[$ag] = ($agama_count[$ag] ?? 0) + 1;

    // Pendidikan
    $pnd = $r['Pendidikan Tertinggi'] ?? 'Tidak Diketahui';
    $pendidikan_count[$pnd] = ($pendidikan_count[$pnd] ?? 0) + 1;

    // Pekerjaan
    $pkj = $r['Lapangan Pekerjaan'] ?? 'Lainnya';
    $pekerjaan_count[$pkj] = ($pekerjaan_count[$pkj] ?? 0) + 1;

    // Provinsi
    $prov = $r['Provinsi'] ?? 'Lainnya';
    $provinsi_count[$prov] = ($provinsi_count[$prov] ?? 0) + 1;

    // Air Minum
    $air = $r['Sumber Air Minum'] ?? 'Lainnya';
    $air_count[$air] = ($air_count[$air] ?? 0) + 1;

    // Status Bangunan
    $bng = $r['Status Kepemilikan Bangunan'] ?? 'Lainnya';
    $bangunan_count[$bng] = ($bangunan_count[$bng] ?? 0) + 1;

    // Umur
    $u = (int)($r['Umur (Tahun)'] ?? 0);
    $total_umur += $u;
    if ($u <= 14) $kelompok_umur['0 - 14 th (Anak)']++;
    elseif ($u <= 24) $kelompok_umur['15 - 24 th (Muda)']++;
    elseif ($u <= 54) $kelompok_umur['25 - 54 th (Produktif)']++;
    else $kelompok_umur['55+ th (Lansia)']++;

    // Pendapatan
    $inc = (float)($r['Estimasi Pendapatan Bulanan (Rp)'] ?? 0);
    if ($inc > 0) {
        $total_pendapatan += $inc;
        $valid_pendapatan_count++;
    }
}

$rata_umur = $total > 0 ? round($total_umur / $total, 1) : 0;
$rata_pendapatan = $valid_pendapatan_count > 0 ? round($total_pendapatan / $valid_pendapatan_count) : 0;

arsort($pekerjaan_count);
arsort($pendidikan_count);
arsort($provinsi_count);

require_once __DIR__ . '/header.php';
?>

<!-- Header Halaman -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary">
            <i class="bi bi-graph-up-arrow me-2"></i>Statistik & Analitik Sensus Penduduk 2024
        </h4>
        <p class="text-muted mb-0 small">Ringkasan demografi, sosial, dan ekonomi dari <?= $total ?> jiwa penduduk terdaftar</p>
    </div>
    <a href="index.php" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Input Data Baru
    </a>
</div>

<!-- Kartu Ringkasan Indikator -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-primary border-4">
            <span class="text-muted small fw-semibold text-uppercase">Rata-rata Usia Penduduk</span>
            <h3 class="fw-bold text-dark mt-2 mb-0"><?= $rata_umur ?> <span class="fs-6 text-muted fw-normal">Tahun</span></h3>
            <small class="text-secondary">Kelompok mayoritas produktif</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-success border-4">
            <span class="text-muted small fw-semibold text-uppercase">Rata-rata Pendapatan</span>
            <h3 class="fw-bold text-success mt-2 mb-0"><?= format_rupiah($rata_pendapatan) ?></h3>
            <small class="text-secondary">Bagi penduduk yang berpenghasilan</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-info border-4">
            <span class="text-muted small fw-semibold text-uppercase">Rasio Gender (L / P)</span>
            <h3 class="fw-bold text-dark mt-2 mb-0"><?= $gender_count['Laki-laki'] ?> : <?= $gender_count['Perempuan'] ?></h3>
            <small class="text-secondary"><?= round(($gender_count['Laki-laki'] / max(1, $total)) * 100) ?>% Laki-laki &bull; <?= round(($gender_count['Perempuan'] / max(1, $total)) * 100) ?>% Perempuan</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 bg-white border-start border-warning border-4">
            <span class="text-muted small fw-semibold text-uppercase">Total Provinsi Terjangkau</span>
            <h3 class="fw-bold text-dark mt-2 mb-0"><?= count($provinsi_count) ?> <span class="fs-6 text-muted fw-normal">Provinsi</span></h3>
            <small class="text-secondary">Cakupan wilayah nasional</small>
        </div>
    </div>
</div>

<!-- Grafik Grid -->
<div class="row g-4 mb-4">
    <!-- Chart Gender -->
    <div class="col-md-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i>Komposisi Jenis Kelamin
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-3">
                <canvas id="chartGender" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Kelompok Usia -->
    <div class="col-md-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-bar-chart-fill text-success me-2"></i>Distribusi Kelompok Usia
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-3">
                <canvas id="chartUsia" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Agama -->
    <div class="col-md-4">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-diagram-3-fill text-warning me-2"></i>Distribusi Agama
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-3">
                <canvas id="chartAgama" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Jenjang Pendidikan -->
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-mortarboard-fill text-primary me-2"></i>Tingkat Pendidikan Tertinggi
            </div>
            <div class="card-body p-3">
                <canvas id="chartPendidikan" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Lapangan Pekerjaan -->
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-briefcase-fill text-danger me-2"></i>Distribusi Lapangan Pekerjaan
            </div>
            <div class="card-body p-3">
                <canvas id="chartPekerjaan" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Wilayah Provinsi -->
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-geo-alt-fill text-info me-2"></i>Sebaran Penduduk per Provinsi
            </div>
            <div class="card-body p-3">
                <canvas id="chartProvinsi" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Sumber Air & Bangunan -->
    <div class="col-md-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white fw-bold py-3">
                <i class="bi bi-droplet-half text-primary me-2"></i>Sumber Air Minum Utama
            </div>
            <div class="card-body d-flex align-items-center justify-content-center p-3">
                <canvas id="chartAir" style="max-height: 280px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Gender Chart
    new Chart(document.getElementById('chartGender'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($gender_count)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($gender_count)) ?>,
                backgroundColor: ['#2563eb', '#f43f5e']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 2. Usia Chart
    new Chart(document.getElementById('chartUsia'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($kelompok_umur)) ?>,
            datasets: [{
                label: 'Jumlah Jiwa',
                data: <?= json_encode(array_values($kelompok_umur)) ?>,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 3. Agama Chart
    new Chart(document.getElementById('chartAgama'), {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_keys($agama_count)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($agama_count)) ?>,
                backgroundColor: ['#059669', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#64748b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // 4. Pendidikan Chart
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($pendidikan_count)) ?>,
            datasets: [{
                label: 'Jumlah',
                data: <?= json_encode(array_values($pendidikan_count)) ?>,
                backgroundColor: '#6366f1'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 5. Pekerjaan Chart
    new Chart(document.getElementById('chartPekerjaan'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_slice(array_keys($pekerjaan_count), 0, 8)) ?>,
            datasets: [{
                label: 'Jumlah Penduduk',
                data: <?= json_encode(array_slice(array_values($pekerjaan_count), 0, 8)) ?>,
                backgroundColor: '#0ea5e9'
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 6. Provinsi Chart
    new Chart(document.getElementById('chartProvinsi'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($provinsi_count)) ?>,
            datasets: [{
                label: 'Jumlah Jiwa',
                data: <?= json_encode(array_values($provinsi_count)) ?>,
                backgroundColor: '#f59e0b'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 7. Sumber Air Chart
    new Chart(document.getElementById('chartAir'), {
        type: 'polarArea',
        data: {
            labels: <?= json_encode(array_keys($air_count)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($air_count)) ?>,
                backgroundColor: ['#06b6d4', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
