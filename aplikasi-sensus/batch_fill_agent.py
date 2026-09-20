#!/usr/bin/env python3
"""
batch_fill_agent.py - Agen Otomasi Batch Form Filling Sensus Penduduk
Mengisi ratusan baris data dari spreadsheet CSV secara otomatis ke Formulir Web & Database MySQL
"""

import sys
import os
import csv
import time
import argparse
import urllib.request
import urllib.parse
from datetime import datetime

SUBMIT_URL = "http://127.0.0.1:8000/proses_simpan.php"
DEFAULT_CSV = "/root/Desktop/aplikasi-sensus/data/data_sumber_sensus_mentah.csv"

def parse_date_to_iso(date_str):
    """Konversi format tanggal DD/MM/YYYY ke YYYY-MM-DD untuk form input"""
    if not date_str:
        return ""
    date_str = str(date_str).strip()
    if "/" in date_str:
        parts = date_str.split("/")
        if len(parts) == 3:
            return f"{parts[2]}-{parts[1].zfill(2)}-{parts[0].zfill(2)}"
    return date_str

def process_csv_row_to_payload(row):
    """Memetakan kolom spreadsheet ke parameter form 28 kolom"""
    tgl_lahir = parse_date_to_iso(row.get("Tanggal Lahir", ""))
    
    # Estimasi pendapatan
    pendapatan = str(row.get("Estimasi Pendapatan Bulanan (Rp)", "0")).replace(".", "").replace(",", "").strip()
    if not pendapatan.isdigit():
        pendapatan = "0"

    # Luas lantai
    luas = str(row.get("Luas Lantai (m2)", "36")).replace(".", "").replace(",", "").strip()
    if not luas.isdigit():
        luas = "36"

    # Umur
    umur = str(row.get("Umur (Tahun)", "")).strip()
    if not umur.isdigit() and tgl_lahir:
        try:
            dob = datetime.strptime(tgl_lahir, "%Y-%m-%d")
            now = datetime.now()
            umur = str(now.year - dob.year - ((now.month, now.day) < (dob.month, dob.day)))
        except Exception:
            umur = "0"

    payload = {
        "no_kk": row.get("No KK", "").strip(),
        "nik": row.get("NIK", "").strip(),
        "nama_lengkap": row.get("Nama Lengkap", "").strip(),
        "hubungan_keluarga": row.get("Hubungan Keluarga", "Kepala Keluarga").strip(),
        "jenis_kelamin": row.get("Jenis Kelamin", "Laki-laki").strip(),
        "tempat_lahir": row.get("Tempat Lahir", "").strip(),
        "tanggal_lahir": tgl_lahir,
        "umur": umur,
        "agama": row.get("Agama", "Islam").strip(),
        "status_perkawinan": row.get("Status Perkawinan", "Belum Kawin").strip(),
        "partisipasi_sekolah": row.get("Partisipasi Sekolah", "Tidak Bersekolah Lagi").strip(),
        "pendidikan_tertinggi": row.get("Pendidikan Tertinggi", "SMA / SMK / MA").strip(),
        "kegiatan_utama": row.get("Kegiatan Utama", "Bekerja").strip(),
        "lapangan_pekerjaan": row.get("Lapangan Pekerjaan", "Karyawan Swasta").strip(),
        "estimasi_pendapatan": pendapatan,
        "jaminan_kesehatan": row.get("Jaminan Kesehatan", "Tidak Memiliki").strip(),
        "disabilitas": row.get("Disabilitas", "Tidak").strip(),
        "provinsi": row.get("Provinsi", "").strip(),
        "kabupaten_kota": row.get("Kabupaten/Kota", "").strip(),
        "kecamatan": row.get("Kecamatan", "").strip(),
        "kelurahan_desa": row.get("Kelurahan/Desa", "").strip(),
        "alamat_domisili": row.get("Alamat Domisili", "").strip(),
        "status_kepemilikan_bangunan": row.get("Status Kepemilikan Bangunan", "Milik Sendiri").strip(),
        "luas_lantai": luas,
        "sumber_air_minum": row.get("Sumber Air Minum", "PAM / Ledeng").strip(),
        "daya_listrik": row.get("Daya Listrik", "PLN 1300 VA").strip(),
        "fasilitas_sanitasi": row.get("Fasilitas Sanitasi", "Jamban Sendiri").strip()
    }
    return payload

