<?php
include ('server/connect.php');
include_once ('server/helper.php');

$expirationDate = date('D, d M Y H:i:s', strtotime("+1 day")) . ' GMT';

header("Cache-Control: must-revalidate");
header("Expires: $expirationDate");

session_start();

if (isset($_GET['kodeTransaksi']) && $_GET['kodeTransaksi'] != '') {
    $kodeTransaksi = $_GET['kodeTransaksi'];
} else {
    $kodeTransaksi = '';
}

if (isset($_SESSION['email']) && $_SESSION['email'] != '' && validateSessionLogin($mysqli, $_SESSION['email'])) {
    $userLogin = $_SESSION['email'];

    $query = "SELECT nama, alamat, nomor_telepon FROM akun WHERE email_hash=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $userLogin);
    $stmt->execute();
    $resultAkun = $stmt->get_result();
    $stmt->close();

    $dataAkun = $resultAkun->fetch_assoc();

    $email = getEmailFromHash($mysqli, $userLogin);

    $query = "SELECT *, (harga-diskon) AS harga_sesudah_diskon FROM riwayat_transaksi r JOIN produk p ON r.id_produk = p.id_produk WHERE r.kode_transaksi=? ORDER BY r.jumlah_beli";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $kodeTransaksi);
    $stmt->execute();
    $resultRiwayat = $stmt->get_result();
    $stmt->close();

    $query = "SELECT status, kode_transaksi FROM riwayat_transaksi WHERE kode_transaksi=? GROUP BY kode_transaksi";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $kodeTransaksi);
    $stmt->execute();
    $resultStatus = $stmt->get_result();
    $stmt->close();

    $row = $resultStatus->fetch_assoc();
    $status = $row['status'];
    $mappingStatus = [
        "fail" => "Transaksi gagal",
        "success" => "Transaksi berhasil",
        "waiting" => "Menunggu bukti transaksi",
        "validating" => "Pengecekan bukti oleh toko",
    ];

    $query = "SELECT SUM((p.harga-p.diskon) * r.jumlah_beli) AS totalBelanjaHarga FROM riwayat_transaksi r JOIN produk p ON r.id_produk = p.id_produk WHERE r.kode_transaksi=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $kodeTransaksi);
    $stmt->execute();
    $resultTotalHarga = $stmt->get_result();
    $stmt->close();

    $row = $resultTotalHarga->fetch_assoc();
    $totalHarga = $row['totalBelanjaHarga'];

    $query = "SELECT kode_transaksi, UNIX_TIMESTAMP(waktu_transaksi) AS waktu_transaksi FROM riwayat_transaksi WHERE kode_transaksi=? GROUP BY kode_transaksi";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $kodeTransaksi);
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
<script> var mark_waktu_transaksi = <?= ($rowCount > 0 && $status == 'waiting') ? $rowWaktu['waktu_transaksi'] : 0 ?> </script>
<script src="/static/js/page/detail_transaksi.js"></script>
<?php $head = ob_get_clean(); ?>

<?php ob_start(); ?>
<h2 class="thrift-shop">
    Detail Transaksi
</h2>
<h4 class="thrift-shop-small-font">Durasi transaksi:&nbsp;<span id="waktuTransaksi">--:--:--</span></h4>
<hr>
<h3 class="thrift-shop-small-font mb-3"><b>Detail pembeli</b></h3>
<ul>
    <li>Nama:&nbsp;<?= $dataAkun['nama'] ?></li>
    <li>Alamat:&nbsp;<?= $dataAkun['alamat'] ?></li>
    <li>Nomor Telepon:&nbsp;<?= $dataAkun['nomor_telepon'] ?></li>
</ul>

<h3 class="thrift-shop-small-font mb-3"><b>Total
        harga: &nbsp<?= intToRupiahStr((($totalHarga != '') ? $totalHarga : 0)) ?></b></h3>

<h3 class="thrift-shop-small-font mb-3"><b>Status transaksi: &nbsp<?= $mappingStatus[$status] ?></b></h3>


<h4 class="thrift-shop-small-font m-0"><b>Detail:</b></h4>
<div class="p-2">
    <?php
    if ($resultRiwayat->num_rows > 0) {
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
                while ($row = $resultRiwayat->fetch_assoc()) {
                    ?>
                    <tr class="itemBelanja" ?>
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
        <?php
    } else {
        ?>
        <h4 class="thrift-shop-small-font">Riwayat transaksi tidak ditemukan</h4>
        <?php
    }
    ?>

</div>
<?php $content = ob_get_clean(); ?>
<?php include ('static/layout/layout.php'); ?>
<?php $mysqli->close(); ?>