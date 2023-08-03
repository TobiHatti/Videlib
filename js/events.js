

$(document).ready(() => {
    $.get({
        url: `/page/addcharacter.php?${Math.random()}`,
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


    $("#addCharacterForm").on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#addCharacterForm")[0]);
        formData.append('image', $("#imgBtn")[0].files[0])
        //let formDataObj = {};
        //formData.forEach((value, key) => (formDataObj[key] = value));
        SmoothPost(formData, "character", "characterlist");
    });


    $("#imgBtn").on("change", (event) => {
        $("#imgPreview").attr("src", URL.createObjectURL($("#imgBtn")[0].files[0]));
    });
}