def submit_payload(payload):
    """Kirim data payload ke endpoint proses_simpan.php"""
    data = urllib.parse.urlencode(payload).encode("utf-8")
    req = urllib.request.Request(
        SUBMIT_URL,
        data=data,
        headers={
            "User-Agent": "Cua-Batch-Agent/2.0",
            "Content-Type": "application/x-www-form-urlencoded"
        }
    )
    with urllib.request.urlopen(req, timeout=10) as resp:
        return resp.getcode() == 200

def render_progress_bar(current, total, bar_length=35, prefix='', suffix=''):
    percent = float(current) * 100 / total if total > 0 else 100
    arrow = '=' * int(percent / 100 * bar_length - 1) + '>' if percent > 0 else ''
    spaces = ' ' * (bar_length - len(arrow))
    sys.stdout.write(f"\r{prefix} [{arrow}{spaces}] {percent:5.1f}% ({current}/{total}) {suffix}")
    sys.stdout.flush()

def main():
    parser = argparse.ArgumentParser(description="Agen Otomasi Pengisian Formulir Massal (Batch Form Filling)")
    parser.add_argument("--file", default=DEFAULT_CSV, help="Jalur file spreadsheet CSV sumber")
    parser.add_argument("--limit", type=int, default=0, help="Batasi jumlah baris yang diproses (0 = semua)")
    parser.add_argument("--delay", type=float, default=0.02, help="Jeda waktu antar submit dalam detik")
    args = parser.parse_args()

    if not os.path.exists(args.file):
        print(f"[!] Berkas sumber CSV tidak ditemukan: {args.file}")
        sys.exit(1)

    print("=" * 70)
    print("      AGEN OTOMASI PENGISIAN FORMULIR SENSUS MASSAL (BATCH FILL)     ")
    print("=" * 70)
    print(f" Berkas Sumber CSV : {args.file}")
    print(f" Target Endpoint   : {SUBMIT_URL}")
    print(f" Jeda Antar Data   : {args.delay} detik")
    print("-" * 70)

    # Baca file CSV
    with open(args.file, "r", encoding="utf-8-sig") as f:
        reader = csv.DictReader(f)
        all_rows = list(reader)

    if args.limit > 0:
        all_rows = all_rows[:args.limit]

    total_rows = len(all_rows)
    print(f" Total baris yang siap diproses: {total_rows} data\n")

    start_time = time.time()
    success_count = 0
    fail_count = 0

    for i, row in enumerate(all_rows, 1):
        nama = row.get("Nama Lengkap", f"Baris {i}")
        nik = row.get("NIK", "-")
        try:
            payload = process_csv_row_to_payload(row)
            ok = submit_payload(payload)
            if ok:
                success_count += 1
            else:
                fail_count += 1
        except Exception as e:
            fail_count += 1

        elapsed = time.time() - start_time
        speed = i / elapsed if elapsed > 0 else 0
        render_progress_bar(
            i, total_rows,
            prefix="Mengisi Form:",
            suffix=f"[{speed:.1f} data/dtk]"
        )

        if args.delay > 0:
            time.sleep(args.delay)

    total_time = time.time() - start_time
    print("\n" + "=" * 70)
    print("                   RINGKASAN EKSEKUSI MASSAL                         ")
    print("=" * 70)
    print(f" [✓] Total Berhasil Terisi : {success_count} data")
    print(f" [!] Total Gagal           : {fail_count} data")
    print(f" [⏱] Total Waktu Tempuh   : {total_time:.2f} detik")
    print(f" [⚡] Kecepatan Rata-rata  : {success_count / total_time:.1f} data/detik")
    print("-" * 70)
    print(" Status Terkini:")
    print(" 1. Cek Data di Web        : http://127.0.0.1:8000/data.php")
    print(" 2. Cek Statistik          : http://127.0.0.1:8000/statistik.php")
    print(" 3. Cek Database MySQL     : http://127.0.0.1:8000/phpmyadmin/")
    print("=" * 70)

if __name__ == "__main__":
    main()
