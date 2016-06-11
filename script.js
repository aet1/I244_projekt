$(document).ready(function(){
    $(".menu_link").click(function(){
        $(".sisu").fadeIn();

    });

    $(".session_link").click(function () {
        var c = confirm("Kinnita, et soovid välja logida");
        if (c) {
            return true
        } else {
            return false;
        }
    });

});