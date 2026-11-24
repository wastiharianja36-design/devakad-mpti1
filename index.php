<?php
session_start();
include 'koneksi.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$jml_mhs = 0;
$jml_mk = 0;
$jml_nilai = 0;

if (isset($koneksi) && !$koneksi->connect_error) {
    $q_mhs = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM mahasiswa");
    if ($q_mhs && $r = mysqli_fetch_assoc($q_mhs)) {
        $jml_mhs = (int)$r['total'];
    }

    $q_mk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM mata_kuliah");
    if ($q_mk && $r = mysqli_fetch_assoc($q_mk)) {
        $jml_mk = (int)$r['total'];
    }

    $q_nilai = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM nilai");
    if ($q_nilai && $r = mysqli_fetch_assoc($q_nilai)) {
        $jml_nilai = (int)$r['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Overview - Devakad</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
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
            <p class="text-xs text-slate-400 font-medium">Monitoring Nilai</p>
          </div>
        </div>
        <button type="button" onclick="toggleSidebar()" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800" aria-label="Tutup menu">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <nav class="space-y-1">
        <a href="index.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white font-medium text-sm transition">
          <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Overview
        </a>
        <a href="mahasiswa.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="users" class="w-5 h-5"></i> Data Mahasiswa
        </a>
        <a href="matkul.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="book-open" class="w-5 h-5"></i> Mata Kuliah
        </a>
        <a href="dosen.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="user-cog" class="w-5 h-5"></i> Data Dosen
        </a>
        <a href="input_nilai.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="bar-chart-3" class="w-5 h-5"></i> Input Nilai
        </a>
        <a href="cetak_khs.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="file-text" class="w-5 h-5"></i> Cetak KHS
        </a>
        <a href="index.php?logout=true" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-300 hover:bg-rose-950/50 hover:text-rose-200 font-medium text-sm transition mt-4 border border-rose-900/50">
          <i data-lucide="log-out" class="w-5 h-5"></i> Keluar (Logout)
        </a>
      </nav>
    </div>

    <div class="bg-gradient-to-br from-blue-900 to-indigo-950 p-4 rounded-2xl border border-blue-800/50 text-center mt-6">
      <div class="w-10 h-10 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-2 text-emerald-400">
        <i data-lucide="database" class="w-5 h-5"></i>
      </div>
      <p class="text-[11px] text-emerald-300 font-semibold">MySQL Connected</p>
      <p class="text-[10px] text-slate-400 mt-0.5">Database: devakad1_db</p>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="flex-1 p-4 sm:p-8 overflow-y-auto space-y-6 min-w-0">
    <!-- HEADER MOBILE -->
    <div class="flex md:hidden items-center justify-between bg-slate-900 text-white px-4 py-3 rounded-2xl">
      <div class="flex items-center gap-2 font-bold text-sm">
        <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-400"></i>
        <span>DEVAKAD</span>
      </div>
      <button type="button" onclick="toggleSidebar()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white" aria-label="Buka atau tutup menu">
        <i data-lucide="menu" class="w-5 h-5"></i>
      </button>
    </div>

    <div>
      <h2 class="text-xl sm:text-2xl font-bold text-slate-900">OVERVIEW</h2>
      <p class="text-xs text-slate-500">Ringkasan data akademik mahasiswa langsung dari server MySQL.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
      <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Mahasiswa</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $jml_mhs; ?></p>
        <p class="text-xs text-emerald-600 font-semibold mt-2">Tersimpan di MySQL</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Mata Kuliah</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $jml_mk; ?></p>
        <p class="text-xs text-blue-600 font-semibold mt-2">Kurikulum Aktif</p>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nilai Terverifikasi Lulus</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-2"><?php echo $jml_nilai; ?></p>
        <p class="text-xs text-indigo-600 font-semibold mt-2">Data Transaksi Nilai</p>
      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      sidebar.classList.toggle('-translate-x-full');
      overlay.classList.toggle('hidden');
    }
  </script>
</body>
</html>