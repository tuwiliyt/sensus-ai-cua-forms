<?php
// tutorial.php - Halaman Panduan & Tutorial Lengkap CUA-S1-FORMS
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-custom p-4 mb-4 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h3 class="fw-bold text-primary mb-1">
                        <i class="bi bi-book-half me-2"></i>Panduan Lengkap Implementasi AI CUA-S1-FORMS
                    </h3>
                    <p class="text-muted mb-0 small">
                        Otomasi Pengisian Formulir Sensus Penduduk Menggunakan Model AI "System One" (706k Parameter, 2.8 MB)
                    </p>
                </div>
                <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fs-6">
                    <i class="bi bi-cpu-fill me-1"></i> Model Terpasang Lokal
                </span>
            </div>

            <!-- Referensi Materi -->
            <div class="alert alert-info border-info d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-youtube fs-1 text-danger"></i>
                <div>
                    <strong>Referensi & Sumber Belajar:</strong><br>
                    &bull; Video Tutorial: <a href="https://www.youtube.com/watch?v=Rzgd-y3mCPs" target="_blank" class="fw-semibold text-decoration-underline">CUA S1 Forms: Jev-Like for GUI Form Filling Model Locally (YouTube)</a><br>
                    &bull; Repositori Hugging Face: <a href="https://huggingface.co/cua-ai/cua-s1-forms" target="_blank" class="fw-semibold text-decoration-underline">cua-ai/cua-s1-forms (Model Checkpoint & Dataset)</a><br>
                    &bull; Repositori GitHub: <a href="https://github.com/trycua/cua/tree/main/libs/cua-s1" target="_blank" class="fw-semibold text-decoration-underline">trycua/cua (libs/cua-s1 Source Code)</a>
                </div>
            </div>

            <!-- Bagian 1: Konsep -->
            <h5 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">
                1. Konsep Dasar CUA-S1-FORMS
            </h5>
            <p class="text-secondary small">
                CUA-S1-FORMS adalah model Transformer spesialis berukuran mini (hanya <strong>2.8 MB</strong>) yang dilatih khusus untuk mengambil keputusan pengisian form secara instan.
            </p>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border h-100">
                        <h6 class="fw-bold text-danger"><i class="bi bi-x-circle me-1"></i> Mengapa Bukan LLM Biasa?</h6>
                        <p class="small text-muted mb-0">
                            LLM generatif (seperti GPT-4 / Claude) bekerja lambat karena menghasilkan kata per kata (*token-by-token*) dan membutuhkan GPU besar atau biaya API per panggilan.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border h-100">
                        <h6 class="fw-bold text-success"><i class="bi bi-check-circle me-1"></i> Keunggulan CUA-S1 "System One"</h6>
                        <p class="small text-muted mb-0">
                            CUA-S1 bekerja sebagai <em>Option Scorer</em>. Dalam satu kali putaran (*single forward pass*), model langsung menilai probabilitas aksi (<code>FILL</code>, <code>CHECK</code>, <code>CLICK</code>, atau <code>SKIP</code>) dalam hitungan milidetik secara lokal di CPU.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Hasil Uji Coba Model AI -->
            <h5 class="fw-bold text-dark border-start border-success border-4 ps-2 mb-3">
                2. Hasil Uji Coba Inferensi Nyata pada Aplikasi Sensus
            </h5>
            <p class="text-secondary small">
                Pengujian nyata telah dijalankan pada data warga <em>Farhan Alamsyah, M.T.</em> terhadap formulir sensus kita:
            </p>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm align-middle small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Elemen UI Form</th>
                            <th>Tipe Aksi</th>
                            <th>Nilai yang Diputuskan AI</th>
                            <th>Tingkat Keyakinan (Confidence)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>Policy # (No KK)</code></td>
                            <td><span class="badge bg-primary">FILL</span></td>
                            <td>3273010106240003</td>
                            <td><strong class="text-success">100.0%</strong></td>
                        </tr>
                        <tr>
                            <td><code>Full name</code></td>
                            <td><span class="badge bg-primary">FILL</span></td>
                            <td>Farhan Alamsyah, M.T.</td>
                            <td><strong class="text-success">86.3%</strong></td>
                        </tr>
                        <tr>
                            <td><code>Date of birth</code></td>
                            <td><span class="badge bg-primary">FILL</span></td>
                            <td>1996-05-19</td>
                            <td><strong class="text-success">100.0%</strong></td>
                        </tr>
                        <tr>
                            <td><code>City</code></td>
                            <td><span class="badge bg-primary">FILL</span></td>
                            <td>Kota Bandung</td>
                            <td><strong class="text-success">99.9%</strong></td>
                        </tr>
                        <tr>
                            <td><code>Street address</code></td>
                            <td><span class="badge bg-primary">FILL</span></td>
                            <td>Jl. Sangkuriang Barat No. 12, RT 002/RW 007</td>
                            <td><strong class="text-success">100.0%</strong></td>
                        </tr>
                        <tr>
                            <td><code>Insurance provider</code></td>
                            <td><span class="badge bg-primary">FILL</span></td>
                            <td>BPJS Non-PBI / Mandiri</td>
                            <td><strong class="text-success">99.4%</strong></td>
                        </tr>
                        <tr>
                            <td><code>Submit Button</code></td>
                            <td><span class="badge bg-success">CLICK</span></td>
                            <td>Eksekusi Pengiriman Form</td>
                            <td><strong class="text-success">100.0%</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bagian 3: Cara Menjalankan di Terminal -->
            <h5 class="fw-bold text-dark border-start border-warning border-4 ps-2 mb-3">
                3. Perintah Eksekusi Script yang Telah Tersedia
            </h5>

            <div class="mb-4">
                <h6 class="fw-bold">A. Uji Coba 1 Record dengan Output Rincian Persentase AI:</h6>
                <div class="bg-dark text-white p-3 rounded font-monospace small mb-2">
                    python3 /root/Desktop/aplikasi-sensus/run_cua_model_test.py
                </div>
                <small class="text-muted">Menampilkan kalkulasi probabilitas AI untuk tiap kolom formulir secara transparan.</small>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold">B. Otomasi Pengisian Ratusan Baris Spreadsheet dengan Model AI:</h6>
                <div class="bg-dark text-white p-3 rounded font-monospace small mb-2">
                    # Memproses 10 baris pertama<br>
                    python3 /root/Desktop/aplikasi-sensus/batch_cua_ai_filler.py --limit 10<br><br>
                    # Memproses 50 baris<br>
                    python3 /root/Desktop/aplikasi-sensus/batch_cua_ai_filler.py --limit 50<br><br>
                    # Memproses SELURUH baris data di spreadsheet sekaligus<br>
                    python3 /root/Desktop/aplikasi-sensus/batch_cua_ai_filler.py --limit 0
                </div>
                <small class="text-muted">Membaca file CSV baris demi baris, mengevaluasi dengan model AI CUA-S1, dan menyimpannya langsung ke database MySQL dan CSV.</small>
            </div>

            <div class="mb-4">
                <h6 class="fw-bold">C. Import Cepat Tanpa Terminal:</h6>
                <p class="small text-muted">
                    Buka menu <a href="import.php" class="fw-bold">Import CSV</a> pada navbar web untuk mengunggah file CSV atau klik tombol import preset secara visual.
                </p>
            </div>

            <!-- Bagian 4: Verifikasi -->
            <h5 class="fw-bold text-dark border-start border-info border-4 ps-2 mb-3">
                4. Tempat Memeriksa Data yang Tersimpan
            </h5>
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center">
                        <i class="bi bi-table fs-2 text-primary d-block mb-2"></i>
                        <a href="data.php" class="fw-bold d-block mb-1">Tabel Data Web</a>
                        <small class="text-muted">Fitur pencarian, filter wilayah, dan modal rincian 28 atribut.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center">
                        <i class="bi bi-database-fill-gear fs-2 text-info d-block mb-2"></i>
                        <a href="phpmyadmin/" target="_blank" class="fw-bold d-block mb-1">phpMyAdmin GUI</a>
                        <small class="text-muted">Database <code>db_sensus</code> tabel <code>penduduk</code>.</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center">
                        <i class="bi bi-file-earmark-excel-fill fs-2 text-success d-block mb-2"></i>
                        <span class="fw-bold d-block mb-1">Gnumeric Spreadsheet</span>
                        <small class="text-muted">Buka file CSV di Desktop via shortcut Gnumeric.</small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
