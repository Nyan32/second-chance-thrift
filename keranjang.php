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

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && validateSessionLogin($mysqli, $_SESSION['email'])) {
    $userLogin = $_SESSION['email'];

    $email = getEmailFromHash($mysqli, $userLogin);

    $query = "SELECT * FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.email=? ORDER BY jumlah_beli";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $query = "SELECT email, UNIX_TIMESTAMP(waktu_keranjang) AS waktu_keranjang FROM keranjang WHERE email=? GROUP BY email";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $resultWaktu = $stmt->get_result();
    $stmt->close();

    $rowWaktu = $resultWaktu->fetch_assoc();
    $rowCount = $resultWaktu->num_rows;
} else {
    $userLogin = "";
    header("Location: /akun.php");
}
?>

<?php ob_start(); ?>
<script>var mark_waktu_keranjang = <?= ($rowCount > 0) ? $rowWaktu['waktu_keranjang'] : 0 ?> </script>
<script src="/static/js/page/keranjang.js"></script>
<?php $head = ob_get_clean(); ?>

<?php ob_start(); ?>
<h2 class="thrift-shop">
    Keranjang
</h2>
<h4 class="thrift-shop-small-font">Durasi keranjang:&nbsp;<span id="waktuKeranjang">--:--:--</span></h4>
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


<div class="p-2">
    <?php
    if ($result->num_rows > 0) {
        ?>
        <form action="/server/feature/ubah_keranjang.php" method="post">
            <table class="table table-sm w-100">
                <thead>
                    <tr>
                        <th class="d-none d-sm-table-cell">Gambar</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th class="d-none d-md-table-cell">Tersisa</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr class="itemBelanja" data-produk-id="<?= $row['id_produk'] ?>">
                            <td class="d-none d-sm-table-cell">
                                <div class="outer-1-1">
                                    <div class="inner">
                                        <img class="w-100 h-100" src="/server/produk/<?= $row['gambar'] ?>" alt=""
                                            style="object-fit:cover">
                                    </div>
                                </div>
                                <input type="text" value="<?= $row['id_produk'] ?>" hidden name="idProduk[]">
                            </td>
                            <td class="align-middle"><?= $row['nama'] ?></td>
                            <td class="align-middle"><?= intToRupiahStr($row['harga']) ?></td>
                            <td class="align-middle d-none d-md-table-cell"><?= convertTo999Plus($row['stok']) ?></td>
                            <td class="align-middle"><input class="thrift-shop form-control" type="text" name="jumlahBeli[]"
                                    value="<?= $row['jumlah_beli'] ?>">
                            </td>
                            <td class="align-middle">
                                <button type="button" class="thrift-shop-transparent tombolHapusItem"
                                    data-produk-id="<?= $row['id_produk'] ?>">
                                    <img src="/static/image/icons8-trash-24.png" alt="hapus">
                                </button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <div class="d-flex flex-wrap justify-content-between">
                <div class="p-2">
                    <button class="d-flex align-items-center thrift-shop-transparent thrift-shop primary p-2"><img
                            src="/static/image/icons8-save-24.png" alt="simpan">&nbsp;Simpan
                        Keranjang</button>
                </div>
                <div class="p-2">
                    <a type="button" class="d-flex align-items-center thrift-shop-transparent thrift-shop primary p-2"
                        href="/detail_belanja.php"><img src="/static/image/icons8-checkmark-24-white.png"
                            alt="selesai">&nbsp;Selesai
                        Belanja</a>
                </div>

            </div>
        </form>
        <?php
    } else {
        ?>
        <h4 class="thrift-shop-small-font">Keranjang kamu masih kosong nih, belanja yuk...</h4>
        <?php
    }
    ?>

</div>
<?php $content = ob_get_clean(); ?>
<?php include ('static/layout/layout.php'); ?>
<?php $mysqli->close(); ?>