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

if (
    isset($_SESSION['email']) && $_SESSION['email'] != '' && validateSessionLogin($mysqli, $_SESSION['email'])
) {
    $userLogin = $_SESSION['email'];

    $email = getEmailFromHash($mysqli, $userLogin);

    $query = "SELECT waktu_transaksi, kode_transaksi, status FROM riwayat_transaksi WHERE email=? GROUP BY kode_transaksi ORDER BY waktu_transaksi DESC";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $userLogin = "";
    header("Location: /akun.php");
}
?>

<?php ob_start(); ?>
<script src="/static/js/page/riwayat_transaksi.js"></script>
<?php $head = ob_get_clean(); ?>

<?php ob_start(); ?>
<h2 class="thrift-shop">
    Riwayat Transaksi
</h2>

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
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Waktu Transaksi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr class="itemBelanja text-break break-word" data-produk-id="<?= $row['kode_transaksi'] ?>">
                        <td class="align-middle"><?= $row['kode_transaksi'] ?></td>
                        <td class="align-middle"><?= formatTimeToShow($row['waktu_transaksi']) ?></td>
                        <td class="align-middle">
                            <div class="p-1 d-flex">
                                <div
                                    class="w-100 h-100 <?php echo ($row['status'] == 'success') ? 'thrift-shop-green-status' : (($row['status'] == 'waiting' || $row['status'] == 'validating') ? 'thrift-shop-yellow-status' : 'thrift-shop-red-status'); ?>">
                                    &nbsp;
                                </div>
                            </div>

                        </td>
                        <td class="d-flex flex-wrap">
                            <div class="p-1 col-12">
                                <?php
                                if ($row['status'] == 'waiting') {
                                    ?>
                                    <form class="kirimBukti" action="/server/feature/upload_bukti.php" method="post"
                                        enctype="multipart/form-data" data-kode-transaksi="<?= $row['kode_transaksi'] ?>">
                                        <input type="text" name="kodeTransaksi" value="<?= $row['kode_transaksi'] ?>" hidden>
                                        <input type="file" name="buktiTransaksi" class="buktiTransaksi"
                                            data-kode-transaksi="<?= $row['kode_transaksi'] ?>" hidden>
                                        <button type="button"
                                            class=" w-100 thrift-shop-transparent thrift-shop-bg-red thrift-shop-font-white p-2 uploadBukti"
                                            data-kode-transaksi="<?= $row['kode_transaksi'] ?>"><img
                                                src="/static/image/icons8-upload-24.png" alt="detail">&nbsp;<span
                                                class="d-none d-md-inline">Upload Bukti</span>
                                        </button>
                                    </form>
                                    <?php
                                } else {
                                    ?>
                                    <button type="button"
                                        class="d-flex align-items-center justify-content-center w-100 thrift-shop-transparent thrift-shop-bg-gray thrift-shop-font-white p-2 uploadBukti"
                                        data-kode-transaksi="<?= $row['kode_transaksi'] ?>"><img
                                            src="/static/image/icons8-upload-24.png" alt="detail"><span
                                            class="d-none d-md-inline">&nbsp;Upload Bukti</span>
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>

                            <div class="p-1 col-12">
                                <a class="w-100 thrift-shop-font-red thrift-shop thrift-shop-bg-white p-2 d-inline-block d-flex align-items-center justify-content-center"
                                    href="/detail_transaksi.php?kodeTransaksi=<?= $row['kode_transaksi'] ?>"><img
                                        src="/static/image/icons8-detail-24.png" alt="detail"><span
                                        class="d-none d-md-inline">&nbsp;Detail</span>
                                </a>
                            </div>

                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    } else {
        ?>
        <h4 class="thrift-shop-small-font">Yuk mulai berbelanja sekarang...</h4>
        <?php
    }
    ?>

</div>
<?php $content = ob_get_clean(); ?>
<?php include ('static/layout/layout.php'); ?>
<?php $mysqli->close(); ?>