#!/usr/bin/env bash
# start.sh - Script untuk menjalankan Aplikasi Sensus Penduduk 2024

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PORT=8000

echo "=========================================================="
echo "    SISTEM FORMULIR SENSUS PENDUDUK INDONESIA 2024       "
echo "=========================================================="
echo "Direktori Aplikasi: $DIR"
echo "File Spreadsheet  : /root/Downloads/sensus_penduduk_indonesia_2024_dummy.csv"
echo "Alamat Web        : http://127.0.0.1:$PORT"
echo "----------------------------------------------------------"

# Cek apakah MariaDB sedang berjalan
if mysqladmin ping >/dev/null 2>&1; then
    echo "MariaDB (MySQL) server sudah aktif."
else
    echo "Menjalankan MariaDB daemon..."
    nohup mariadbd --user=mysql > /dev/null 2>&1 &
    sleep 2
    if mysqladmin ping >/dev/null 2>&1; then
        echo "MariaDB BERHASIL dijalankan."
    else
        echo "Peringatan: MariaDB belum merespons."
    fi
fi

# Cek apakah port 8000 sedang berjalan
if netstat -tuln 2>/dev/null | grep -q ":$PORT "; then
    echo "Server PHP sudah aktif di port $PORT."
else
    echo "Menjalankan PHP Built-in Web Server di port $PORT..."
    nohup php -S 0.0.0.0:$PORT -t "$DIR" > "$DIR/server.log" 2>&1 &
    sleep 1
    if netstat -tuln 2>/dev/null | grep -q ":$PORT "; then
        echo "Server PHP BERHASIL dijalankan di background (PID: $!)."
    else
        echo "Gagal menjalankan server. Cek $DIR/server.log"
        exit 1
    fi
fi

echo "Membuka browser Google Chrome..."
if which google-chrome >/dev/null 2>&1; then
    google-chrome "http://127.0.0.1:$PORT/data.php" >/dev/null 2>&1 &
fi

echo ""
echo "Aplikasi & phpMyAdmin siap digunakan!"
echo "1. Formulir Sensus  : http://127.0.0.1:$PORT/index.php"
echo "2. Tabel Data Sensus: http://127.0.0.1:$PORT/data.php"
echo "3. phpMyAdmin       : http://127.0.0.1:$PORT/phpmyadmin/"
echo "=========================================================="
