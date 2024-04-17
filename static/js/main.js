$(function () {
    var showSideBar = false

    const urlParamsCari = new URLSearchParams(window.location.search);
    const cari = urlParamsCari.get('cari');

    if (cari != null) {
        $('.kategoriLink').each(function () {
            var url = new URL(window.location.origin + $(this).attr('href'))
            url.searchParams.append('cari', cari)
            $(this).attr('href', url)
        })

        $('#cariItem').val(cari)
    }

    $('#searchBar').submit(function (event) {
        event.preventDefault();

        var cari = $('#cariItem').val()
        const urlParamsKategori = new URLSearchParams(window.location.search);
        const kategori = urlParamsKategori.get('kategori');

        if (kategori != null) {
            $(this).append(`<input type="text" name="kategori" value="${kategori}" hidden>`)
        }


        $(this).off('submit').submit();
    })

    if ($(window).width() < 768) {
        $('#sideBar').removeClass("thrift-shop-side-bar-show")
        $('#sideBar').addClass("thrift-shop-side-bar-hid")
    }

    const mappingUrlToMenuName = {
        "beranda.php": "Beranda",
        "profil.php": "Profil",
        "produk.php": "Produk",
        "keranjang.php": "Keranjang",
        "detail_belanja.php": "Keranjang",
        "cara_pembelian.php": "Cara Pembelian",
        "riwayat_transaksi.php": "Riwayat Transaksi",
        "detail_transaksi.php": "Riwayat Transaksi",
        "akun.php": "Akun",
    };

    var activePages = window.location.pathname.split('/');
    var activePage = activePages[activePages.length - 1]

    if (activePage == '') {
        $('li[data-url="beranda.php"] a').addClass('thrift-shop-active-menu')
        $('#pagePosition').text(mappingUrlToMenuName['beranda.php'])
    } else if (activePage == 'detail_belanja.php') {
        $(`li[data-url="keranjang.php"] a`).addClass('thrift-shop-active-menu')
        $('#pagePosition').text(mappingUrlToMenuName[activePage])
    } else if (activePage == 'detail_transaksi.php') {
        $(`li[data-url="riwayat_transaksi.php"] a`).addClass('thrift-shop-active-menu')
        $('#pagePosition').text(mappingUrlToMenuName[activePage])
    } else {
        $(`li[data-url="${activePage}"] a`).addClass('thrift-shop-active-menu')
        $('#pagePosition').text(mappingUrlToMenuName[activePage])
    }

    $(window).resize(function () {
        showSideBar = false

        if ($(window).width() >= 768) {
            $('#sideBar').removeClass("thrift-shop-side-bar-show")
            $('#sideBar').removeClass("thrift-shop-side-bar-hid")

        } else {
            $('#sideBar').removeClass("thrift-shop-side-bar-show")
            $('#sideBar').addClass("thrift-shop-side-bar-hid")
        }
    })

    $('#showSideBar').on('click', function () {
        if (showSideBar == false) {
            showSideBar = true
            $('#sideBar').removeClass("thrift-shop-side-bar-hid")
            $('#sideBar').addClass("thrift-shop-side-bar-show")
        }
    })

    $('#hidSideBar').on('click', function () {
        if (showSideBar == true) {
            showSideBar = false
            $('#sideBar').removeClass("thrift-shop-side-bar-show")
            $('#sideBar').addClass("thrift-shop-side-bar-hid")
        }
    })

    const myModal = new bootstrap.Modal(document.getElementById('showDetailProduk'))

    $('.detailProduk').on('click', function () {
        var idProduk = $(this).data('id-produk')
        var nama = $(`.detailInfo[data-id-produk="${idProduk}"] .nama`).text()
        var deskripsi = $(`.detailInfo[data-id-produk="${idProduk}"] .deskripsi`).html()
        var harga = $(`.detailInfo[data-id-produk="${idProduk}"] .harga`).text()
        var stok = $(`.detailInfo[data-id-produk="${idProduk}"] .stok`).text()
        var berat = $(`.detailInfo[data-id-produk="${idProduk}"] .berat`).text()
        var gambar = $(`.detailInfo[data-id-produk="${idProduk}"] .gambar`).text()
        var jumlahDibeli = $(`.detailInfo[data-id-produk="${idProduk}"] .jumlahDibeli`).text()
        var diskon = $(`.detailInfo[data-id-produk="${idProduk}"] .diskon`).text()
        var kategori = $(`.detailInfo[data-id-produk="${idProduk}"] .kategori`).text()

        $('#gambarProdukModal').attr('src', `/server/produk/${gambar}`)
        $('#namaProdukModal').text(nama)
        $('#namaKategoriProdukModal').text(kategori)
        $('#deskripsiProdukModal').html(deskripsi)
        $('#hargaProdukModal span').text(harga)
        $('#beratProdukModal span').text(berat)
        $('#diskonProdukModal span').text(diskon)
        $('#stokProdukModal span').text(stok)
        $('#jumlahDibeliProdukModal span').text(jumlahDibeli)

        myModal.toggle()
    })
})