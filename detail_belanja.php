<?php
include ('server/connect.php');
include_once ('server/helper.php');

$expirationDate = date('D, d M Y H:i:s', strtotime("+1 day")) . ' GMT';

header("Cache-Control: must-revalidate");
header("Expires: $expirationDate");

session_start();

if (
    isset($_SESSION['email']) && $_SESSION['email'] != '' && validateSessionLogin($mysqli, $_SESSION['email'])
) {
    $userLogin = $_SESSION['email'];

    $query = "SELECT nama, alamat, nomor_telepon FROM akun WHERE email_hash=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $userLogin);
    $stmt->execute();
    $resultAkun = $stmt->get_result();
    $stmt->close();

    $dataAkun = $resultAkun->fetch_assoc();

    $email = getEmailFromHash($mysqli, $userLogin);


    $query = "SELECT *, (harga-diskon) AS harga_sesudah_diskon FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.email=? ORDER BY k.jumlah_beli";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $query = "SELECT SUM((p.harga-p.diskon) * k.jumlah_beli) AS totalBelanjaHarga FROM keranjang k JOIN produk p ON k.id_produk = p.id_produk WHERE k.email=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $resultTotalHarga = $stmt->get_result();
    $stmt->close();

    $row = $resultTotalHarga->fetch_assoc();
    $totalHarga = $row['totalBelanjaHarga'];

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
<script> var mark_waktu_keranjang = <?= ($rowCount > 0) ? $rowWaktu['waktu_keranjang'] : 0 ?> </script>
<script src="/static/js/page/detail_belanja.js"></script>
<?php $head = ob_get_clean(); ?>

<?php ob_start(); ?>
<h2 class="thrift-shop">
    Detail Belanja
</h2>
<h4 class="thrift-shop-small-font">Durasi keranjang:&nbsp;<span id="waktuKeranjang">--:--:--</span></h4>

<hr>
<h3 class="thrift-shop-small-font mb-3"><b>Detail pembeli</b></h3>
<ul>
    <li>Nama:&nbsp;<?= $dataAkun['nama'] ?></li>
    <li>Alamat:&nbsp;<?= $dataAkun['alamat'] ?></li>
    <li>Nomor Telepon:&nbsp;<?= $dataAkun['nomor_telepon'] ?></li>
</ul>

<h3 class="thrift-shop-small-font mb-3"><b>Total
        harga: &nbsp<?= intToRupiahStr((($totalHarga != '') ? $totalHarga : 0)) ?></b></h3>


<h4 class="thrift-shop-small-font m-0"><b>Detail:</b></h4>
<div class="p-2">
    <?php
    if ($result->num_rows > 0) {
        ?>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr class="itemBelanja">
                        <td>
                            <div class="outer-1-1">
                                <div class="inner">
                                    <img class="w-100 h-100" src="/server/produk/<?= $row['gambar'] ?>" alt=""
                                        style="object-fit:cover">
                                </div>
                            </div>
                            <input type="text" value="<?= $row['id_produk'] ?>" hidden name="idProduk[]">
                        </td>
                        <td class="align-middle"><?= $row['nama'] ?></td>
                        <td class="align-middle"><?= intToRupiahStr($row['harga_sesudah_diskon']) ?></td>
                        <td class="align-middle"><?= $row['jumlah_beli'] ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <div class="d-flex flex-wrap">
            <div class="p-2">
                <form action="/server/feature/proses_keranjang.php" method="post">
                    <button class="d-flex align-items-center thrift-shop-transparent thrift-shop primary p-2">
                        <img src="/static/image/icons8-checkmark-24-white.png" alt="proses">&nbsp;Proses</button>
                </form>
            </div>

        </div>
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