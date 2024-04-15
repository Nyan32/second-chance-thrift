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

$query = "SELECT p.*, k.nama AS nama_kategori FROM produk p JOIN kategori k ON p.id_kategori=k.id_kategori ORDER BY jumlah_dibeli DESC LIMIT 20";
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

<div class="modal fade" id="showDetailProduk" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="staticBackdropLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="btn-close thrift-shop-modal border-0" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap">
                    <div class="p-2 col-12 col-md-4">
                        <div class="outer-1-1">
                            <div class="inner">
                                <img id="gambarProdukModal" class="w-100 h-100 gambar" src="" alt="gambar produk"
                                    style="object-fit:cover">
                            </div>
                        </div>
                    </div>
                    <div class="p-2 col-12 col-md-8">
                        <h3 id="namaProdukModal" class="thrift-shop-font-red fw-bold p-2" style="font-size: 2rem">
                        </h3>
                        <h4 id="namaKategoriProdukModal" class="fw-bold p-2 thrift-shop-small-font">
                        </h4>
                        <hr>
                        <p id="deskripsiProdukModal" class="p-2"></p>
                        <br>
                        <p id="hargaProdukModal" class="d-flex align-items-center p-2 m-0"><img
                                src="/static/image/icons8-price-24.png" alt="harga">&nbsp;Harga:&nbsp;<span></span></p>
                        <p id="diskonProdukModal" class="d-flex align-items-center p-2 m-0"><img
                                src="/static/image/icons8-discount-24.png" alt="diskon">&nbsp;Diskon:&nbsp;<span></span>
                        </p>
                        <p id="beratProdukModal" class="d-flex align-items-center p-2 m-0"><img
                                src="/static/image/icons8-weight-24.png" alt="berat">&nbsp;Berat:&nbsp;<span></span>gr
                        </p>
                        <p id="stokProdukModal" class="d-flex align-items-center p-2 m-0"><img
                                src="/static/image/icons8-box-24.png" alt="berat">&nbsp;Stok:&nbsp;<span></span>
                        </p>
                        <p id="jumlahDibeliProdukModal" class="d-flex align-items-center p-2 m-0"><img
                                src="/static/image/icons8-shopping-cart-24.png" alt="berat">&nbsp;Jumlah
                            dibeli:&nbsp;<span></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                            <img class="w-100 h-100" src="/server/produk/<?= $row['gambar'] ?>" alt="gambar produk"
                                style="object-fit:cover">
                        </div>
                    </div>
                </div>
                <div class="p-2 thrift-shop-font-red text-center">
                    <?= intToRupiahStr($row['harga']);
                    ?>
                </div>
                <div class="detailInfo" data-id-produk="<?= $row['id_produk'] ?>" hidden>
                    <p class="nama"><?= $row['nama'] ?></p>
                    <p class="deskripsi"><?= $row['deskripsi'] ?></p>
                    <p class="harga"><?= intToRupiahStr($row['harga']) ?></p>
                    <p class="stok"><?= convertTo999Plus($row['stok']) ?></p>
                    <p class="berat"><?= $row['berat'] ?></p>
                    <p class="gambar"><?= $row['gambar'] ?></p>
                    <p class="jumlahDibeli"><?= formatJumlahDibeli($row['jumlah_dibeli']) ?></p>
                    <p class="diskon"><?= intToRupiahStr($row['diskon']) ?></p>
                    <p class="kategori"><?= $row['nama_kategori'] ?></p>
                </div>
                <div class="d-flex">
                    <form class="col-6" action="/server/feature/tambah_barang.php" method="post">
                        <input name="idProduk" type="text" value="<?= $row['id_produk'] ?>" hidden>
                        <input name="srcURL" type="text" value="/beranda.php" hidden>
                        <button
                            class="primary d-flex align-items-center thrift-shop-transparent justify-content-center w-100"><img
                                src="/static/image/icons8-shopping-cart-24-white.png" alt="shop cart"><span
                                class="d-none d-lg-inline">&nbsp;Beli</span></button>
                    </form>
                    <button
                        class="col-6 detail d-flex align-items-center thrift-shop-transparent thrift-shop-bg-white justify-content-center detailProduk"
                        data-id-produk="<?= $row['id_produk'] ?>"><img src="/static/image/icons8-detail-24.png"
                            alt="detail"><span class="d-none d-lg-inline">&nbsp;Detail</span></button>
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