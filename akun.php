<?php
include ('server/connect.php');
include_once ('server/helper.php');

$expirationDate = date('D, d M Y H:i:s', strtotime("+1 day")) . ' GMT';

header("Cache-Control: must-revalidate");
header("Expires: $expirationDate");

session_start();

if (isset($_COOKIE['error'])) {
    $error = unserialize($_COOKIE['error']);
} else {
    $error = array();
}
setcookie('error', '', time() - 3600, '/');

if (isset($_COOKIE['prev_input'])) {
    $prev_input = unserialize($_COOKIE['prev_input']);
} else {
    $prev_input = array();
}
setcookie('prev_input', '', time() - 3600, '/');
if (isset($_SESSION['email']) && $_SESSION['email'] != '') {
    $userLogin = $_SESSION['email'];
    $isRegister = false;
} else {
    $userLogin = "";
    if (isset($_GET['register']) && $_GET['register'] == 'true') {
        $isRegister = true;
    } else {
        $isRegister = false;
    }
}

?>

<?php ob_start(); ?>
<?php
if ($userLogin == '') {
    if ($isRegister == true) {

        if (count($error) > 0) {
            ?>
            <div class="alert-danger rounded p-2 mb-3">
                <ul>
                    <?php
                    foreach ($error as $e) {
                        ?>
                        <li><?= $e ?></li>
                        <?php
                    } ?>
                </ul>
            </div>
            <?php
        }
        ?>


        <h2 class="thrift-shop">
            User Register
        </h2>

        <hr>

        <form action="/server/feature/register_akun.php" method="post">
            <div class="p-2">
                <label for="" class="form-label">Nama:</label>
                <input type="text" name="nama" class="thrift-shop form-control"
                    value="<?= (isset($prev_input['nama']) && $prev_input['nama'] != '') ? $prev_input['nama'] : ''; ?>">
            </div>
            <div class="p-2">
                <label for="" class="form-label">Nomor Telepon:</label>
                <input type="text" name="nomorTelepon" class="thrift-shop form-control"
                    value="<?= (isset($prev_input['nomorTelepon']) && $prev_input['nomorTelepon'] != '') ? $prev_input['nomorTelepon'] : ''; ?>">
            </div>
            <div class="p-2">
                <label for="" class="form-label">Alamat:</label>
                <textarea class="thrift-shop form-control" name="alamat"
                    rows="3"><?= (isset($prev_input['alamat']) && $prev_input['alamat'] != '') ? $prev_input['alamat'] : ''; ?></textarea>
            </div>
            <div class="p-2">
                <label for="" class="form-label">Email:</label>
                <input type="email" name="email" class="thrift-shop form-control"
                    value="<?= (isset($prev_input['email']) && $prev_input['email'] != '') ? $prev_input['email'] : ''; ?>">
            </div>
            <div class="p-2">
                <label for="" class="form-label">Password:</label>
                <input type="password" name="password" class="thrift-shop form-control">
            </div>
            <div class="d-flex justify-content-center p-2">
                <button
                    class="d-flex align-items-center thrift-shop-transparent thrift-shop-bg-red p-2 justify-content-center thrift-shop-font-white"><img
                        src="/static/image/icons8-login-24.png" alt="login">Register</button>
            </div>
            <div class="text-center">
                <a href="/akun.php" class="thrift-shop register">Sudah punya akun? Masuk sekarang!</a>
            </div>
        </form>
        <?php
    } else {
        if (count($error) > 0) {
            ?>
            <div class="alert-danger rounded p-2 mb-3">
                <ul>
                    <?php
                    foreach ($error as $e) {
                        ?>
                        <li><?= $e ?></li>
                        <?php
                    } ?>
                </ul>
            </div>
            <?php
        }
        ?>
        <h2 class="thrift-shop">
            User Login
        </h2>

        <hr>

        <form action="/server/feature/login_akun.php" method="post">
            <div class="p-2">
                <label for="" class="form-label">Email:</label>
                <input type="email" name="email" class="thrift-shop form-control"
                    value="<?= (isset($prev_input['email']) && $prev_input['email'] != '') ? $prev_input['email'] : ''; ?>">
            </div>
            <div class="p-2">
                <label for="" class="form-label">Password:</label>
                <input type="password" name="password" class="thrift-shop form-control">
            </div>
            <div class="d-flex justify-content-center p-2">
                <button
                    class="d-flex align-items-center thrift-shop-transparent thrift-shop-bg-red p-2 justify-content-center thrift-shop-font-white"><img
                        src="/static/image/icons8-login-24.png" alt="login">Login</button>
            </div>
            <div class="text-center">
                <a href="/akun.php?register=true" class="thrift-shop register">Belum punya akun? Daftar sekarang!</a>
            </div>
        </form>
        <?php
    }
}
?>

<?php $content = ob_get_clean(); ?>

<?php include ('static/layout/layout.php'); ?>

<?php
$mysqli->close();
?>