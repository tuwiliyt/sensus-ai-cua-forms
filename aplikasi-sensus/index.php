<?php
// index.php - Formulir Utama Sensus Penduduk Indonesia 2024
require_once __DIR__ . '/config.php';

$records = read_csv_records();
$total_records = count($records);
$unique_kk = count(array_unique(array_filter(array_column($records, 'No KK'))));
$last_record = !empty($records) ? end($records) : null;

require_once __DIR__ . '/header.php';
?>

<!-- Banner Ringkasan Data -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card bg-blue d-flex align-items-center justify-content-between">
            <div>
                <span class="text-white-50 text-uppercase fw-semibold small">Total Warga Terdata</span>
                <h2 class="mb-0 fw-bold mt-1"><?= number_format($total_records, 0, ',', '.') ?></h2>
                <small class="text-white-50">Jiwa penduduk</small>
            </div>
            <div class="fs-1 text-white-50"><i class="bi bi-people-fill"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card bg-emerald d-flex align-items-center justify-content-between">
            <div>
                <span class="text-white-50 text-uppercase fw-semibold small">Kartu Keluarga (KK)</span>
                <h2 class="mb-0 fw-bold mt-1"><?= number_format($unique_kk, 0, ',', '.') ?></h2>
                <small class="text-white-50">Kepala keluarga terdaftar</small>
            </div>
            <div class="fs-1 text-white-50"><i class="bi bi-houses-fill"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card bg-purple d-flex align-items-center justify-content-between">
            <div>
                <span class="text-white-50 text-uppercase fw-semibold small">Jumlah Kolom Sensus</span>
                <h2 class="mb-0 fw-bold mt-1">28</h2>
                <small class="text-white-50">Atribut data lengkap</small>
            </div>
            <div class="fs-1 text-white-50"><i class="bi bi-ui-checks-grid"></i></div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card bg-amber d-flex align-items-center justify-content-between">
            <div>
                <span class="text-white-50 text-uppercase fw-semibold small">Data Terakhir Masuk</span>
                <h5 class="mb-0 fw-bold mt-1 text-truncate" style="max-width: 170px;">
                    <?= $last_record ? htmlspecialchars($last_record['Nama Lengkap']) : 'Belum Ada' ?>
                </h5>
                <small class="text-white-50">
                    <?= $last_record ? htmlspecialchars($last_record['Kabupaten/Kota']) : '-' ?>
                </small>
            </div>
            <div class="fs-1 text-white-50"><i class="bi bi-clock-history"></i></div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['flash_msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
            <div>
                <strong>Berhasil!</strong> <?= $_SESSION['flash_msg'] ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_msg']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
            <div>
                <strong>Perhatian!</strong> <?= $_SESSION['flash_error'] ?>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Formulir Input Utama -->
<div class="card card-custom">
    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h4 class="card-title fw-bold mb-1 text-primary">
                <i class="bi bi-journal-text me-2"></i>Formulir Pendataan Sensus Penduduk 2024
            </h4>
            <p class="text-muted mb-0 small">
                Silakan isi seluruh kolom formulir sensus di bawah ini. Bidang bertanda (<span class="required-star">*</span>) wajib diisi.
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="data.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-list-ul me-1"></i> Lihat Data Tersimpan (<?= $total_records ?>)
            </a>
            <button type="reset" form="formSensus" class="btn btn-light btn-sm text-secondary border">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form
            </button>
        </div>
    </div>

    <div class="card-body p-4">
        <form id="formSensus" action="proses_simpan.php" method="POST">

            <!-- BAGIAN 1: IDENTITAS & HUBUNGAN KELUARGA -->
            <div class="section-header">
                <h5>1. Identitas Kependudukan & Hubungan Keluarga</h5>
                <small>Informasi Kartu Keluarga, NIK, dan identitas dasar penduduk</small>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <label for="no_kk" class="form-label">No KK <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        <input type="text" class="form-control font-monospace" id="no_kk" name="no_kk" 
                               placeholder="16 digit No KK" maxlength="16" required
                               pattern="[0-9]{16}" title="Harus 16 digit angka">
                    </div>
                    <div class="d-flex justify-content-between helper-text mt-1">
                        <span>Nomor Kartu Keluarga</span>
                        <span id="kk_counter" class="text-muted">0/16</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="nik" class="form-label">NIK <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                        <input type="text" class="form-control font-monospace" id="nik" name="nik" 
                               placeholder="16 digit NIK" maxlength="16" required
                               pattern="[0-9]{16}" title="Harus 16 digit angka">
                    </div>
                    <div class="d-flex justify-content-between helper-text mt-1">
                        <span>Nomor Induk Kependudukan</span>
                        <span id="nik_counter" class="text-muted">0/16</span>
                    </div>
                </div>

                <div class="col-md-8 col-lg-4">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                               placeholder="Nama lengkap sesuai KTP" required>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label for="hubungan_keluarga" class="form-label">Hubungan Keluarga <span class="required-star">*</span></label>
                    <select class="form-select" id="hubungan_keluarga" name="hubungan_keluarga" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Hubungan Keluarga'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- BAGIAN 2: DATA PRIBADI & KELAHIRAN -->
            <div class="section-header">
                <h5>2. Karakteristik Individu & Kelahiran</h5>
                <small>Jenis kelamin, tempat dan tanggal lahir, umur, agama, dan status perkawinan</small>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-2">
                    <label class="form-label d-block">Jenis Kelamin <span class="required-star">*</span></label>
                    <div class="d-flex gap-3 pt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_l" value="Laki-laki" required>
                            <label class="form-check-label" for="jk_l">Laki-laki</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_p" value="Perempuan">
                            <label class="form-check-label" for="jk_p">Perempuan</label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                           placeholder="Contoh: Bandung, Surabaya, Jakarta..." required list="listTempatLahir">
                    <datalist id="listTempatLahir">
                        <option value="Bandung">
                        <option value="Jakarta Selatan">
                        <option value="Surabaya">
                        <option value="Semarang">
                        <option value="Medan">
                        <option value="Padang">
                        <option value="Denpasar">
                        <option value="Makassar">
                        <option value="Balikpapan">
                        <option value="Bogor">
                    </datalist>
                </div>

                <div class="col-md-4 col-lg-3">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="required-star">*</span></label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                    <div class="helper-text mt-1">Format: Tanggal / Bulan / Tahun</div>
                </div>

                <div class="col-md-2 col-lg-2">
                    <label for="umur" class="form-label">Umur (Tahun) <span class="required-star">*</span></label>
                    <input type="number" class="form-control text-center fw-bold" id="umur" name="umur" 
                           min="0" max="130" placeholder="0" required>
                    <div class="helper-text mt-1 text-center">Hitung otomatis</div>
                </div>

                <div class="col-md-3 col-lg-2">
                    <label for="agama" class="form-label">Agama <span class="required-star">*</span></label>
                    <select class="form-select" id="agama" name="agama" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Agama'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 col-lg-3">
                    <label for="status_perkawinan" class="form-label">Status Perkawinan <span class="required-star">*</span></label>
                    <select class="form-select" id="status_perkawinan" name="status_perkawinan" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Status Perkawinan'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- BAGIAN 3: PENDIDIKAN & PEKERJAAN -->
            <div class="section-header">
                <h5>3. Pendidikan, Pekerjaan & Ekonomi</h5>
                <small>Partisipasi sekolah, jenjang pendidikan, pekerjaan, dan estimasi pendapatan</small>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <label for="partisipasi_sekolah" class="form-label">Partisipasi Sekolah <span class="required-star">*</span></label>
                    <select class="form-select" id="partisipasi_sekolah" name="partisipasi_sekolah" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Partisipasi Sekolah'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="pendidikan_tertinggi" class="form-label">Pendidikan Tertinggi <span class="required-star">*</span></label>
                    <select class="form-select" id="pendidikan_tertinggi" name="pendidikan_tertinggi" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Pendidikan Tertinggi'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="kegiatan_utama" class="form-label">Kegiatan Utama <span class="required-star">*</span></label>
                    <select class="form-select" id="kegiatan_utama" name="kegiatan_utama" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Kegiatan Utama'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="lapangan_pekerjaan" class="form-label">Lapangan Pekerjaan <span class="required-star">*</span></label>
                    <select class="form-select" id="lapangan_pekerjaan" name="lapangan_pekerjaan" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Lapangan Pekerjaan'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="estimasi_pendapatan" class="form-label">Estimasi Pendapatan Bulanan (Rp) <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="estimasi_pendapatan" name="estimasi_pendapatan" 
                               placeholder="Contoh: 3500000" min="0" step="50000" value="0" required>
                    </div>
                    <div class="helper-text mt-1" id="preview_rupiah">Rp 0</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="jaminan_kesehatan" class="form-label">Jaminan Kesehatan <span class="required-star">*</span></label>
                    <select class="form-select" id="jaminan_kesehatan" name="jaminan_kesehatan" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Jaminan Kesehatan'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label for="disabilitas" class="form-label">Disabilitas <span class="required-star">*</span></label>
                    <select class="form-select" id="disabilitas" name="disabilitas" required>
                        <?php foreach ($MASTER_OPTIONS['Disabilitas'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= ($opt == 'Tidak') ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- BAGIAN 4: WILAYAH & ALAMAT DOMISILI -->
            <div class="section-header">
                <h5>4. Wilayah & Alamat Domisili</h5>
                <small>Hierarki administratif domisili tempat tinggal penduduk</small>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <label for="provinsi" class="form-label">Provinsi <span class="required-star">*</span></label>
                    <select class="form-select" id="provinsi" name="provinsi" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <?php foreach (array_keys($MASTER_WILAYAH) as $prov): ?>
                            <option value="<?= htmlspecialchars($prov) ?>"><?= htmlspecialchars($prov) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="kabupaten_kota" class="form-label">Kabupaten/Kota <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="kabupaten_kota" name="kabupaten_kota" 
                           placeholder="Pilih atau ketik Kota/Kabupaten" required list="listKabKota">
                    <datalist id="listKabKota"></datalist>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="kecamatan" class="form-label">Kecamatan <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="kecamatan" name="kecamatan" 
                           placeholder="Pilih atau ketik Kecamatan" required list="listKecamatan">
                    <datalist id="listKecamatan"></datalist>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label for="kelurahan_desa" class="form-label">Kelurahan/Desa <span class="required-star">*</span></label>
                    <input type="text" class="form-control" id="kelurahan_desa" name="kelurahan_desa" 
                           placeholder="Pilih atau ketik Kelurahan/Desa" required list="listKelurahan">
                    <datalist id="listKelurahan"></datalist>
                </div>

                <div class="col-12">
                    <label for="alamat_domisili" class="form-label">Alamat Domisili <span class="required-star">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control" id="alamat_domisili" name="alamat_domisili" 
                               placeholder="Nama Jalan, Nomor Rumah, RT/RW (Contoh: Jl. Sudirman No. 29, RT 001/RW 005)" required>
                    </div>
                </div>
            </div>

            <!-- BAGIAN 5: KONDISI PERUMAHAN & SANITASI -->
            <div class="section-header">
                <h5>5. Kondisi Bangunan Tempat Tinggal & Fasilitas Sanitasi</h5>
                <small>Karakteristik fisik hunian, fasilitas air, listrik, dan jamban</small>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <label for="status_kepemilikan_bangunan" class="form-label">Status Kepemilikan Bangunan <span class="required-star">*</span></label>
                    <select class="form-select" id="status_kepemilikan_bangunan" name="status_kepemilikan_bangunan" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Status Kepemilikan Bangunan'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 col-lg-2">
                    <label for="luas_lantai" class="form-label">Luas Lantai (m2) <span class="required-star">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="luas_lantai" name="luas_lantai" 
                               placeholder="Contoh: 45" min="1" max="1000" required>
                        <span class="input-group-text">m²</span>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <label for="sumber_air_minum" class="form-label">Sumber Air Minum <span class="required-star">*</span></label>
                    <select class="form-select" id="sumber_air_minum" name="sumber_air_minum" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Sumber Air Minum'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label for="daya_listrik" class="form-label">Daya Listrik <span class="required-star">*</span></label>
                    <select class="form-select" id="daya_listrik" name="daya_listrik" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Daya Listrik'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4 col-lg-2">
                    <label for="fasilitas_sanitasi" class="form-label">Fasilitas Sanitasi <span class="required-star">*</span></label>
                    <select class="form-select" id="fasilitas_sanitasi" name="fasilitas_sanitasi" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($MASTER_OPTIONS['Fasilitas Sanitasi'] as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>"><?= htmlspecialchars($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <hr class="my-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    <i class="bi bi-shield-check text-success me-1"></i> Data yang diinput akan tersimpan secara terstruktur ke dalam file spreadsheet sensus CSV.
                </div>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-circle me-1"></i> Bersihkan
                    </button>
                    <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                        <i class="bi bi-cloud-arrow-up-fill me-2"></i> Simpan Data Sensus
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- JavaScript Interaktivitas Formulir -->
<script>
    // Data Wilayah JSON
    const dataWilayah = <?= json_encode($MASTER_WILAYAH) ?>;

    // Elemen Input
    const noKkInput = document.getElementById('no_kk');
    const nikInput = document.getElementById('nik');
    const kkCounter = document.getElementById('kk_counter');
    const nikCounter = document.getElementById('nik_counter');
    const tglLahirInput = document.getElementById('tanggal_lahir');
    const umurInput = document.getElementById('umur');
    const pendapatanInput = document.getElementById('estimasi_pendapatan');
    const previewRupiah = document.getElementById('preview_rupiah');
    
    // Wilayah elements
    const provSelect = document.getElementById('provinsi');
    const kabInput = document.getElementById('kabupaten_kota');
    const kecInput = document.getElementById('kecamatan');
    const kelInput = document.getElementById('kelurahan_desa');
    const listKab = document.getElementById('listKabKota');
    const listKec = document.getElementById('listKecamatan');
    const listKel = document.getElementById('listKelurahan');

    // 1. Counter Karakter No KK & NIK
    function updateCounter(input, counterEl) {
        // filter hanya angka
        input.value = input.value.replace(/[^0-9]/g, '');
        const len = input.value.length;
        counterEl.textContent = `${len}/16`;
        if (len === 16) {
            counterEl.className = 'text-success fw-bold';
        } else {
            counterEl.className = 'text-muted';
        }
    }

    noKkInput.addEventListener('input', () => updateCounter(noKkInput, kkCounter));
    nikInput.addEventListener('input', () => updateCounter(nikInput, nikCounter));

    // 2. Hitung Umur Otomatis Berdasarkan Tanggal Lahir
    tglLahirInput.addEventListener('change', function() {
        if (!this.value) return;
        const birthDate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        umurInput.value = age >= 0 ? age : 0;
    });

    // 3. Format Rupiah Real-time Preview
    pendapatanInput.addEventListener('input', function() {
        let val = parseInt(this.value, 10);
        if (isNaN(val) || val < 0) val = 0;
        previewRupiah.textContent = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(val);
    });

    // 4. Cascade Wilayah
    provSelect.addEventListener('change', function() {
        const prov = this.value;
        listKab.innerHTML = '';
        listKec.innerHTML = '';
        listKel.innerHTML = '';
        kabInput.value = '';
        kecInput.value = '';
        kelInput.value = '';

        if (dataWilayah[prov]) {
            const kabs = Object.keys(dataWilayah[prov]);
            kabs.forEach(k => {
                const opt = document.createElement('option');
                opt.value = k;
                listKab.appendChild(opt);
            });
            if (kabs.length === 1) {
                kabInput.value = kabs[0];
                populateKec(prov, kabs[0]);
            }
        }
    });

    function populateKec(prov, kab) {
        listKec.innerHTML = '';
        listKel.innerHTML = '';
        kecInput.value = '';
        kelInput.value = '';

        if (dataWilayah[prov] && dataWilayah[prov][kab]) {
            const kecs = Object.keys(dataWilayah[prov][kab]);
            kecs.forEach(kc => {
                const opt = document.createElement('option');
                opt.value = kc;
                listKec.appendChild(opt);
            });
            if (kecs.length === 1) {
                kecInput.value = kecs[0];
                populateKel(prov, kab, kecs[0]);
            }
        }
    }

    function populateKel(prov, kab, kec) {
        listKel.innerHTML = '';
        kelInput.value = '';

        if (dataWilayah[prov] && dataWilayah[prov][kab] && dataWilayah[prov][kab][kec]) {
            const kels = dataWilayah[prov][kab][kec];
            kels.forEach(kl => {
                const opt = document.createElement('option');
                opt.value = kl;
                listKel.appendChild(opt);
            });
            if (kels.length === 1) {
                kelInput.value = kels[0];
            }
        }
    }

    kabInput.addEventListener('change', function() {
        const prov = provSelect.value;
        populateKec(prov, this.value);
    });

    kecInput.addEventListener('change', function() {
        const prov = provSelect.value;
        const kab = kabInput.value;
        populateKel(prov, kab, this.value);
    });

    // Validasi Sebelum Submit
    document.getElementById('formSensus').addEventListener('submit', function(e) {
        if (noKkInput.value.length !== 16) {
            alert('Nomor Kartu Keluarga (No KK) harus terdiri dari 16 digit angka!');
            noKkInput.focus();
            e.preventDefault();
            return false;
        }
        if (nikInput.value.length !== 16) {
            alert('Nomor Induk Kependudukan (NIK) harus terdiri dari 16 digit angka!');
            nikInput.focus();
            e.preventDefault();
            return false;
        }
    });
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
