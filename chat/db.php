<?php
$host = 'localhost';
$user = 'root'; // ubah jika pakai user lain
$pass = '';     // ubah sesuai password MySQL kamu
$dbname = 'chatdb';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
?>
