<?php
// data.php - Menampilkan Daftar Data Sensus Penduduk
require_once __DIR__ . '/config.php';

$all_records = read_csv_records();

// Ambil parameter pencarian dan filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_prov = isset($_GET['provinsi']) ? trim($_GET['provinsi']) : '';
$filter_jk = isset($_GET['jk']) ? trim($_GET['jk']) : '';

// Lakukan filtering data
$filtered_records = array_filter($all_records, function($r) use ($search, $filter_prov, $filter_jk) {
    if ($filter_prov !== '' && ($r['Provinsi'] ?? '') !== $filter_prov) {
        return false;
    }
    if ($filter_jk !== '' && ($r['Jenis Kelamin'] ?? '') !== $filter_jk) {
        return false;
    }
    if ($search !== '') {
        $haystack = strtolower(
            ($r['Nama Lengkap'] ?? '') . ' ' . 
            ($r['NIK'] ?? '') . ' ' . 
            ($r['No KK'] ?? '') . ' ' . 
            ($r['Alamat Domisili'] ?? '') . ' ' .
            ($r['Kecamatan'] ?? '') . ' ' .
            ($r['Kabupaten/Kota'] ?? '')
        );
        if (strpos($haystack, strtolower($search)) === false) {
            return false;
        }
    }
    return true;
});

// Urutkan dari data terbaru (opsional atau sesuai No)
$filtered_records = array_values($filtered_records);
$total_filtered = count($filtered_records);

// Pagination
$per_page = 15;
$total_pages = max(1, ceil($total_filtered / $per_page));
$page = isset($_GET['page']) ? max(1, min((int)$_GET['page'], $total_pages)) : 1;
$offset = ($page - 1) * $per_page;
$display_records = array_slice($filtered_records, $offset, $per_page);

require_once __DIR__ . '/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-primary">
            <i class="bi bi-table me-2"></i>Tabel Data Sensus Penduduk Indonesia
        </h4>
        <p class="text-muted mb-0 small">
            Menampilkan data dari file <code>sensus_penduduk_indonesia_2024_dummy.csv</code>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="export.php" class="btn btn-outline-success btn-sm btn-action">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor CSV
        </a>
        <a href="index.php" class="btn btn-primary btn-sm btn-action">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data Sensus
        </a>
    </div>
</div>

