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

$query = "SELECT * FROM produk ORDER BY jumlah_dibeli DESC LIMIT 20";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<?php ob_start(); ?>
<h3 class="thrift-shop-small-font">
    Selamat datang di SECOND CHANCE THRIFT. Kami menjual baju bekas berkualitas.
    SECOND CHANCE THRIFT menjual berbagai jenis pakaian bekas,
    mulai dari pakaian bekas anak-anak sampai dengan orang dewasa.
    Toko kami menjual pakaian bekas karena harga murah namun berkualitas dan
    lebih ramah lingkungan.
</h3>

<hr>
<?php
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

<div class="d-flex flex-wrap">
    <?php
    while ($row = $result->fetch_assoc()) {
        ?>
        <div class="col-6 col-md-4 col-lg-3 p-2">
            <div class="d-flex flex-column thrift-shop-card">
                <div class="thrift-shop-font-red fw-bold text-center p-2">
                    <?= $row['nama'] ?>
                </div>
                <div class="p-2">
                    <div class="outer-1-1">
                        <div class="inner">
                            <img class="w-100 h-100" src="/server/produk/<?= $row['gambar'] ?>" alt=""
                                style="object-fit:cover">
                        </div>
                    </div>
                </div>
                <div class="p-2 thrift-shop-font-red text-center">
                    <?php
                    echo (intToRupiahStr($row['harga']));
                    ?>
                </div>
                <div class="d-flex">
                    <form class="col-6" action="/server/feature/tambah_barang.php" method="post">
                        <input name="idProduk" type="text" value="<?= $row['id_produk'] ?>" hidden>
                        <input name="srcURL" type="text" value="/beranda.php" hidden>
                        <button
                            class="primary d-flex align-items-center thrift-shop-transparent justify-content-center w-100"><img
                                src="/static/image/icons8-shopping-cart-24.png" alt="shop cart">&nbsp;Beli</button>
                    </form>
                    <button
                        class="col-6 detail d-flex align-items-center thrift-shop-transparent thrift-shop-bg-white justify-content-center"><img
                            src="/static/image/icons8-detail-24.png" alt="detail">&nbsp;Detail</button>
                </div>

            </div>
        </div>
        <?php
    }
    ?>
</div>
<?php $content = ob_get_clean(); ?>
<?php include ('static/layout/layout.php'); ?>
<?php $mysqli->close(); ?>