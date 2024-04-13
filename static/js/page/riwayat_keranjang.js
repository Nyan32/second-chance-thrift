$(function () {
    $('.uploadBukti').on('click', function () {
        var kodeTransaksi = $(this).data('kode-transaksi')

        $(`.buktiTransaksi[data-kode-transaksi="${kodeTransaksi}"]`).click()
    })

    $('.buktiTransaksi').change(function () {
        if ($(this).val()) {
            var kodeTransaksi = $(this).data('kode-transaksi')
            $(`.kirimBukti[data-kode-transaksi="${kodeTransaksi}"]`).submit()
        }
    });
})