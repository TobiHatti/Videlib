

$(document).ready(() => {
    $.get({
        url: `/page/addCharacter.php?${Math.random()}&c=B219C811-F11B-479A-8F05-46B09BB87792`,
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
    // $("details").each(function(){
    //     new Accordion($(this)[0]);
    // });

    $("button").each(function(){
        $(this).on("click", function(){
            $(this).attr("disabled", "disabled");
        });
    });

    $("#addCharacter").on("click", () => SmoothLoadPage("addCharacter"));

    $(".realCharacter").each(function(){
        $(this).on("click", () => SmoothLoadPage("characterinfo", "c", $(this).attr("d-chid")));
    });

    $("#addCharacterForm").on("submit", (event) => {
        event.preventDefault();
        $(".addCharacterSubmitBtn").attr("disabled", "disabled");

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
        $("#noteSubmitBtn").attr("disabled", "disabled");
        
        let formData = new FormData($("#addNoteForm")[0]);
        SmoothPost(formData, "characternote", "characterinfo", "c", $("#cid").val());
    });

    $(".nodeImg").each(function(){
        $(this).on("click", () => SmoothLoadPage("characterinfo", "c", $(this).closest(".treeNode").attr("d-chid")));
    });

    $(".nodeName").each(function(){
        $(this).on("click", () => LoadModal("treeMenu", "c", $(this).closest(".treeNode").attr("d-chid")))
    });

    $("svg.branch").each(function(){
        $(this).attr("viewbox", `-${$(this).width()/2} -${$(this).height()/2} ${$(this).width()} ${$(this).height()}`);
    });

    $(".tab").each(function(){
        $(this).on("click", () => SmoothLoadPage("characterinfo", "c", $(this).attr("d-chid")));
    });

    $(".modalBlur").on("click", function() { $(".modalWrapper").removeClass("modOpen").addClass("modClose"); setTimeout(() => $(".modalWrapper").css("display", "none"), 200); })

    $("#modBtnAddPartner").on("click", () => LoadModal("treeMenuPartner", "c", $("#modCID").val()));
    $("#modBtnAddChild").on("click", () => LoadModal("treeMenuChildren", "c", $("#modCID").val()))
    $("#modBtnAddChildWithExisting").on("click", () => LoadModal("treeMenuChildWithExisting", "c", $("#modCID").val()))
    $("#modBtnAddExistingPartner").on("click", () => LoadModal("treeMenuPartnerWithExisting", "c", $("#modCID").val()))
    
    // p1 & p2 --> Add child of p1 & p2
    $("#modBtnAddChildWithSuggestion").on("click", function() { SmoothLoadPage("addcharacter", "p1", `${$("#modCID").val()}&p2=${$(this).attr("d-sid")}`)});
    $("#modBtnAddChildWithNoPartner").on("click", function() { SmoothLoadPage("addcharacter", "p1", `${$("#modCID").val()}&p2=00000000-0000-0000-0000-000000000000`)});
    $("#newChildWithExistingPartnerForm").on("submit", (event) => {
        event.preventDefault();
        SmoothLoadPage("addcharacter", "p1", `${$("#modCID").val()}&p2=${$("#existingCharacter").val()}`);
    });

    $("#modBtnAddSibling").on("click", () => LoadModal("treeMenuSiblings", "c", $("#modCID").val()));
    
    // m --> Partner of m
    $("#modBtnAddNewPartner").on("click", () => SmoothLoadPage("addcharacter", "m", $("#modCID").val()));

    $("#newExistingPartnerForm").on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#newExistingPartnerForm")[0]);
        SmoothPost(formData, "addPartner", "characterinfo", "c", $("#cid").val());
    });
}