<?php if (isset($_SESSION['flash_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
            <div><?= $_SESSION['flash_msg'] ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_msg']); ?>
<?php endif; ?>

<!-- Filter & Search Card -->
<div class="card card-custom mb-4">
    <div class="card-body p-3">
        <form method="GET" action="data.php" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" 
                           placeholder="Cari Nama, NIK, No KK, Alamat..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="provinsi" class="form-select">
                    <option value="">-- Semua Provinsi --</option>
                    <?php 
                    $provinces = array_unique(array_filter(array_column($all_records, 'Provinsi')));
                    sort($provinces);
                    foreach ($provinces as $p): ?>
                        <option value="<?= htmlspecialchars($p) ?>" <?= ($filter_prov === $p) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="jk" class="form-select">
                    <option value="">-- Semua Gender --</option>
                    <option value="Laki-laki" <?= ($filter_jk === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= ($filter_jk === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <?php if ($search !== '' || $filter_prov !== '' || $filter_jk !== ''): ?>
                    <a href="data.php" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card card-custom">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-secondary small">
            Menampilkan <strong><?= count($display_records) ?></strong> dari <strong><?= $total_filtered ?></strong> hasil pencarian (Total data: <?= count($all_records) ?>)
        </span>
        <span class="badge bg-primary-subtle text-primary border">Halaman <?= $page ?> dari <?= $total_pages ?></span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="table-light">
                <tr class="text-uppercase text-secondary" style="font-size: 0.78rem;">
                    <th style="width: 50px;">No</th>
                    <th>Identitas (Nama / NIK)</th>
                    <th>No KK</th>
                    <th>JK / Usia</th>
                    <th>Hub. Keluarga</th>
                    <th>Pekerjaan & Pendapatan</th>
                    <th>Domisili Wilayah</th>
                    <th class="text-center" style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($display_records)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data kependudukan yang sesuai dengan kriteria pencarian.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($display_records as $idx => $r): ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= htmlspecialchars($r['No'] ?? ($offset + $idx + 1)) ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($r['Nama Lengkap'] ?? '-') ?></div>
                                <small class="text-muted font-monospace">NIK: <?= htmlspecialchars($r['NIK'] ?? '-') ?></small>
                            </td>
                            <td>
                                <span class="font-monospace text-secondary"><?= htmlspecialchars($r['No KK'] ?? '-') ?></span>
                            </td>
                            <td>
                                <?php if (($r['Jenis Kelamin'] ?? '') === 'Laki-laki'): ?>
                                    <span class="badge bg-primary-subtle text-primary"><i class="bi bi-gender-male me-1"></i>L</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-gender-female me-1"></i>P</span>
                                <?php endif; ?>
                                <span class="ms-1 fw-semibold"><?= htmlspecialchars($r['Umur (Tahun)'] ?? '-') ?> th</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($r['Hubungan Keluarga'] ?? '-') ?></span>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($r['Lapangan Pekerjaan'] ?? '-') ?></div>
                                <small class="text-success fw-semibold"><?= format_rupiah($r['Estimasi Pendapatan Bulanan (Rp)'] ?? '0') ?></small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars(($r['Kelurahan/Desa'] ?? '') . ', ' . ($r['Kecamatan'] ?? '') . ', ' . ($r['Kabupaten/Kota'] ?? '')) ?>">
                                    <?= htmlspecialchars($r['Kelurahan/Desa'] ?? '') ?>, <?= htmlspecialchars($r['Kecamatan'] ?? '') ?>
                                </div>
                                <small class="text-muted"><?= htmlspecialchars($r['Kabupaten/Kota'] ?? '') ?>, <?= htmlspecialchars($r['Provinsi'] ?? '') ?></small>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-primary btn-sm btn-detail" 
                                        data-record='<?= json_encode($r, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                    <i class="bi bi-eye-fill me-1"></i> Detail
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    <?php if ($total_pages > 1): ?>
        <div class="card-footer bg-white py-3">
            <nav aria-label="Navigasi Halaman">
                <ul class="pagination pagination-sm justify-content-center mb-0 gap-1">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=1&q=<?= urlencode($search) ?>&provinsi=<?= urlencode($filter_prov) ?>&jk=<?= urlencode($filter_jk) ?>">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&provinsi=<?= urlencode($filter_prov) ?>&jk=<?= urlencode($filter_jk) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>

                    <?php
                    $start_p = max(1, $page - 2);
                    $end_p = min($total_pages, $page + 2);
                    for ($p = $start_p; $p <= $end_p; $p++): ?>
                        <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&provinsi=<?= urlencode($filter_prov) ?>&jk=<?= urlencode($filter_jk) ?>">
                                <?= $p ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&provinsi=<?= urlencode($filter_prov) ?>&jk=<?= urlencode($filter_jk) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $total_pages ?>&q=<?= urlencode($search) ?>&provinsi=<?= urlencode($filter_prov) ?>&jk=<?= urlencode($filter_jk) ?>">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Lengkap 28 Kolom Sensus -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">
                    <i class="bi bi-person-lines-fill me-2"></i>Rincian Lengkap Data Sensus Penduduk
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalDetailContent">
                <!-- Diisi secara dinamis melalui JavaScript -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal Detail Handler
    document.querySelectorAll('.btn-detail').forEach(button => {
        button.addEventListener('click', function() {
            const data = JSON.parse(this.getAttribute('data-record'));
            
            let html = `
                <div class="card mb-3 border-0 bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 fw-bold text-primary">${data['Nama Lengkap'] || '-'}</h4>
                                <span class="text-muted font-monospace">NIK: ${data['NIK'] || '-'} &bull; No KK: ${data['No KK'] || '-'}</span>
                            </div>
                            <span class="badge bg-primary fs-6">No. Urut: ${data['No'] || '-'}</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- 1. IDENTITAS -->
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header bg-white fw-bold text-dark small py-2">
                                <i class="bi bi-person-vcard text-primary me-1"></i> I. IDENTITAS & KARAKTERISTIK
                            </div>
                            <div class="card-body p-3 small">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:40%">Hub. Keluarga</td><td class="fw-semibold">: ${data['Hubungan Keluarga'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Jenis Kelamin</td><td class="fw-semibold">: ${data['Jenis Kelamin'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Tempat Lahir</td><td class="fw-semibold">: ${data['Tempat Lahir'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Tanggal Lahir</td><td class="fw-semibold">: ${data['Tanggal Lahir'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Umur</td><td class="fw-semibold">: ${data['Umur (Tahun)'] || '-'} Tahun</td></tr>
                                    <tr><td class="text-muted">Agama</td><td class="fw-semibold">: ${data['Agama'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Status Kawin</td><td class="fw-semibold">: ${data['Status Perkawinan'] || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 2. PENDIDIKAN & PEKERJAAN -->
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header bg-white fw-bold text-dark small py-2">
                                <i class="bi bi-briefcase text-primary me-1"></i> II. PENDIDIKAN & EKONOMI
                            </div>
                            <div class="card-body p-3 small">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:40%">Partisipasi Sekolah</td><td class="fw-semibold">: ${data['Partisipasi Sekolah'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Pendidikan Tertinggi</td><td class="fw-semibold">: ${data['Pendidikan Tertinggi'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Kegiatan Utama</td><td class="fw-semibold">: ${data['Kegiatan Utama'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Lapangan Kerja</td><td class="fw-semibold">: ${data['Lapangan Pekerjaan'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Est. Pendapatan</td><td class="fw-bold text-success">: Rp ${new Intl.NumberFormat('id-ID').format(data['Estimasi Pendapatan Bulanan (Rp)'] || 0)}</td></tr>
                                    <tr><td class="text-muted">Jaminan Kesehatan</td><td class="fw-semibold">: ${data['Jaminan Kesehatan'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Disabilitas</td><td class="fw-semibold">: ${data['Disabilitas'] || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 3. WILAYAH & DOMISILI -->
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header bg-white fw-bold text-dark small py-2">
                                <i class="bi bi-geo-alt text-primary me-1"></i> III. DOMISILI & ALAMAT
                            </div>
                            <div class="card-body p-3 small">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:40%">Provinsi</td><td class="fw-semibold">: ${data['Provinsi'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Kabupaten / Kota</td><td class="fw-semibold">: ${data['Kabupaten/Kota'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Kecamatan</td><td class="fw-semibold">: ${data['Kecamatan'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Kelurahan / Desa</td><td class="fw-semibold">: ${data['Kelurahan/Desa'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Alamat Lengkap</td><td class="fw-semibold">: ${data['Alamat Domisili'] || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 4. PERUMAHAN & SANITASI -->
                    <div class="col-md-6">
                        <div class="card h-100 border">
                            <div class="card-header bg-white fw-bold text-dark small py-2">
                                <i class="bi bi-house-door text-primary me-1"></i> IV. KONDISI HUNIAN & SANITASI
                            </div>
                            <div class="card-body p-3 small">
                                <table class="table table-sm table-borderless mb-0">
                                    <tr><td class="text-muted" style="width:40%">Status Kepemilikan</td><td class="fw-semibold">: ${data['Status Kepemilikan Bangunan'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Luas Lantai</td><td class="fw-semibold">: ${data['Luas Lantai (m2)'] || '-'} m²</td></tr>
                                    <tr><td class="text-muted">Sumber Air Minum</td><td class="fw-semibold">: ${data['Sumber Air Minum'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Daya Listrik</td><td class="fw-semibold">: ${data['Daya Listrik'] || '-'}</td></tr>
                                    <tr><td class="text-muted">Fasilitas Sanitasi</td><td class="fw-semibold">: ${data['Fasilitas Sanitasi'] || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('modalDetailContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
            modal.show();
        });
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
