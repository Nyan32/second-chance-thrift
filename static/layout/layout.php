<?php
$query = "SELECT COUNT(p.id_kategori) AS count_item, k.nama AS nama, k.id_kategori AS id_kategori FROM produk p RIGHT JOIN kategori k ON k.id_kategori=p.id_kategori GROUP BY k.id_kategori";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result_category = $stmt->get_result();
$stmt->close();

$query = "SELECT COUNT(*) AS jumlah FROM produk";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$resultAll = $stmt->get_result();

$stmt->close();
$row = $resultAll->fetch_assoc();
$total = $row['jumlah'];

if (isset($_SESSION['email']) && $_SESSION['email'] != '') {
    $userLogin = $_SESSION['email'];

    $query = "SELECT SUM(jumlah_beli) AS total_keranjang FROM keranjang WHERE email=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $userLogin);
    $stmt->execute();
    $resultAll = $stmt->get_result();

    $stmt->close();
    $row = $resultAll->fetch_assoc();
    $total_keranjang = $row['total_keranjang'];
} else {
    $userLogin = "";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include ('static/layout/head.php') ?>

    <?php echo isset($head) ? $head : ''; ?>
</head>

<body class="d-flex flex-column" style="min-height:100vh">
    <?php include ('static/layout/header.php') ?>
    <div class="outer-9-16-md outer-3-4">
        <div class="inner">
            <img class="w-100 h-100" src="/static/image/toko_baju.jpg" alt="banner" style="object-fit:cover">
        </div>
    </div>

    <div class="position-relative row m-0">
        <button id="showSideBar" class="d-md-none d-block thrift-shop-transparent bg-dark"><img
                src="/static/image/icons8-menu-24.png" alt="menu"></button>
        <div class="col-12 col-md-8 d-inline-block p-2">
            <?php echo isset($content) ? $content : ''; ?>
        </div>
        <div class="col-12 col-md-4 d-inline-block thrift-shop-side-bar p-0 h-100" id="sideBar">
            <div class="position-relative">
                <button id="hidSideBar"
                    class="d-md-none d-block thrift-shop-transparent position-absolute top-0 end-0 bg-dark"><img
                        src="/static/image/icons8-close-24.png" alt="close"></button>
                <?php
                if ($userLogin != '') {
                    ?>
                    <div class="p-2">
                        <div class="bg-light border">
                            <div class="p-2 title-card">Keranjang</div>
                            <!-- number of product will be using data in database -->
                            <div class="p-2"><span
                                    class="thrift-shop-font-orange"><?= ($total_keranjang != '') ? $total_keranjang : 0 ?></span>&nbsp;
                                produk
                            </div>
                            <div class="p-2 row m-0">
                                <a class="col-12 col-lg-6 thrift-shop cart-link p-0" href="/keranjang.php"><img
                                        src="/static/image/icons8-checkmark-24.png" alt="checkmark">lihat
                                    keranjang</a>
                                <?php
                                if ($total_keranjang > 0) {
                                    ?>
                                    <a class="col-12 col-lg-6 thrift-shop cart-link p-0" href="/detail_belanja.php"><img
                                            src="/static/image/icons8-checkmark-24.png" alt="checkmark">selesai
                                        belanja</a>
                                    <?php
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="p-2">
                    <div class="bg-light border">
                        <div class="p-2 title-card">Kategori Produk</div>
                        <div class="p-2 row m-0">
                            <a class="col-12 thrift-shop category-link p-1 kategoriLink" href="/produk.php"><img
                                    src="/static/image/icons8-right-24.png" alt="checkmark">Semua Produk
                                (<?= convertTo999Plus($total) ?>)</a>
                            <!-- it will be using data in database -->
                            <?php
                            while ($row = $result_category->fetch_assoc()) {
                                ?>
                                <a class="col-12 thrift-shop category-link p-1 kategoriLink"
                                    href="/produk.php?kategori=<?= $row['id_kategori'] ?>"><img
                                        src="/static/image/icons8-right-24.png" alt="checkmark"><?= $row['nama'] ?>
                                    (<?= convertTo999Plus($row['count_item']) ?>)</a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="p-2">
                    <div class="bg-light border">
                        <div class="p-2 title-card">Bank Pembayaran</div>
                        <div class="p-2 row m-0">
                            BCA 9827453274903 a.n. Elga
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


</body>

</html>