$(document).ready(() => {
    $.get({
        url: `/page/characterlist.php?${Math.random()}`,
        success: (response) => { $("main").html(response); BindMenu(); }
    });

    $.get({
        url: "/page/particles.html",
        success: (response) => $("#particleContainer").html(response)
    });
});

$("header").on("click", function(){
    $.get({
        url: `/page/menu.html?${Math.random()}`,
        success: (response) =>{ $("main").html(response); BindMenu(); }
    });
})

function BindMenu(){
    $(".menuTileSubContainer").each(function(){
        let p = $(this).attr("d-page");
        $(this).on("click", function(){
            $(this).attr("style", "--animOffset: 0.0s;");
            $(this).closest(".menuTileWrapper").find(".menuTileSubContainer").each(function(index) {
                $(this).addClass("flyOut");
            });
            setTimeout(function(){
                $.get({
                    url: `/page/${p}.php?${Math.random()}`,
                    success: function(response){
                        $("main").html(response);
                    }
                });
            }, 1000);
        });
    });
}