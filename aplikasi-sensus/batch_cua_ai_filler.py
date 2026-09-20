#!/usr/bin/env python3
"""
batch_cua_ai_filler.py - Batch Form Filling Menggunakan Inferensi Nyata Model AI CUA-S1-FORMS
Model: https://huggingface.co/cua-ai/cua-s1-forms (Safetensors, 706k params)
"""

import sys
import os
import csv
import time
import argparse
from pathlib import Path
import urllib.request
import urllib.parse
from datetime import datetime

BASE_DIR = Path(__file__).resolve().parent
LOCAL_CUA_SRC = str(BASE_DIR / "cua-repo" / "libs" / "cua-s1" / "python" / "src")
CUA_SRC = LOCAL_CUA_SRC if os.path.exists(LOCAL_CUA_SRC) else "/root/Desktop/aplikasi-sensus/cua-repo/libs/cua-s1/python/src"
if CUA_SRC not in sys.path:
    sys.path.insert(0, CUA_SRC)

import torch
from cua_s1.model import load_checkpoint, ChoiceExample
from cua_s1.schema import Element, Entity, render_context, render_options, decode

LOCAL_WEIGHTS = BASE_DIR / "models" / "cua-s1-forms.safetensors"
HF_WEIGHTS = Path('/root/.cache/huggingface/hub/models--cua-ai--cua-s1-forms/snapshots/f54adbf447f4ca6ec259f529ee3f2e3e09f8cc71/cua-s1-forms.safetensors')
MODEL_WEIGHTS = LOCAL_WEIGHTS if LOCAL_WEIGHTS.exists() else HF_WEIGHTS
SUBMIT_URL = "http://127.0.0.1:8000/proses_simpan.php"
DEFAULT_CSV = str(BASE_DIR / "data" / "data_sumber_sensus_mentah.csv")

def format_date_iso(date_str):
    if not date_str:
        return ""
    if "/" in date_str:
        parts = date_str.split("/")
        if len(parts) == 3:
            return f"{parts[2]}-{parts[1].zfill(2)}-{parts[0].zfill(2)}"
    return str(date_str)

