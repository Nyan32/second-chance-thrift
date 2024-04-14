<?php

function intToRupiahStr($integer)
{
    $result = "Rp. " . number_format($integer, 0, ',', '.');
    return $result;
}

function areAllDigits($str)
{
    $result = preg_match('/^\d+$/', $str);
    return $result;
}

function isValuelUniqueInTable($mysqli, $table, $column, $dataType, $value)
{
    $query = "SELECT COUNT(*) AS jumlah FROM $table WHERE $column = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($dataType, $value);
    $stmt->execute();
    $resultQuery = $stmt->get_result();

    $stmt->close();
    $row = $resultQuery->fetch_assoc();
    $result = $row['jumlah'];

    return ($result < 1) ? true : false;
}

function formatNama($str)
{
    $result = ucwords(strtolower($str));
    return $result;
}

function formatEmail($str)
{
    $result = strtolower($str);
    return $result;
}

function validateLogin($mysqli, $email, $password)
{
    $query = 'SELECT COUNT(*) AS jumlah FROM akun WHERE email=? and password=?';
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $resultQuery = $stmt->get_result();

    $stmt->close();
    $row = $resultQuery->fetch_assoc();
    $result = $row['jumlah'];

    return ($result > 0) ? true : false;
}

function isValueInTable($mysqli, $table, $column, $dataType, $value)
{
    $query = "SELECT COUNT(*) AS jumlah FROM $table WHERE $column = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($dataType, $value);
    $stmt->execute();
    $resultQuery = $stmt->get_result();

    $stmt->close();
    $row = $resultQuery->fetch_assoc();
    $result = $row['jumlah'];

    return ($result > 0) ? true : false;
}

function compareRowValueInTable($mysqli, $table, $column_id, $id, $column, $dataType, $comparison, $value)
{
    $query = "SELECT ($column $comparison ?) AS isTrue FROM $table WHERE $column_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($dataType, $value, $id);
    $stmt->execute();
    $resultQuery = $stmt->get_result();

    $stmt->close();
    $row = $resultQuery->fetch_assoc();
    $result = $row['isTrue'];

    return $result;
}

function convertTo999Plus($num)
{
    if ($num > 999) {
        return '999+';
    } else {
        return strval($num);
    }
}

function generateUid()
{
    $salt = "second_chance_thrift";
    $uid = time() . $salt;
    $encryptedUid = hash('sha256', $uid);
    return $encryptedUid;
}

function validateFileExtension($ext)
{
    $allowed_ext = ['jpg', 'png', 'jpeg'];
    return in_array($ext, $allowed_ext);
}

function formatTimeToShow($inputDatetime)
{
    $datetime = new DateTime($inputDatetime);
    $formattedDatetime = $datetime->format('d F Y - H:i:s');
    return $formattedDatetime;
}

function validateSessionLogin($mysqli)
{

}