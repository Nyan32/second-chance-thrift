<?php
include ('../connect.php');
include_once ('../helper.php');

session_start();


function countSumOfDict($associativeArray)
{
    $sum = 0;
    foreach ($associativeArray as $value) {
        $sum += $value;
    }

    return $sum;
}

function validateTotalItemInCart($mysqli, $zipIdJumlah)
{
    $totalNew = countSumOfDict($zipIdJumlah);
    return ($totalNew <= 20) ? true : false;
}

function validateItemsNeeds($mysqli, $validIdProduk, $zipIdJumlah)
{
    for ($i = 0; $i < count($validIdProduk); $i++) {
        $query = 'SELECT stok FROM produk WHERE id_produk=?';
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $validIdProduk[$i]);
        $stmt->execute();
        $resultJumlah = $stmt->get_result();

        $stmt->close();
        $row = $resultJumlah->fetch_assoc();
        $stokTersedia = $row['stok'];

        $query = 'SELECT jumlah_beli FROM keranjang WHERE email=? AND id_produk=?';
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("si", $_SESSION['email'], $validIdProduk[$i]);
        $stmt->execute();
        $resultJumlah = $stmt->get_result();

        $stmt->close();
        $row = $resultJumlah->fetch_assoc();
        $old_value = $row['jumlah_beli'];

        $needs = $zipIdJumlah[$validIdProduk[$i]] - $old_value;
        if ($stokTersedia < $needs) {
            return false;
        }
    }
    return true;
}

function validateIdProdukandItsValue($mysqli, $idProduk, $zipIdJumlah)
{
    if (count($idProduk) > 0) {
        $placeholders = implode(',', array_fill(0, count($idProduk), '?'));

        $query = "SELECT * FROM produk WHERE id_produk IN ($placeholders)";
        $stmt = $mysqli->prepare($query);

        $types = str_repeat('i', count($idProduk));
        $bindParams = array_merge([$types], $idProduk);
        $params = [];
        foreach ($bindParams as $key => &$value) {
            $params[$key] = &$value;
        }
        call_user_func_array([$stmt, 'bind_param'], $params);

        $stmt->execute();
        $resultQuery = $stmt->get_result();
        $stmt->close();

        $validIdProduk = array();
        while ($row = $resultQuery->fetch_assoc()) {
            array_push($validIdProduk, $row['id_produk']);
        }

        for ($i = 0; $i < count($validIdProduk); $i++) {
            if (!areAllDigits($zipIdJumlah[$validIdProduk[$i]])) {
                unset($zipIdJumlah[$validIdProduk[$i]]);
                unset($validIdProduk[$i]);
            } else if ($zipIdJumlah[$validIdProduk[$i]] < 0) {
                $zipIdJumlah[$validIdProduk[$i]] = 0;
            }
        }
        $validIdProduk = array_values($validIdProduk);

        $isThereValidIdProduk = (count($validIdProduk) > 0) ? true : false;

        return array("validIdProduk" => $validIdProduk, "zipIdJumlah" => $zipIdJumlah);
    } else {
        return array("validIdProduk" => array(), "zipIdJumlah" => array());
    }

}

