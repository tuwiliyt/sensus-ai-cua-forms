#!/usr/bin/env python3
"""
auto_fill_agent.py - Agen Otomasi Pengisian Formulir Sensus Penduduk
Berdasarkan Arsitektur CUA-S1-FORMS (System One GUI Form Filling Model)
Repositori: https://huggingface.co/cua-ai/cua-s1-forms
Video Referensi: https://www.youtube.com/watch?v=Rzgd-y3mCPs
"""

import sys
import json
import urllib.request
import urllib.parse
from datetime import datetime

# URL Aplikasi Sensus Lokal
BASE_URL = "http://127.0.0.1:8000"
SUBMIT_URL = f"{BASE_URL}/proses_simpan.php"

# Definisi mapping 28 elemen formulir sensus
FORM_FIELDS_SCHEMA = {
    # 1. Identitas Kependudukan
    "no_kk": {"label": "No KK", "type": "text", "description": "16 digit Nomor Kartu Keluarga"},
    "nik": {"label": "NIK", "type": "text", "description": "16 digit Nomor Induk Kependudukan"},
    "nama_lengkap": {"label": "Nama Lengkap", "type": "text", "description": "Nama lengkap sesuai KTP"},
    "hubungan_keluarga": {"label": "Hubungan Keluarga", "type": "select", "options": ["Kepala Keluarga", "Suami", "Istri", "Anak", "Orang Tua / Mertua", "Famili Lain"]},
    
    # 2. Karakteristik & Kelahiran
    "jenis_kelamin": {"label": "Jenis Kelamin", "type": "radio", "options": ["Laki-laki", "Perempuan"]},
    "tempat_lahir": {"label": "Tempat Lahir", "type": "text", "description": "Kota atau kabupaten kelahiran"},
    "tanggal_lahir": {"label": "Tanggal Lahir", "type": "date", "description": "Format YYYY-MM-DD"},
    "umur": {"label": "Umur (Tahun)", "type": "number", "description": "Usia dalam tahun"},
    "agama": {"label": "Agama", "type": "select", "options": ["Islam", "Kristen Protestan", "Katolik", "Hindu", "Buddha", "Khonghucu"]},
    "status_perkawinan": {"label": "Status Perkawinan", "type": "select", "options": ["Belum Kawin", "Kawin", "Cerai Hidup", "Cerai Mati"]},
    
    # 3. Pendidikan & Ekonomi
    "partisipasi_sekolah": {"label": "Partisipasi Sekolah", "type": "select", "options": ["Belum Pernah Sekolah", "Masih Sekolah", "Tidak Bersekolah Lagi"]},
    "pendidikan_tertinggi": {"label": "Pendidikan Tertinggi", "type": "select", "options": ["Tidak / Belum Sekolah", "PAUD / TK", "SD / Sederajat", "SMP / Sederajat", "SMA / SMK / MA", "Diploma (D1-D3)", "Sarjana (S1/D4)", "Magister (S2)", "Doktor (S3)"]},
    "kegiatan_utama": {"label": "Kegiatan Utama", "type": "select", "options": ["Bekerja", "Mengurus Rumah Tangga", "Sekolah", "Pensiunan / Tidak Bekerja", "Tidak Bekerja"]},
    "lapangan_pekerjaan": {"label": "Lapangan Pekerjaan", "type": "select", "options": ["Pegawai Negeri Sipil (PNS)", "Karyawan BUMN", "Karyawan Swasta", "Wiraswasta / Pedagang", "Petani / Pekebun", "Guru / Dosen", "Tenaga Kesehatan (Dokter/Perawat)", "Teknisi / Pengrajin", "Buruh Harian Lepas", "Pengemudi Ojek Online / Transportasi", "Pensiunan", "Pelajar / Mahasiswa", "Mengurus Rumah Tangga", "Belum / Tidak Bekerja"]},
    "estimasi_pendapatan": {"label": "Estimasi Pendapatan Bulanan (Rp)", "type": "number", "description": "Nominal penghasilan dalam rupiah"},
    "jaminan_kesehatan": {"label": "Jaminan Kesehatan", "type": "select", "options": ["BPJS PBI (Bantuan Pemerintah)", "BPJS Non-PBI / Mandiri", "Asuransi Swasta", "Tidak Memiliki"]},
    "disabilitas": {"label": "Disabilitas", "type": "select", "options": ["Tidak", "Tunadaksa", "Tunanetra", "Tunarungu", "Tunawicara", "Disabilitas Mental/Intelektual"]},
    
    # 4. Wilayah Domisili
    "provinsi": {"label": "Provinsi", "type": "select"},
    "kabupaten_kota": {"label": "Kabupaten/Kota", "type": "text"},
    "kecamatan": {"label": "Kecamatan", "type": "text"},
    "kelurahan_desa": {"label": "Kelurahan/Desa", "type": "text"},
    "alamat_domisili": {"label": "Alamat Domisili", "type": "text"},
    
    # 5. Kondisi Perumahan & Sanitasi
    "status_kepemilikan_bangunan": {"label": "Status Kepemilikan Bangunan", "type": "select", "options": ["Milik Sendiri", "Sewa / Kontrak", "Rumah Dinas", "Bebas Sewa (Milik Keluarga/Ortu)"]},
    "luas_lantai": {"label": "Luas Lantai (m2)", "type": "number"},
    "sumber_air_minum": {"label": "Sumber Air Minum", "type": "select", "options": ["PAM / Ledeng", "Sumur Bor / Pompa", "Sumur Terlindung", "Mata Air", "Air Kemasan / Isi Ulang"]},
    "daya_listrik": {"label": "Daya Listrik", "type": "select", "options": ["PLN 450 VA", "PLN 900 VA", "PLN 1300 VA", "PLN 2200 VA+", "Non-PLN / Tanpa Listrik"]},
    "fasilitas_sanitasi": {"label": "Fasilitas Sanitasi", "type": "select", "options": ["Jamban Sendiri", "Jamban Bersama", "Bukan Jamban Sendiri"]}
}