def main():
    parser = argparse.ArgumentParser(description="Pengisi Formulir Batch Berbasis Model AI CUA-S1-FORMS")
    parser.add_argument("--file", default=DEFAULT_CSV, help="File CSV sumber data")
    parser.add_argument("--limit", type=int, default=10, help="Jumlah baris yang diproses (default: 10)")
    args = parser.parse_args()

    print("=" * 75)
    print("   BATCH FORM FILLING MENGGUNAKAN INFERENSI MODEL AI CUA-S1-FORMS   ")
    print("=" * 75)

    # 1. Load Model AI
    print("[1/3] Memuat Checkpoint CUA-S1-FORMS dari Safetensors...")
    device = torch.device("cpu")
    model, collator, config = load_checkpoint(MODEL_WEIGHTS, device)
    model.eval()
    print(f"      ✓ Model aktif: {sum(p.numel() for p in model.parameters()):,} parameter di {device}")

    # 2. Baca CSV
    if not os.path.exists(args.file):
        print(f"[!] File sumber tidak ditemukan: {args.file}")
        return

    with open(args.file, "r", encoding="utf-8-sig") as f:
        reader = csv.DictReader(f)
        all_rows = list(reader)

    if args.limit > 0:
        rows_to_process = all_rows[:args.limit]
    else:
        rows_to_process = all_rows

    total = len(rows_to_process)
    print(f"[2/3] Membaca Spreadsheet: {total} baris data siap diproses dengan AI\n")

    # 3. Batch Loop
    print("[3/3] Menjalankan Inferensi AI & Pengisian Formulir:")
    start_time = time.time()
    success_count = 0

    for i, row in enumerate(rows_to_process, 1):
        nama = row.get("Nama Lengkap", f"Person {i}")
        dob = format_date_iso(row.get("Tanggal Lahir", ""))

        # Bentuk entitas dokumen
        entities = [
            Entity(label="Policy #", value=row.get("No KK", "")),
            Entity(label="Full name", value=nama),
            Entity(label="Date of birth", value=dob),
            Entity(label="City", value=row.get("Kabupaten/Kota", "")),
            Entity(label="Street address", value=row.get("Alamat Domisili", "")),
            Entity(label="Insurance provider", value=row.get("Jaminan Kesehatan", "")),
        ]
        rendered_options = render_options(entities)

        form_elements = [
            Element(role="Edit", label="Policy #", element_token="no_kk", value=""),
            Element(role="Edit", label="Full name", element_token="nama_lengkap", value=""),
            Element(role="Edit", label="Date of birth", element_token="tanggal_lahir", value=""),
            Element(role="Edit", label="City", element_token="kabupaten_kota", value=""),
            Element(role="Edit", label="Street address", element_token="alamat_domisili", value=""),
            Element(role="Edit", label="Insurance provider", element_token="jaminan_kesehatan", value=""),
            Element(role="Button", label="Submit", element_token="btn_submit", value="")
        ]

        examples = [
            ChoiceExample(
                context=render_context("Census Registration Form", el),
                options=tuple(rendered_options),
                label=0
            )
            for el in form_elements
        ]

        batch = collator(examples)
        with torch.no_grad():
            probs = model(batch).softmax(-1).tolist()

        # Decode keputusan AI
        payload = {}
        for el, prob_row in zip(form_elements, probs):
            best_idx = prob_row.index(max(prob_row))
            action, ent_idx = decode(best_idx, entities)
            if action == "fill" and ent_idx is not None:
                payload[el.element_token] = entities[ent_idx].value

        # Lengkapi sisa kolom sesuai skema
        payload["nik"] = row.get("NIK", "")
        payload["tempat_lahir"] = row.get("Tempat Lahir", "")
        payload["umur"] = row.get("Umur (Tahun)", "30")
        payload["hubungan_keluarga"] = row.get("Hubungan Keluarga", "Kepala Keluarga")
        payload["jenis_kelamin"] = row.get("Jenis Kelamin", "Laki-laki")
        payload["agama"] = row.get("Agama", "Islam")
        payload["status_perkawinan"] = row.get("Status Perkawinan", "Kawin")
        payload["partisipasi_sekolah"] = row.get("Partisipasi Sekolah", "Tidak Bersekolah Lagi")
        payload["pendidikan_tertinggi"] = row.get("Pendidikan Tertinggi", "SMA / SMK / MA")
        payload["kegiatan_utama"] = row.get("Kegiatan Utama", "Bekerja")
        payload["lapangan_pekerjaan"] = row.get("Lapangan Pekerjaan", "Karyawan Swasta")
        payload["estimasi_pendapatan"] = str(row.get("Estimasi Pendapatan Bulanan (Rp)", "0")).replace(".", "").replace(",", "")
        payload["disabilitas"] = row.get("Disabilitas", "Tidak")
        payload["provinsi"] = row.get("Provinsi", "Jawa Barat")
        payload["kecamatan"] = row.get("Kecamatan", "")
        payload["kelurahan_desa"] = row.get("Kelurahan/Desa", "")
        payload["status_kepemilikan_bangunan"] = row.get("Status Kepemilikan Bangunan", "Milik Sendiri")
        payload["luas_lantai"] = str(row.get("Luas Lantai (m2)", "45"))
        payload["sumber_air_minum"] = row.get("Sumber Air Minum", "PAM / Ledeng")
        payload["daya_listrik"] = row.get("Daya Listrik", "PLN 1300 VA")
        payload["fasilitas_sanitasi"] = row.get("Fasilitas Sanitasi", "Jamban Sendiri")

        # Submit ke Web Server
        data_encoded = urllib.parse.urlencode(payload).encode("utf-8")
        req = urllib.request.Request(
            SUBMIT_URL,
            data=data_encoded,
            headers={"Content-Type": "application/x-www-form-urlencoded"}
        )
        try:
            with urllib.request.urlopen(req, timeout=5) as resp:
                if resp.getcode() == 200:
                    success_count += 1
                    print(f" [{i:03d}/{total:03d}] AI Forward Pass OK -> Terisi: {nama[:25]:<25} (NIK: {payload['nik']})")
        except Exception as e:
            print(f" [{i:03d}/{total:03d}] Error submit {nama}: {e}")

    elapsed = time.time() - start_time
    print("\n" + "=" * 75)
    print(f" [✓] Selesai! Berhasil memproses {success_count} dari {total} data menggunakan AI Model.")
    print(f" [⏱] Waktu eksekusi inferensi AI: {elapsed:.2f} detik ({success_count/max(0.1, elapsed):.1f} data/detik)")
    print("=" * 75)

if __name__ == "__main__":
    main()
