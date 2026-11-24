<?php
session_start();
include 'koneksi.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);$nama     = mysqli_real_escape_string($koneksi,$_POST['nama']);
    $prodi    = mysqli_real_escape_string($koneksi, $_POST['prodi']);$semester = intval($_POST['semester']);$status   = mysqli_real_escape_string($koneksi,$_POST['status']);
    $password =$nim;

    $cek = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim = '$nim'");
    if ($cek && mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Gagal! NIM $nim sudah terdaftar.'); window.location='mahasiswa.php';</script>";
    } else {
        $q = "INSERT INTO mahasiswa (nim, nama, prodi, semester, status, password) VALUES ('$nim', '$nama', '$prodi', '$semester', '$status', '$password')";
        if (mysqli_query($koneksi,$q)) {
            echo "<script>alert('Data mahasiswa berhasil ditambahkan!'); window.location='mahasiswa.php';</script>";
        } else {
            echo "<script>alert('Gagal menambah data: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

if (isset($_POST['edit'])) {
    $id       = intval($_POST['id']);
    $nim      = mysqli_real_escape_string($koneksi, $_POST['nim']);$nama     = mysqli_real_escape_string($koneksi,$_POST['nama']);
    $prodi    = mysqli_real_escape_string($koneksi, $_POST['prodi']);$semester = intval($_POST['semester']);$status   = mysqli_real_escape_string($koneksi,$_POST['status']);

    $q = "UPDATE mahasiswa SET nim='$nim', nama='$nama', prodi='$prodi', semester='$semester', status='$status' WHERE id='$id'";
    if (mysqli_query($koneksi,$q)) {
        echo "<script>alert('Data mahasiswa berhasil diperbarui!'); window.location='mahasiswa.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data: " . mysqli_error($koneksi) . "');</script>";
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM mahasiswa WHERE id='$id'");
    echo "<script>alert('Data mahasiswa berhasil dihapus!'); window.location='mahasiswa.php';</script>";
}

$keyword = "";
if (isset($_GET['cari']) &&$_GET['cari'] != "") {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);$sql = "SELECT * FROM mahasiswa WHERE nim LIKE '%$keyword%' OR nama LIKE '%$keyword\%' OR prodi LIKE '\%$keyword%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM mahasiswa ORDER BY id DESC";
}
$result_mhs = mysqli_query($koneksi,$sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Mahasiswa - Devakad</title>
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
  <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white flex flex-col justify-between p-4 min-h-screen shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">
    <div>
      <div class="flex items-center justify-between px-3 py-4 mb-4 border-b border-slate-800">
        <div class="flex items-center gap-3">
          <div class="bg-blue-600 p-2 rounded-xl text-white">
            <i data-lucide="graduation-cap" class="w-6 h-6"></i>
          </div>
          <div>
            <h1 class="font-bold text-lg leading-tight tracking-wide">DEVAKAD</h1>
            <p class="text-xs text-slate-400 font-medium">Dashboard Dosen</p>
          </div>
        </div>
        <button type="button" onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800" aria-label="Tutup menu">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <nav class="space-y-1">
        <a href="index.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400