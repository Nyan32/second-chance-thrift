<?php
include ('../connect.php');
include_once ('../helper.php');

session_start();

function validateTotalItemInCart($mysqli)
{
    $query = 'SELECT SUM(jumlah_beli) AS total_keranjang FROM keranjang WHERE email=?';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $resultJumlah = $stmt->get_result();

    $row = $resultJumlah->fetch_assoc();

    $totalKeranjang = $row['total_keranjang'];

    return ($totalKeranjang < 20) ? true : false;
}

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('Asia/Bangkok');
    $error = array();
    $idProduk = trim($_POST['idProduk']);
    $srcURL = trim($_POST['srcURL']);

    $timestamp = time();
    $dateTime = date("Y-m-d H:i:s", $timestamp);


    // collect -> validate -> format -> submit

    if (!isValueInTable($mysqli, "produk", "id_produk", "i", $idProduk)) {
        array_push($error, "Produk ID tidak valid");
    } else if (!compareRowValueInTable($mysqli, "produk", "id_produk", $idProduk, "stok", "ii", ">=", 1)) {
        array_push($error, "Terjadi perubahan stok, stok tidak mencukupi");
    } else if (!validateTotalItemInCart($mysqli)) {
        array_push($error, "Jumlah beli melewati batas keranjang");
    }

    if (count($error) > 0) {
        setcookie('error', serialize($error), 0, '/');

        if ($srcURL == '/beranda.php' || $srcURL == '/produk.php') {
            header('Location: ' . $srcURL);
        } else {
            header('Location: /beranda.php');
        }
    } else {
        $query = 'SELECT COUNT(*) AS jumlah, jumlah_beli FROM keranjang WHERE email=? AND id_produk=?';
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("si", $_SESSION['email'], $idProduk);
        $stmt->execute();
        $resultQuery = $stmt->get_result();
        $stmt->close();

        $row = $resultQuery->fetch_assoc();
        $jumlah = $row['jumlah'];

        if ($jumlah > 0) {
            $prev_jumlah_beli = $row['jumlah_beli'];
            $jumlah_beli = $prev_jumlah_beli + 1;
            $query = 'UPDATE keranjang SET jumlah_beli=? WHERE email=? AND id_produk=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("isi", $jumlah_beli, $_SESSION['email'], $idProduk);
            $stmt->execute();
            $stmt->close();

            $query = 'UPDATE keranjang SET waktu_keranjang=? WHERE email=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $dateTime, $_SESSION['email']);
            $stmt->execute();
            $stmt->close();

            $query = 'UPDATE produk SET stok=(SELECT stok FROM produk WHERE id_produk=?)-1 WHERE id_produk=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ii", $idProduk, $idProduk);
            $stmt->execute();
            $stmt->close();
        } else {
            $jumlah_beli = 1;
            $query = 'INSERT INTO keranjang(id_produk, jumlah_beli, email) VALUES (?,?,?)';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("iis", $idProduk, $jumlah_beli, $_SESSION['email']);
            $stmt->execute();
            $stmt->close();

            $query = 'UPDATE keranjang SET waktu_keranjang=? WHERE email=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ss", $dateTime, $_SESSION['email']);
            $stmt->execute();
            $stmt->close();

            $query = 'UPDATE produk SET stok=(SELECT stok FROM produk WHERE id_produk=?)-1 WHERE id_produk=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ii", $idProduk, $idProduk);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: /keranjang.php');
    }
} else {
    header('Location: /akun.php');
}

$mysqli->close();