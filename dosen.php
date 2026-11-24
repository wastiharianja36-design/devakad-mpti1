<?php
session_start();
include 'koneksi.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $cek_nip = mysqli_query($koneksi, "SELECT * FROM dosen WHERE nip = '$nip'");
    if (mysqli_num_rows($cek_nip) > 0) {
        echo "<script>alert('Gagal! NIP $nip sudah terdaftar.'); window.location='dosen.php';</script>";
    } else {
        $query = "INSERT INTO dosen (nip, nama, password) VALUES ('$nip', '$nama', '$password')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data dosen berhasil ditambahkan!'); window.location='dosen.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}

if (isset($_POST['edit'])) {
    $id       = intval($_POST['id']);
    $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = "UPDATE dosen SET nip='$nip', nama='$nama', password='$password' WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Perubahan data dosen berhasil disimpan!'); window.location='dosen.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data: " . mysqli_error($koneksi) . "');</script>";
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $query = "DELETE FROM dosen WHERE id='$id'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data dosen berhasil dihapus!'); window.location='dosen.php';</script>";
    }
}

$keyword = "";
if (isset($_GET['cari']) && $_GET['cari'] != "") {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $query_tampil = "SELECT * FROM dosen WHERE nip LIKE '%$keyword%' OR nama LIKE '%$keyword%' ORDER BY id DESC";
} else {
    $query_tampil = "SELECT * FROM dosen ORDER BY id DESC";
}
$result_dosen = mysqli_query($koneksi, $query_tampil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Dosen - Devakad</title>
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
        <a href="index.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Overview
        </a>
        <a href="mahasiswa.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="users" class="w-5 h-5"></i> Data Mahasiswa
        </a>
        <a href="matkul.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="book-open" class="w-5 h-5"></i> Mata Kuliah
        </a>
        <a href="dosen.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 text-white font-medium text-sm transition">
          <i data-lucide="user-cog" class="w-5 h-5"></i> Data Dosen
        </a>
        <a href="input_nilai.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="bar-chart-3" class="w-5 h-5"></i> Input Nilai
        </a>
        <a href="cetak_khs.php" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white font-medium text-sm transition">
          <i data-lucide="file-text" class="w-5 h-5"></i> Cetak KHS
        </a>
        <a href="dosen.php?logout=true" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-300 hover:bg-rose-950/50 hover:text-rose-200 font-medium text-sm transition mt-4 border border-rose-900/50">
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

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900">DATA DOSEN PENGAMPU</h2>
        <p class="text-xs text-slate-500">Kelola akun dan hak akses dosen pengajar sistem akademik.</p>
      </div>
      <button type="button" onclick="toggleModal('modal-tambah', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 shadow-sm transition w-full md:w-auto">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Dosen Baru
      </button>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="dosen.php" class="flex flex-wrap items-center gap-2">
          <div class="relative w-full sm:w-72">
            <input type="text" name="cari" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Cari NIP atau Nama Dosen..." class="w-full bg-slate-50 border border-slate-200 text-xs rounded-xl pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
            <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
          </div>
          <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-semibold transition">Cari</button>
          <?php if ($keyword != ""): ?>
            <a href="dosen.php" class="text-xs text-rose-500 hover:underline font-semibold ml-1">Reset</a>
          <?php endif; ?>
        </form>
        <span class="text-xs text-slate-400 font-medium">Total: <?php echo mysqli_num_rows($result_dosen); ?> Dosen</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-slate-600">
          <thead class="bg-slate-900 text-white uppercase text-[10px] tracking-wider">
            <tr>
              <th class="py-3.5 px-4 w-16">No</th>
              <th class="py-3.5 px-4 w-40">NIP / ID Dosen</th>
              <th class="py-3.5 px-4">Nama Lengkap & Gelar</th>
              <th class="py-3.5 px-4">Password Login</th>
              <th class="py-3.5 px-4 text-center w-36">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php
            $no = 1;
            if (mysqli_num_rows($result_dosen) > 0) {
                while ($row = mysqli_fetch_assoc($result_dosen)) {
                    ?>
                    <tr class="hover:bg-slate-50">
                      <td class="py-3 px-4 font-semibold"><?php echo $no++; ?></td>
                      <td class="py-3 px-4 font-mono font-bold text-slate-700"><?php echo htmlspecialchars($row['nip']); ?></td>
                      <td class="py-3 px-4 font-bold text-slate-800"><?php echo htmlspecialchars($row['nama']); ?></td>
                      <td class="py-3 px-4 font-mono text-slate-500"><?php echo htmlspecialchars($row['password']); ?></td>
                      <td class="py-3 px-4 text-center whitespace-nowrap">
                        <button type="button" onclick="bukaModalEdit('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['nip'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['nama'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($row['password'], ENT_QUOTES); ?>')" class="text-blue-600 hover:underline font-semibold mr-3">Edit</button>
                        <a href="dosen.php?hapus=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus dosen ini?')" class="text-rose-500 hover:underline font-semibold">Hapus</a>
                      </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='5' class='py-6 text-center text-slate-400'>Data dosen tidak ditemukan.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- MODAL TAMBAH DOSEN -->
  <div id="modal-tambah" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <form method="POST" action="dosen.php" class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
      <h3 class="text-lg font-bold text-slate-900">Tambah Dosen Baru</h3>
      <div class="space-y-3 text-xs">
        <div>
          <label class="block font-bold text-slate-600 mb-1">NIP / ID Dosen</label>
          <input type="text" name="nip" required placeholder="Contoh: 19850103" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-mono focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block font-bold text-slate-600 mb-1">Nama Lengkap & Gelar</label>
          <input type="text" name="nama" required placeholder="Contoh: Dr. Nama Dosen, M.Kom." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block font-bold text-slate-600 mb-1">Password Login</label>
          <input type="text" name="password" required placeholder="Password akun dosen" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="toggleModal('modal-tambah', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:bg-slate-100">Batal</button>
        <button type="submit" name="tambah" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700">Simpan Dosen</button>
      </div>
    </form>
  </div>

  <!-- MODAL EDIT DOSEN -->
  <div id="modal-edit" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4 z-50">
    <form method="POST" action="dosen.php" class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
      <h3 class="text-lg font-bold text-slate-900">Edit Data Dosen</h3>
      <input type="hidden" name="id" id="edit-id">
      <div class="space-y-3 text-xs">
        <div>
          <label class="block font-bold text-slate-600 mb-1">NIP / ID Dosen</label>
          <input type="text" name="nip" id="edit-nip" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-mono focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block font-bold text-slate-600 mb-1">Nama Lengkap & Gelar</label>
          <input type="text" name="nama" id="edit-nama" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block font-bold text-slate-600 mb-1">Password Login</label>
          <input type="text" name="password" id="edit-password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
      </div>
      <div class="flex justify-end gap-2 pt-2">
        <button type="button" onclick="toggleModal('modal-edit', false)" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:bg-slate-100">Batal</button>
        <button type="submit" name="edit" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700">Simpan Perubahan</button>
      </div>
    </form>
  </div>

  <script>
    lucide.createIcons();
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebar-overlay');
      sidebar.classList.toggle('-translate-x-full');
      overlay.classList.toggle('hidden');
    }
    function toggleModal(modalId, show) {
      document.getElementById(modalId).classList.toggle('hidden', !show);
    }
    function bukaModalEdit(id, nip, nama, password) {
      document.getElementById('edit-id').value = id;
      document.getElementById('edit-nip').value = nip;
      document.getElementById('edit-nama').value = nama;
      document.getElementById('edit-password').value = password;
      toggleModal('modal-edit', true);
    }
  </script>
</body>
</html>