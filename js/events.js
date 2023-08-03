$(document).ready(() => {
    $.get({
        url: `/page/addCharacter.php?${Math.random()}`,
        success: (response) => { $("main").html(response); BindMenu(); Bind(); }
    });

    $.get({
        url: "/page/particles.html",
        success: (response) => $("#particleContainer").html(response)
    });
});

$("header").on("click", function(){
    if($(".menuTileWrapper").length == 0)
    {
        $(".contentWrapper").addClass("fadeOut");
        setTimeout(function(){
            $.get({
                url: `/page/menu.html?${Math.random()}`,
                success: (response) =>{ $("main").html(response); BindMenu(); }
            });
        },300);
    }
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
                LoadPage(p);
            }, 1000);
        });
    });
}

function Bind(){
    $("#addCharacter").on("click", function(){
        $(".contentWrapper").addClass("fadeOut");
        setTimeout(function(){
            LoadPage("addCharacter");
        },300);
    });
}


function LoadPage(page){
    $.get({
        url: `/page/${page}.php?${Math.random()}`,
        success: function(response){
            $("main").html(response);
            Bind();
        }
    });
}