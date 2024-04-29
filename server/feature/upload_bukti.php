<?php
include ('../connect.php');
include_once ('../helper.php');

session_start();

function validateTransaction($mysqli, $kodeTransaksi)
{
    $query = 'SELECT status, kode_transaksi FROM riwayat_transaksi WHERE kode_transaksi=? GROUP BY kode_transaksi';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $kodeTransaksi);
    $stmt->execute();
    $resultQuery = $stmt->get_result();

    $stmt->close();
    $row = $resultQuery->fetch_assoc();

    return ($row['status'] != 'fail') ? true : false;
}

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && validateSessionLogin($mysqli, $_SESSION['email']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('Asia/Bangkok');
    $error = array();

    $file = (isset($_FILES["buktiTransaksi"])) ? $_FILES['buktiTransaksi'] : null;
    $kodeTransaksi = trim($_POST['kodeTransaksi']);
    $maxFileSize = 10 * 1024 * 1024;  // 10 MB

    // collect -> validate -> format -> submit

    if ($file == null) {
        array_push($error, "Tidak ada bukti yang terkirim");
    } else if ($file["error"] != 0) {
        array_push($error, "Terdapat kesalahan pada file");
    } else if (!validateFileExtension(pathinfo($file['name'], PATHINFO_EXTENSION))) {
        array_push($error, "Tipe file tidak diperbolehkan (valid: jpg, png, jpeg)");
    } else if ($file['size'] > $maxFileSize){
        array_push($error, "Ukuran file tidak dapat melebihi 10MB");
    }

    if (!isValueInTable($mysqli, "riwayat_transaksi", "kode_transaksi", "s", $kodeTransaksi)) {
        array_push($error, "Kode transaksi tidak valid");
    } else if (!validateTransaction($mysqli, $kodeTransaksi)) {
        array_push($error, "Transaksi sudah tidak valid");
    }

    if (count($error) > 0) {
        setcookie('error', serialize($error), 0, '/');
        header('Location: /riwayat_transaksi.php');
    } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $kodeTransaksi . '.' . $ext;

        move_uploaded_file($file['tmp_name'], "../bukti/" . $fileName);

        $query = 'UPDATE riwayat_transaksi SET status="validating", bukti_transaksi=? WHERE kode_transaksi=?';
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $fileName, $kodeTransaksi);
        $stmt->execute();
        $stmt->close();

        header('Location: /riwayat_transaksi.php');
    }

} else {
    header('Location: /akun.php');
}