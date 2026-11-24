<?php
session_start();
include 'koneksi.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$mhs_options = mysqli_query($koneksi, "SELECT id, nim, nama, prodi, semester FROM mahasiswa ORDER BY nama ASC");
$selected_id = isset($_GET['mhs_id']) ? intval($_GET['mhs_id']) : 0;

$mhs_data = null;
$khs_rows = [];
$total_sks = 0;
$total_bobot_sks = 0;

if ($selected_id > 0) {
    $q_mhs = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE id = '$selected_id' LIMIT 1");
    if ($q_mhs && mysqli_num_rows($q_mhs) > 0) {
        $mhs_data = mysqli_fetch_assoc($q_mhs);
    }

    $q_nilai = mysqli_query($koneksi, "
        SELECT n.*, mk.kode_mk, mk.nama_mk, mk.sks
        FROM nilai n
        INNER JOIN mata_kuliah mk ON n.mk_id = mk.id
        WHERE n.mahasiswa_id = '$selected_id'
        ORDER BY mk.kode_mk ASC
    ");
    if ($q_nilai) {
        while ($row = mysqli_fetch_assoc($q_nilai)) {
            $sks = intval($row['sks']);
            $bobot = floatval($row['bobot']);
            $total_sks += $sks;
            $total_bobot_sks += ($sks * $bobot);
            $khs_rows[] = $row;
        }
    }
}

$ips = $total_sks > 0 ? number_format($total_bobot_sks / $total_sks, 2) : "0.00";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak KHS - Devakad</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; }
    @media print {
      #sidebar, #sidebar-overlay, .no-print { display: none !important; }
      main { padding: 0 !important; background: #fff !important; }
    }
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
        <a href="index.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
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
        <a href="cetak_khs.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white font-medium text-sm transition">
          <i data-lucide="file-text" class="w-5 h-5"></i> Cetak KHS
        </a>
        <a href="cetak_khs.php?logout=true" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-300 hover:bg-rose-950/50 hover:text-rose-200 font-medium text-sm transition mt-4 border border-rose-900/50">
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
    <div class="flex md:hidden items-center justify-between bg-slate-900 text-white px-4 py-3 rounded-2xl no-print">
      <div class="flex items-center gap-2 font-bold text-sm">
        <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-400"></i>
        <span>DEVAKAD</span>
      </div>
      <button type="button" onclick="toggleSidebar()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white" aria-label="Buka atau tutup menu">
        <i data-lucide="menu" class="w-5 h-5"></i>
      </button>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900">CETAK KARTU HASIL STUDI (KHS)</h2>
        <p class="text-xs text-slate-500">Pilih mahasiswa untuk menampilkan dan mencetak transkrip KHS.</p>
      </div>
      <?php if ($mhs_data): ?>
        <button type="button" onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition w-full md:w-auto">
          <i data-lucide="printer" class="w-4 h-4"></i> Cetak KHS
        </button>
      <?php endif; ?>
    </div>

    <form method="GET" action="cetak_khs.php" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row gap-3 items-stretch sm:items-center no-print">
      <select name="mhs_id" required class="flex-1 bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">-- Pilih Mahasiswa --</option>
        <?php if ($mhs_options) { while ($opt = mysqli_fetch_assoc($mhs_options)) { ?>
          <option value="<?php echo $opt['id']; ?>" <?php echo ($selected_id === (int)$opt['id']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($opt['nim'] . ' - ' . $opt['nama']); ?>
          </option>
        <?php } } ?>
      </select>
      <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition">
        Tampilkan KHS
      </button>
    </form>

    <?php if ($mhs_data): ?>
      <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-5">
        <div class="border-b pb-4 flex flex-col sm:flex-row justify-between gap-2 text-xs">
          <div>
            <p><span class="font-bold">Nama:</span> <?php echo htmlspecialchars($mhs_data['nama']); ?></p>
            <p><span class="font-bold">NIM:</span> <?php echo htmlspecialchars($mhs_data['nim']); ?></p>
          </div>
          <div>
            <p><span class="font-bold">Prodi:</span> <?php echo htmlspecialchars($mhs_data['prodi'] ?? '-'); ?></p>
            <p><span class="font-bold">Semester:</span> <?php echo htmlspecialchars($mhs_data['semester'] ?? '-'); ?></p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-900 text-white uppercase text-[10px]">
              <tr>
                <th class="py-3 px-4">Kode MK</th>
                <th class="py-3 px-4">Mata Kuliah</th>
                <th class="py-3 px-4 text-center">SKS</th>
                <th class="py-3 px-4 text-center">Nilai</th>
                <th class="py-3 px-4 text-center">Huruf</th>
                <th class="py-3 px-4 text-center">Bobot</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php if (count($khs_rows) > 0): foreach ($khs_rows as $kr): ?>
                <tr>
                  <td class="py-3 px-4 font-mono"><?php echo htmlspecialchars($kr['kode_mk']); ?></td>
                  <td class="py-3 px-4 font-semibold text-slate-800"><?php echo htmlspecialchars($kr['nama_mk']); ?></td>
                  <td class="py-3 px-4 text-center"><?php echo htmlspecialchars($kr['sks']); ?></td>
                  <td class="py-3 px-4 text-center"><?php echo htmlspecialchars($kr['nilai_angka']); ?></td>
                  <td class="py-3 px-4 text-center font-bold"><?php echo htmlspecialchars($kr['huruf']); ?></td>
                  <td class="py-3 px-4 text-center"><?php echo htmlspecialchars($kr['bobot']); ?></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="6" class="py-5 text-center text-slate-400">Belum ada nilai tercatat untuk mahasiswa ini.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="flex flex-wrap justify-between gap-4 pt-2 text-xs font-bold text-slate-800">
          <span>Total SKS: <?php echo $total_sks; ?></span>
          <span>IPS: <?php echo $ips; ?></span>
        </div>
      </div>
    <?php endif; ?>
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