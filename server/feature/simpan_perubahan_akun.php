<?php

include ('../connect.php');
include_once ('../helper.php');

session_start();

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = array();
    $prev_input = array();

    $nama = trim($_POST['nama']);
    $nomorTelepon = trim($_POST['nomorTelepon']);
    $alamat = trim($_POST['alamat']);
    $password = $_POST['password'];
    $email = $_SESSION['email'];

    // collect -> validate -> format -> submit

    if ($nama == '') {
        array_push($error, 'Nama tidak boleh kosong');
    }

    if ($nomorTelepon == '') {
        array_push($error, 'Nomor telepon tidak boleh kosong');
    } else if (!areAllDigits($nomorTelepon)) {
        array_push($error, 'Nomor telepon hanya boleh terdiri dari angka');
    } else if (strlen($nomorTelepon) > 13 or strlen($nomorTelepon) < 10) {
        array_push($error, 'Nomor telepon terdiri dari 10-13 angka');
    }

    if ($alamat == '') {
        array_push($error, 'Alamat tidak boleh kosong');
    }

    if (strlen($password) < 8 && strlen($password) > 0) {
        array_push($error, 'Password minimal terdiri dari 8 karakter');
    }

    if (count($error) > 0) {
        $prev_input['nama'] = $nama;
        $prev_input['nomorTelepon'] = $nomorTelepon;
        $prev_input['alamat'] = $alamat;

        setcookie('error', serialize($error), 0, '/');
        setcookie('prev_input', serialize($prev_input), 0, '/');
        header('Location: /akun.php');
    } else {
        $namaFMT = formatNama($nama);
        $nomorTeleponFMT = $nomorTelepon;
        $alamatFMT = $alamat;
        $passwordFMT = $password;

        if (strlen($passwordFMT) == 0) {
            $query = 'UPDATE akun SET nama=?, alamat=?, nomor_telepon=? WHERE email=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssss", $namaFMT, $alamatFMT, $nomorTeleponFMT, $email);
        } else {
            $query = 'UPDATE akun SET password=?, nama=?, alamat=?, nomor_telepon=? WHERE email=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssss", $passwordFMT, $namaFMT, $alamatFMT, $nomorTeleponFMT, $email);
        }

        $stmt->execute();
        $stmt->close();

        header('Location: /akun.php');
    }
}

$mysqli->close();