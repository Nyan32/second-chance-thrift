<?php

include ('../connect.php');
include_once ('../helper.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = array();
    $prev_input = array();

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // collect -> validate -> format -> submit

    if ($email == '') {
        array_push($error, 'Email tidak boleh kosong');
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($error, 'Email tidak valid');
    }

    if ($password == '') {
        array_push($error, 'Password tidak boleh kosong');
    }

    $emailFMT = formatEmail($email);
    $passwordFMT = $password;
    if (count($error) < 1 && !validateLogin($mysqli, $emailFMT, $passwordFMT)) {
        array_push($error, 'Kredensial login tidak ditemukan');
    }

    if (count($error) > 0) {
        $prev_input['email'] = $email;
        setcookie('error', serialize($error), 0, '/');
        setcookie('prev_input', serialize($prev_input), 0, '/');
        header('Location: /akun.php');
    } else {
        $_SESSION['email'] = $emailFMT;
        header('Location: /beranda.php');
    }
}

$mysqli->close();