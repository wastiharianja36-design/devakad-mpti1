<?php
$host     = "sql305.infinityfree.com";
$user     = "if0_43497078";
$password = "fWgY1j74XpniH9";
$database = "if0_43497078_db_devakad1";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>