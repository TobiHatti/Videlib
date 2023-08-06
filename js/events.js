

$(document).ready(() => {
    $.get({
        url: `/page/characterinfo.php?${Math.random()}&c=B219C811-F11B-479A-8F05-46B09BB87792`,
        success: (response) => { $("main").html(response); BindMenu(); Bind(); }
    });

    $.get({
        url: "/page/particles.html",
        success: (response) => $("#particleContainer").html(response)
    });
});

$("header").on("click", function(){
    if($(".menuTileWrapper").length == 0) SmoothLoadPage("menu");
})

function BindMenu(){
    $(".menuTileSubContainer").each(function(){
        let p = $(this).attr("d-page");
        $(this).on("click", function(){
            $(this).attr("style", "--animOffset: 0.0s;");
            $(this).closest(".menuTileWrapper").find(".menuTileSubContainer").each(function(index) {
                $(this).addClass("flyOut");
            });
            setTimeout(() => LoadPage(p), 1000);
        });
    });
}

function Bind(){
    $("#addCharacter").on("click", () => SmoothLoadPage("addCharacter"));

    $(".realCharacter").each(function(){
        $(this).on("click", () => SmoothLoadPage("characterinfo", "c", $(this).attr("d-chid")));
    });

    $("#addCharacterForm").on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#addCharacterForm")[0]);
        formData.append('image', $("#imgBtn")[0].files[0])
        SmoothPost(formData, "character", "characterlist");
    });

    $("#imgBtn").on("change", (event) => {
        $("#imgPreview").attr("src", URL.createObjectURL($("#imgBtn")[0].files[0]));
    });

    $("#imgBtn").on("change", (event) => $("#addImageForm").submit());

    $("#addImageForm").on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#addImageForm")[0]);
        formData.append('image', $("#imgBtn")[0].files[0])
        SmoothPost(formData, "characterimg", "characterinfo", "c", $("#cid").val());
    });

    $(".carouselImg").each(function(){
        let elem = $(this);
        $(this).on("click", () => $("#mainImg").attr("src", elem.find("img").attr("src")));
    });

    $("#addNoteForm").on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#addNoteForm")[0]);
        SmoothPost(formData, "characternote", "characterinfo", "c", $("#cid").val());
    });


    $(".treeNode").each(function(){
        $(this).on("click", () => SmoothLoadPage("characterinfo", "c", $(this).attr("d-chid")));
    });

    $("svg.branch").each(function(){
        $(this).attr("viewbox", `-${$(this).width()/2} -${$(this).height()/2} ${$(this).width()} ${$(this).height()}`);
    });
}

