<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_POST['simpan_nilai'])) {
    $mhs_id = intval($_POST['mahasiswa_id']);
    $mk_id  = intval($_POST['mk_id']);
    $nilai  = floatval($_POST['nilai_angka']);

    if ($nilai >= 85) {
        $huruf = 'A';
        $bobot = 4.00;
    } elseif ($nilai >= 75) {
        $huruf = 'B';
        $bobot = 3.00;
    } elseif ($nilai >= 60) {
        $huruf = 'C';
        $bobot = 2.00;
    } elseif ($nilai >= 50) {
        $huruf = 'D';
        $bobot = 1.00;
    } else {
        $huruf = 'E';
        $bobot = 0.00;
    }

    $q = "INSERT INTO nilai (mahasiswa_id, mk_id, nilai_angka, huruf, bobot) VALUES ('$mhs_id', '$mk_id', '$nilai', '$huruf', '$bobot')";
    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('Nilai berhasil disimpan!'); window.location='input_nilai.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan nilai: " . mysqli_error($koneksi) . "');</script>";
    }
}

$mhs_list = mysqli_query($koneksi, "SELECT id, nim, nama FROM mahasiswa ORDER BY nama ASC");
$mk_list  = mysqli_query($koneksi, "SELECT id, kode_mk, nama_mk, sks FROM mata_kuliah ORDER BY nama_mk ASC");
$nilai_list = mysqli_query($koneksi, "
    SELECT n.*, m.nim, m.nama AS nama_mhs, mk.kode_mk, mk.nama_mk, mk.sks
    FROM nilai n
    LEFT JOIN mahasiswa m ON n.mahasiswa_id = m.id
    LEFT JOIN mata_kuliah mk ON n.mk_id = mk.id
    ORDER BY n.id DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input Nilai - Devakad</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex">

  <!-- OVERLAY MOBILE -->
  <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 z-30 hidden md:hidden"></div>

  <!-- SIDEBAR DOSEN -->
  <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40 w-64 bg