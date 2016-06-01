$(document).ready(function(){
    $(".menu_link").click(function(){
        $(".sisu").fadeIn();

    });

    $(".session_link").click(function(){
        confirm("Kinnita, et soovid välja logida");
    });
});