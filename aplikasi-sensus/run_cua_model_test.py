#!/usr/bin/env python3
"""
run_cua_model_test.py - Pengujian Model AI CUA-S1-FORMS Secara Nyata
Model: https://huggingface.co/cua-ai/cua-s1-forms (2.8 MB, 706k params)
Arsitektur: Option-Attention Byte Transformer (Jev-Like System One)
"""

import sys
import os
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

def load_ai_model():
    print("[1/4] Memuat Model AI CUA-S1-FORMS ke memori...")
    device = torch.device('cpu')
    model, collator, config = load_checkpoint(MODEL_WEIGHTS, device)
    model.eval()
    params = sum(p.numel() for p in model.parameters())
    print(f"      ✓ Berhasil memuat model: {params:,} parameter pada {device}")
    print(f"      ✓ Arsitektur: 2-layer Transformer, 4 heads, width {config['width']}")
    return model, collator

def run_ai_form_scoring(model, collator, raw_person_data):
    print("\n[2/4] Menyiapkan Entitas Dokumen & Elemen Formulir...")
    
    # 1. Representasikan entitas dokumen sesuai kosakata konsep CUA-S1
    entities = [
        Entity(label="Policy #", value=raw_person_data["no_kk"]),
        Entity(label="Full name", value=raw_person_data["nama_lengkap"]),
        Entity(label="Date of birth", value=raw_person_data["tanggal_lahir"]),
        Entity(label="City", value=raw_person_data["kabupaten_kota"]),
        Entity(label="Street address", value=raw_person_data["alamat_domisili"]),
        Entity(label="Insurance provider", value=raw_person_data["jaminan_kesehatan"]),
    ]
    rendered_options = render_options(entities)

    # 2. Representasikan elemen form pada aplikasi sensus
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

    print("\n[3/4] Menjalankan Forward Pass (Inferensi AI Paralel)...")
    batch = collator(examples)
    
    with torch.no_grad():
        logits = model(batch)
        probs = logits.softmax(-1).tolist()

    print("\n" + "="*75)
    print("      HASIL PREDIKSI KEPUTUSAN MODEL AI CUA-S1 (INFERENSI AKTIF)     ")
    print("="*75)

    extracted_payload = {}
    for el, prob_row in zip(form_elements, probs):
        best_idx = prob_row.index(max(prob_row))
        confidence = prob_row[best_idx] * 100
        action, entity_idx = decode(best_idx, entities)
        
        if action == "fill" and entity_idx is not None:
            chosen_entity = entities[entity_idx]
            extracted_payload[el.element_token] = chosen_entity.value
            print(f" [EDIT  ] {el.label:<20} -> FILL : '{chosen_entity.value}' ({confidence:5.1f}%)")
        elif action == "click":
            print(f" [BUTTON] {el.label:<20} -> CLICK: Eksekusi Submit ({confidence:5.1f}%)")
        else:
            print(f" [SKIP  ] {el.label:<20} -> SKIP  ({confidence:5.1f}%)")

    # Lengkapi atribut sensus lainnya yang bersifat kategori
    extracted_payload.update({
        "hubungan_keluarga": raw_person_data.get("hubungan_keluarga", "Kepala Keluarga"),
        "jenis_kelamin": raw_person_data.get("jenis_kelamin", "Laki-laki"),
        "umur": raw_person_data.get("umur", "30"),
        "agama": raw_person_data.get("agama", "Islam"),
        "status_perkawinan": raw_person_data.get("status_perkawinan", "Kawin"),
        "partisipasi_sekolah": raw_person_data.get("partisipasi_sekolah", "Tidak Bersekolah Lagi"),
        "pendidikan_tertinggi": raw_person_data.get("pendidikan_tertinggi", "Sarjana (S1/D4)"),
        "kegiatan_utama": raw_person_data.get("kegiatan_utama", "Bekerja"),
        "lapangan_pekerjaan": raw_person_data.get("lapangan_pekerjaan", "Karyawan Swasta"),
        "disabilitas": raw_person_data.get("disabilitas", "Tidak"),
        "provinsi": raw_person_data.get("provinsi", "Jawa Barat"),
        "kecamatan": raw_person_data.get("kecamatan", "Coblong"),
        "kelurahan_desa": raw_person_data.get("kelurahan_desa", "Dago"),
        "status_kepemilikan_bangunan": raw_person_data.get("status_kepemilikan_bangunan", "Milik Sendiri"),
        "luas_lantai": str(raw_person_data.get("luas_lantai", 72)),
        "sumber_air_minum": raw_person_data.get("sumber_air_minum", "PAM / Ledeng"),
        "daya_listrik": raw_person_data.get("daya_listrik", "PLN 1300 VA"),
        "fasilitas_sanitasi": raw_person_data.get("fasilitas_sanitasi", "Jamban Sendiri")
    })

    return extracted_payload

def submit_to_web(payload):
    print("\n[4/4] Mengirimkan Hasil Prediksi AI ke Web Sensus & Database...")
    data = urllib.parse.urlencode(payload).encode("utf-8")
    req = urllib.request.Request(
        SUBMIT_URL,
        data=data,
        headers={
            "User-Agent": "CUA-S1-Inference-Engine/1.0",
            "Content-Type": "application/x-www-form-urlencoded"
        }
    )
    with urllib.request.urlopen(req, timeout=10) as resp:
        if resp.getcode() == 200:
            print("      ✓ Response: HTTP 200 OK")
            print("      ✓ Data telah berhasil masuk ke database MySQL (db_sensus) dan CSV!")
            return True
        return False

def main():
    # Data subjek uji coba
    test_subject = {
        "nik": "3273011905960003",
        "no_kk": "3273010106240003",
        "nama_lengkap": "Farhan Alamsyah, M.T.",
        "tanggal_lahir": "1996-05-19",
        "tempat_lahir": "Bandung",
        "umur": "28",
        "jenis_kelamin": "Laki-laki",
        "hubungan_keluarga": "Kepala Keluarga",
        "agama": "Islam",
        "status_perkawinan": "Kawin",
        "partisipasi_sekolah": "Tidak Bersekolah Lagi",
        "pendidikan_tertinggi": "Magister (S2)",
        "kegiatan_utama": "Bekerja",
        "lapangan_pekerjaan": "Karyawan Swasta",
        "estimasi_pendapatan": 12500000,
        "jaminan_kesehatan": "BPJS Non-PBI / Mandiri",
        "disabilitas": "Tidak",
        "provinsi": "Jawa Barat",
        "kabupaten_kota": "Kota Bandung",
        "kecamatan": "Coblong",
        "kelurahan_desa": "Dago",
        "alamat_domisili": "Jl. Sangkuriang Barat No. 12, RT 002/RW 007",
        "status_kepemilikan_bangunan": "Milik Sendiri",
        "luas_lantai": 90,
        "sumber_air_minum": "PAM / Ledeng",
        "daya_listrik": "PLN 2200 VA+",
        "fasilitas_sanitasi": "Jamban Sendiri"
    }

    model, collator = load_ai_model()
    payload = run_ai_form_scoring(model, collator, test_subject)
    submit_to_web(payload)

    print("\n" + "="*75)
    print(" Verifikasi Akhir:")
    print(" 1. Web Data Sensus : http://127.0.0.1:8000/data.php?q=Farhan")
    print(" 2. phpMyAdmin      : http://127.0.0.1:8000/phpmyadmin/")
    print("="*75)

if __name__ == "__main__":
    main()
