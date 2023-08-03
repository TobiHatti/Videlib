function SelectMenuOption(elem, page){

    $(elem).attr("style", "--animOffset: 0.0s;");
    $(elem).closest(".menuTileWrapper").find(".menuTileSubContainer").each(function(index) {
        $(this).addClass("flyOut");
    });

    setTimeout(function(){
        $.get({
            url: `/page/${page}.html`,
            success: function(response){
                $("main").html(response);
            }
        });
    }, 1000);
}