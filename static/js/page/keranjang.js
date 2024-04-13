$(function () {
    $('.tombolHapusItem').on('click', function () {
        var idProduk = $(this).data('produk-id')
        $(`tr[data-produk-id="${idProduk}"]`).remove()
    })
})