<?php
include ('../connect.php');
include_once ('../helper.php');

session_start();

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('Asia/Bangkok');
    $error = array();

    $query = 'SELECT id_produk, jumlah_beli FROM keranjang WHERE email=?';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $kodeTransaksi = generateUid();
    $timestamp = time();
    $dateTime = date("Y-m-d H:i:s", $timestamp);
    $status = 'waiting';
    $email = $row['email'];
    $buktiTransaksi = '';

    // collect -> validate -> format -> submit

    $jumlahBarang = 0;

    while ($row = $result->fetch_assoc()) {
        $idProduk = $row['id_produk'];
        $jumlahBeli = $row['jumlah_beli'];

        $query = 'INSERT INTO riwayat_transaksi(email, id_produk, waktu_transaksi, status, kode_transaksi, bukti_transaksi, jumlah_beli)
        VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sissssi", $_SESSION['email'], $idProduk, $dateTime, $status, $kodeTransaksi, $buktiTransaksi, $jumlahBeli);
        $stmt->execute();
        $stmt->close();

        $jumlahBarang += 1;
    }

    $query = 'DELETE FROM keranjang WHERE email=?';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();

    if ($jumlahBarang <= 0) {
        array_push($error, 'Keranjangmu sebelumnya sudah expired');
        setcookie('error', serialize($error), 0, '/');
        header("Location: /keranjang.php");
    } else {
        header("Location: /riwayat_transaksi.php");
    }
} else {
    header('Location: /akun.php');
}