class CuaFormFillingAgent:
    """
    Simulasi Agen CUA-S1-FORMS:
    1. Parsing Dokumen Sumber (KTP/Profil Penduduk)
    2. Context Representation (TASK + FORM + ELEMENT)
    3. Action Scoring (Fill, Select, Check, Click)
    4. Execution & Pipeline Submission
    """
    def __init__(self, target_url=SUBMIT_URL):
        self.target_url = target_url

    def format_element_context(self, field_key, field_spec):
        """Membuat representasi konteks UI sesuai spesifikasi CUA-S1-FORMS"""
        return f'TASK fill the form from document, then submit\nFORM Formulir Sensus Penduduk 2024\nELEMENT {field_spec.get("type", "input")} "{field_spec["label"]}" name="{field_key}"'

    def score_and_match_entities(self, document_entities):
        """
        Mencocokkan entitas dokumen dengan elemen form formulir sensus
        (Prinsip CUA-S1: Action Selection argmax P(option | context))
        """
        form_payload = {}
        actions_log = []

        print("\n" + "="*65)
        print(" [CUA-S1-FORMS] PROSES SKORING & PEMETAAN ENTITAS FORMULIR")
        print("="*65)

        for field_key, field_spec in FORM_FIELDS_SCHEMA.items():
            context = self.format_element_context(field_key, field_spec)
            
            # Cari kandidat nilai yang cocok dari dokumen
            matched_value = None
            for doc_key, doc_val in document_entities.items():
                # Normalisasi nama field
                clean_doc_key = doc_key.lower().replace(" ", "_").replace("/", "_").replace("(", "").replace(")", "")
                if field_key == clean_doc_key or field_spec["label"].lower() in doc_key.lower():
                    matched_value = str(doc_val).strip()
                    break

            if matched_value is not None:
                form_payload[field_key] = matched_value
                action_desc = f'FILL "{field_spec["label"]}" -> "{matched_value}"'
                actions_log.append(action_desc)
                print(f" [+] ELEMENT: {field_spec['label']:<30} | ACTION: {action_desc}")
            else:
                # Opsi default jika tidak ada di dokumen
                default_val = "Tidak" if field_key == "disabilitas" else ("0" if "Rp" in field_spec["label"] else "")
                form_payload[field_key] = default_val
                print(f" [.] ELEMENT: {field_spec['label']:<30} | ACTION: SKIP (Default: '{default_val}')")

        # Otomatisasi perhitungan umur jika belum ada tapi tanggal lahir tersedia
        if not form_payload.get("umur") and form_payload.get("tanggal_lahir"):
            try:
                dob = datetime.strptime(form_payload["tanggal_lahir"], "%Y-%m-%d")
                now = datetime.now()
                age = now.year - dob.year - ((now.month, now.day) < (dob.month, dob.day))
                form_payload["umur"] = str(age)
                print(f" [*] CUA AUTO-CALC: Menghitung umur dari Tanggal Lahir -> {age} Tahun")
            except Exception:
                pass

        return form_payload, actions_log

    def submit_form(self, payload):
        """Mengirimkan aksi ke sistem web PHP"""
        data = urllib.parse.urlencode(payload).encode("utf-8")
        req = urllib.request.Request(
            self.target_url,
            data=data,
            headers={
                "User-Agent": "Cua-Agent-S1/1.0",
                "Content-Type": "application/x-www-form-urlencoded"
            }
        )

        print("\n" + "="*65)
        print(" [CUA-DRIVER] MENGEKSEKUSI SUBMISSION KE WEB APLIKASI SENSUS")
        print("="*65)
        print(f" Target Endpoint: {self.target_url}")
        print(f" Payload Size   : {len(data)} bytes (28 atribut lengkap)")

        try:
            with urllib.request.urlopen(req) as resp:
                status_code = resp.getcode()
                final_url = resp.geturl()
                print(f"\n [✓] Respon Server: HTTP {status_code}")
                print(f" [✓] Redirected to: {final_url}")
                return True
        except Exception as e:
            print(f"\n [!] Kesalahan Pengiriman Form: {e}")
            return False

