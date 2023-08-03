$(document).ready(() => {
    $.get({
        url: "/page/menu.html",
        success: (response) => $("main").html(response)
    });

    $.get({
        url: "/page/particles.html",
        success: (response) => $("#particleContainer").html(response)
    });
});

$("header").on("click", function(){
    $.get({
        url: "/page/menu.html",
        success: (response) => $("main").html(response)
    });
})