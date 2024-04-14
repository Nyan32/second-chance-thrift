<?php
include ('server/connect.php');
include_once ('server/helper.php');

$expirationDate = date('D, d M Y H:i:s', strtotime("+1 day")) . ' GMT';

header("Cache-Control: must-revalidate");
header("Expires: $expirationDate");

session_start();

?>


<?php ob_start(); ?>
<h2 class="thrift-shop">
    Profil Kami
</h2>

<hr>

<p>
    SECOND CHANCE THRIFT menjual berbagai jenis pakaian bekas,
    mulai dari fashion pria, fashion wanita, fashion anak dan fashion muslim.
    Toko kami berlokasi disini yaa...
</p>

<div class="w-100">
    <img src="/static/image/maps.png" class="w-100" alt="lokasi toko">
</div>
<?php $content = ob_get_clean(); ?>
<?php include ('static/layout/layout.php'); ?>
<?php $mysqli->close(); ?>