def run_test():
    # Contoh data simulasi warga baru (misal hasil ekstraksi KTP / Dokumen Sensus)
    dummy_resident = {
        "no_kk": "3273010106240001",
        "nik": "3273011508920002",
        "nama_lengkap": "Rian Kurniawan, S.Kom.",
        "hubungan_keluarga": "Kepala Keluarga",
        "jenis_kelamin": "Laki-laki",
        "tempat_lahir": "Bandung",
        "tanggal_lahir": "1992-08-15",
        "umur": "32",
        "agama": "Islam",
        "status_perkawinan": "Kawin",
        "partisipasi_sekolah": "Tidak Bersekolah Lagi",
        "pendidikan_tertinggi": "Sarjana (S1/D4)",
        "kegiatan_utama": "Bekerja",
        "lapangan_pekerjaan": "Karyawan Swasta",
        "estimasi_pendapatan": "8750000",
        "jaminan_kesehatan": "BPJS Non-PBI / Mandiri",
        "disabilitas": "Tidak",
        "provinsi": "Jawa Barat",
        "kabupaten_kota": "Kota Bandung",
        "kecamatan": "Coblong",
        "kelurahan_desa": "Dago",
        "alamat_domisili": "Jl. Cisitu Indah No. 18, RT 004/RW 002",
        "status_kepemilikan_bangunan": "Milik Sendiri",
        "luas_lantai": "72",
        "sumber_air_minum": "PAM / Ledeng",
        "daya_listrik": "PLN 1300 VA",
        "fasilitas_sanitasi": "Jamban Sendiri"
    }

    print("\n" + "#"*65)
    print("   UJI COBA SISTEM OTOMASI PENGISIAN FORMULIR SENSUS (CUA-S1)  ")
    print("#"*65)
    print(f" Warga yang akan diinput : {dummy_resident['nama_lengkap']}")
    print(f" NIK                     : {dummy_resident['nik']}")
    print(f" Domisili                : {dummy_resident['kelurahan_desa']}, {dummy_resident['kabupaten_kota']}")

    agent = CuaFormFillingAgent()
    payload, actions = agent.score_and_match_entities(dummy_resident)
    success = agent.submit_form(payload)

    if success:
        print("\n" + "="*65)
        print(" [HASIL VERIFIKASI] DATA BERHASIL DISIMPAN KE SISTEM!")
        print("="*65)
        print(f" 1. Cek di Web Sensus : {BASE_URL}/data.php")
        print(f" 2. Cek di phpMyAdmin : {BASE_URL}/phpmyadmin/ (Tabel db_sensus.penduduk)")
        print(f" 3. Cek di CSV        : /root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv")
    else:
        print("\n [!] Gagal melakukan input data.")

if __name__ == "__main__":
    run_test()
