$(function () {
    const waktuKeranjangCont = $('#waktuKeranjang')

    setInterval(function () {
    	
        var unixTimestamp = Math.floor(Date.now() / 1000)
        var diff = 3600 - (unixTimestamp - mark_waktu_keranjang)
		
        if (diff > 3600 || diff < 0) {
            diff = 0
        }

        if (diff == 0) {
            waktuKeranjangCont.text("--:--:--")
        } else {
            waktuKeranjangCont.text(formatTime(diff))
        }

    }, 1000)

    function formatTime(seconds) {
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        var remainingSeconds = seconds % 60;

        hours = String(hours).padStart(2, '0');
        minutes = String(minutes).padStart(2, '0');
        remainingSeconds = String(remainingSeconds).padStart(2, '0');

        return hours + ':' + minutes + ':' + remainingSeconds;
    }
})