<?php

include ('../connect.php');
include_once ('../helper.php');

session_start();

unset($_SESSION['email']);

header("Location: /beranda.php");