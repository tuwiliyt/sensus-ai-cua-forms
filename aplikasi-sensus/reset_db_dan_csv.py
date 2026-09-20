#!/usr/bin/env python3
"""
reset_db_dan_csv.py
Script untuk mengosongkan tabel penduduk di database MySQL db_sensus
dan mereset berkas CSV aktif di Downloads agar hanya menyisakan baris header.
"""
import subprocess
import os

print("=" * 60)
print("   PEMBERSIHAN DATABASE & BERKAS CSV SENSUS (RESET TOTAL)   ")
print("=" * 60)

# 1. Truncate tabel penduduk di MySQL
try:
    subprocess.run(["mysql", "-e", "TRUNCATE TABLE db_sensus.penduduk;"], check=True)
    print("✓ Tabel MySQL `db_sensus.penduduk` berhasil di-TRUNCATE (0 baris).")
except Exception as e:
    print(f"[!] Gagal TRUNCATE database: {e}")

# 2. Reset berkas CSV aktif di /root/Downloads/
csv_path = "/root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv"
if os.path.exists(csv_path):
    try:
        with open(csv_path, "r", encoding="utf-8-sig") as f:
            header = f.readline()
        with open(csv_path, "w", encoding="utf-8-sig") as f:
            f.write(header)
        print(f"✓ Berkas CSV aktif ({csv_path}) berhasil direset (hanya baris judul).")
    except Exception as e:
        print(f"[!] Gagal mereset CSV: {e}")
else:
    print(f"[!] Berkas CSV {csv_path} tidak ditemukan.")

# 3. Verifikasi Jumlah Data
print("\nVerifikasi Jumlah Baris:")
subprocess.run(["mysql", "-e", "SELECT COUNT(*) AS total_data_saat_ini FROM db_sensus.penduduk;"])
print("=" * 60)
print("Sistem siap 100% untuk pengujian model AI CUA-S1 dari nol!")
print("=" * 60)