function findDiffIdFromInputandDB($mysqli, $validIdProduk)
{
    $query = 'SELECT id_produk FROM keranjang WHERE email=?';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $_SESSION['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $specificColumn = $result->fetch_all(MYSQLI_NUM);
    $idProdukInDB = array_column($specificColumn, 0);
    $difference = array_values(array_diff($idProdukInDB, $validIdProduk));

    $isThereDiffId = (count($difference) > 0) ? true : false;

    return array("isThereDiffId" => $isThereDiffId, "difference" => $difference);
}

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    date_default_timezone_set('Asia/Bangkok');

    $error = array();
    $idProduk = (isset($_POST['idProduk'])) ? $_POST['idProduk'] : array();
    $jumlahBeli = (isset($_POST['jumlahBeli'])) ? $_POST['jumlahBeli'] : array();
    $zipIdJumlah = array_combine($idProduk, $jumlahBeli);

    $timestamp = time();
    $dateTime = date("Y-m-d H:i:s", $timestamp);

    // collect -> validate -> format -> submit

    $resultIdProdukValidation = validateIdProdukandItsValue($mysqli, $idProduk, $zipIdJumlah);
    $validIdProduk = $resultIdProdukValidation['validIdProduk'];
    $zipIdJumlah = $resultIdProdukValidation['zipIdJumlah'];

    if (!validateTotalItemInCart($mysqli, $zipIdJumlah)) {
        array_push($error, "Jumlah beli melewati batas keranjang");
    } else if (!validateItemsNeeds($mysqli, $validIdProduk, $zipIdJumlah)) {
        array_push($error, "Terjadi perubahan stok, stok tidak mencukupi");
    }

    if (count($error) < 1) {
        for ($i = 0; $i < count($validIdProduk); $i++) {
            $query = 'UPDATE produk SET stok=(SELECT stok FROM produk WHERE id_produk=?) - (? - (SELECT jumlah_beli FROM keranjang WHERE email=? AND id_produk=?)) WHERE id_produk=?';
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("iisii", $validIdProduk[$i], $zipIdJumlah[$validIdProduk[$i]], $_SESSION['email'], $validIdProduk[$i], $validIdProduk[$i]);
            $stmt->execute();
            $stmt->close();

            if ($zipIdJumlah[$validIdProduk[$i]] == 0) {
                $query = 'DELETE FROM keranjang WHERE email=? AND id_produk=?';
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("si", $_SESSION['email'], $validIdProduk[$i]);
                $stmt->execute();
                $stmt->close();
            } else {
                $query = 'UPDATE keranjang SET jumlah_beli=?, waktu_keranjang=? WHERE email=? AND id_produk=?';
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("issi", $zipIdJumlah[$validIdProduk[$i]], $dateTime, $_SESSION['email'], $validIdProduk[$i]);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    $resultfindDiffIdFromInputandDB = findDiffIdFromInputandDB($mysqli, $validIdProduk);
    $isThereDiffId = $resultfindDiffIdFromInputandDB['isThereDiffId'];
    $difference = $resultfindDiffIdFromInputandDB['difference'];

    if ($isThereDiffId) {
        $placeholdersToDelete = implode(',', array_fill(0, count($difference), '?'));

        $query = "SELECT jumlah_beli, id_produk FROM keranjang WHERE id_produk IN ($placeholdersToDelete) AND email=?";
        $stmt = $mysqli->prepare($query);

        $types = str_repeat('i', count($difference)) . 's';
        $bindParams = array_merge([$types], $difference, [$_SESSION['email']]);
        $params = [];
        foreach ($bindParams as $key => &$value) {
            $params[$key] = &$value;
        }
        call_user_func_array([$stmt, 'bind_param'], $params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $idProdukReturn = $row['id_produk'];
            $jumlahReturn = $row['jumlah_beli'];

            $query = "UPDATE produk SET stok=(SELECT stok FROM produk WHERE id_produk=?) + ? WHERE id_produk=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("iii", $idProdukReturn, $jumlahReturn, $idProdukReturn);
            $stmt->execute();
            $stmt->close();
        }

        $query = "DELETE FROM keranjang WHERE id_produk IN ($placeholdersToDelete) AND email=?";
        $stmt = $mysqli->prepare($query);

        $types = str_repeat('i', count($difference)) . 's';
        $bindParams = array_merge([$types], $difference, [$_SESSION['email']]);
        $params = [];
        foreach ($bindParams as $key => &$value) {
            $params[$key] = &$value;
        }
        call_user_func_array([$stmt, 'bind_param'], $params);
        $stmt->execute();
    }

    setcookie('error', serialize($error), 0, '/');
    header("Location: /keranjang.php");

} else {
    header('Location: /akun.php');
}

$mysqli->close();
