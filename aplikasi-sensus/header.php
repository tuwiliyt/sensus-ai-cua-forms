<?php
// header.php
require_once __DIR__ . '/config.php';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensus Penduduk Indonesia 2024</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary-rgb: 13, 110, 253;
            --primary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            --header-gradient: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f6f9;
            color: #2b2f32;
        }
        .navbar-custom {
            background: var(--header-gradient);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .nav-link {
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 6px;
            padding: 8px 14px !important;
        }
        .nav-link.active, .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff !important;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .section-header {
            border-left: 4px solid #0d6efd;
            padding-left: 12px;
            margin-bottom: 20px;
        }
        .section-header h5 {
            font-weight: 700;
            margin-bottom: 2px;
            color: #1e293b;
        }
        .section-header small {
            color: #64748b;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.88rem;
            color: #334155;
            margin-bottom: 4px;
        }
        .required-star {
            color: #dc3545;
            font-weight: bold;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 0.92rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .badge-subtle {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .btn-action {
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 18px;
        }
        .stat-card {
            border-radius: 12px;
            padding: 20px;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .stat-card.bg-blue {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
        }
        .stat-card.bg-emerald {
            background: linear-gradient(135deg, #059669, #047857);
        }
        .stat-card.bg-purple {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
        }
        .stat-card.bg-amber {
            background: linear-gradient(135deg, #d97706, #b45309);
        }
        .helper-text {
            font-size: 0.78rem;
            color: #64748b;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <span class="p-1 px-2 rounded bg-primary text-white"><i class="bi bi-person-vcard-fill"></i></span>
            <span>SENSUS PENDUDUK <span class="badge bg-warning text-dark ms-1">2024</span></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>" href="index.php">
                        <i class="bi bi-input-cursor-text me-1"></i> Formulir Sensus
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'data.php') ? 'active' : '' ?>" href="data.php">
                        <i class="bi bi-table me-1"></i> Data Penduduk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'statistik.php') ? 'active' : '' ?>" href="statistik.php">
                        <i class="bi bi-bar-chart-line-fill me-1"></i> Statistik & Analitik
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'import.php') ? 'active' : '' ?>" href="import.php">
                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Import CSV
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="phpmyadmin/" target="_blank" class="btn btn-info btn-sm btn-action fw-bold text-dark shadow-sm" title="Buka Database di phpMyAdmin">
                    <i class="bi bi-database me-1"></i> Buka phpMyAdmin
                </a>
                <a href="export.php" class="btn btn-outline-light btn-sm btn-action">
                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Unduh CSV
                </a>
                <a href="index.php" class="btn btn-warning btn-sm btn-action fw-bold">
                    <i class="bi bi-plus-circle-fill me-1"></i> Input Data Baru
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-4">
