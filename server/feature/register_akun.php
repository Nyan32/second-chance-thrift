<?php

include ('../connect.php');
include_once ('../helper.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = array();
    $prev_input = array();

    $nama = trim($_POST['nama']);
    $nomorTelepon = trim($_POST['nomorTelepon']);
    $alamat = trim($_POST['alamat']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

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

    if ($email == '') {
        array_push($error, 'Email tidak boleh kosong');
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($error, 'Email tidak valid');
    } else if (!isValuelUniqueInTable($mysqli, "akun", "email", "s", $email)) {
        array_push($error, 'Email sudah terdaftar');
    }

    if ($password == '') {
        array_push($error, 'Password tidak boleh kosong');
    } else if (strlen($password) < 8) {
        array_push($error, 'Password minimal terdiri dari 8 karakter');
    }

    if (count($error) > 0) {
        $prev_input['nama'] = $nama;
        $prev_input['nomorTelepon'] = $nomorTelepon;
        $prev_input['alamat'] = $alamat;
        $prev_input['email'] = $email;

        setcookie('error', serialize($error), 0, '/');
        setcookie('prev_input', serialize($prev_input), 0, '/');
        header('Location: /akun.php?register=true');
    } else {
        $namaFMT = formatNama($nama);
        $nomorTeleponFMT = $nomorTelepon;
        $alamatFMT = $alamat;
        $emailFMT = formatEmail($email);
        $passwordFMT = $password;
        $emailHash = hash('sha256', $emailFMT . '-second_chance_thrift');

        $query = 'INSERT INTO akun (email, password, nama, alamat, nomor_telepon, email_hash) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssss", $emailFMT, $passwordFMT, $namaFMT, $alamatFMT, $nomorTeleponFMT, $emailHash);
        $stmt->execute();
        $stmt->close();

        header('Location: /akun.php');
    }
}

$mysqli->close();