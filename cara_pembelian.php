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
    Cara Pembelian
</h2>

<hr>

<ol>
    <li>Klik pada tombol <b>beli</b> pada produk yang ingin Anda pesan.</li>
    <li>Produk yang Anda pesan akan masuk ke dalam <b>keranjang belanja</b>.
        Anda dapat melakukan perubahan jumlah produk yang diinginkan dengan mengubah angka di kolom <b>jumlah</b>.</li>
    <li>Untuk menghapus produk, dapat menekan tombol <b>keranjang sampah</b>.</li>
    <li>Jika melakukan perubahan pada item di keranjang, jangan lupa menekan <b>simpan keranjang</b>.</li>
    <li>Barang-barang yang kamu simpan di keranjang akan bertahan selama <b>1 jam</b>.</li>
    <li>Kamu hanya bisa meletakkan <b>20 produk</b> dalam keranjang. Silahkan checkout dan selesaikan pembayaran untuk
        mengosongkan keranjangmu.</li>
    <li>Jika sudah selesai, klik tombol <b>selesai belanja</b>. Selanjutnya akan diarahkan ke halaman detail belanja,
        pastikan seluruh barang pesanan dan alamat sudah benar.</li>
    <li>Setelah semua data sudah benar, tekan tombol <b>proses</b>.</li>
    <li>Anda akan dibawa ke halaman <b>riwayat transaksi</b>, disini anda dapat melihat transaksi yang belum dilakukan
        konfirmasi pembayaran dan transaksi-transaksi sebelumnya yang sudah dilakukan.</li>
    </li>
    <li>Lakukan konfirmasi pembayaran dalam <b>15 menit</b>, atau transaksi akan dibatalkan.</li>
    <li>Pastikan bukti yang dikirim <b>dapat terbaca dengan baik</b>. Jika tidak, admin akan membatalkan pesanan.</li>
    <li>Hubungi admin terkait pengiriman ulang bukti.</li>
    <li>Setelah pembayaran dikonfirmasi, kami akan mengirimkan barang pesanan ke alamat dituju.</li>
</ol>

<?php $content = ob_get_clean(); ?>

<?php include ('static/layout/layout.php'); ?>

<?php $mysqli->close(); ?>