<?php
declare(strict_types=1);

/**
 * Konfigurasi phpMyAdmin untuk Aplikasi Sensus Penduduk 2024
 */

$cfg['blowfish_secret'] = 'sensus2024indonesiasecretkey32bytes!';

$i = 0;
$i++;

/* Server connection */
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['port'] = '3306';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = true;
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';

/* Direktori Sementara */
$cfg['TempDir'] = '/tmp';
$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';
$cfg['SendErrorReports'] = 'never';
