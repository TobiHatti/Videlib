

$(document).ready(() => {
    $.get({
        url: `/page/characterinfo.php?${Math.random()}&c=4893FA7B-1014-43ED-8628-C292B6A9CF9F`,
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
    $("button:not([noLoad])").each(function(){
        $(this).on("click", function(){
            $(this).attr("disabled", "disabled");
        });
    });
    
    $(".imgBlur").each(function(){
        $(this).on("click", function(){
            $(this).removeClass("imgBlur");
        });
    });

    $(".backBtn").each(function(){
        $(this).on("click", function(){
            SmoothLoadPage($(this).attr("d-page"), $(this).attr("d-pk"), $(this).attr("d-pv"))
        });
    });

    $(".btnCloseModal").on("click", () => CloseModal());

    $("#addCharacter").on("click", () => SmoothLoadPage("addCharacter"));

    $(".realCharacter").each(function(){
        $(this).on("click", () => SmoothLoadPage("characterinfo", "c", $(this).attr("d-chid")));
    });

    $("#addCharacterForm").off().on("submit", (event) => {
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

    $("#addImageForm").off().on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#addImageForm")[0]);
        formData.append('image', $("#imgBtn")[0].files[0])
        SmoothPost(formData, "characterimg", "characterinfo", "c", $("#cid").val());
    });

    $(".carouselImg").each(function(){
        let elem = $(this);
        $(this).on("click", function() {
            $("#mainImg").attr("src", elem.find("img").attr("src"));
            $("#modIID").val(elem.attr("d-iid"));
            if(elem.attr("d-sens") == "true") $("#mainImg").addClass("imgBlur");
            else $("#mainImg").removeClass("imgBlur");
            $("#btnMarkSensitive").attr("d-sens", elem.attr("d-sens"));
            $("#imgDescription").html(b64DecodeUnicode(elem.attr("d-idesc")));
        });
    });

    $("#addNoteForm").off().on("submit", function (event) {
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

    $(".modalBlur").on("click", () => CloseModal());

    $("#modBtnAddPartner").on("click", () => LoadModal("treeMenuPartner", "c", $("#modCID").val()));
    $("#modBtnAddChild").on("click", () => LoadModal("treeMenuChildren", "c", $("#modCID").val()))
    $("#modBtnAddChildWithExisting").on("click", () => LoadModal("treeMenuChildWithExisting", "c", $("#modCID").val()))
    $("#modBtnAddExistingPartner").on("click", () => LoadModal("treeMenuPartnerWithExisting", "c", $("#modCID").val()))
    
    // p1 & p2 --> Add child of p1 & p2
    $(".modBtnAddChildWithSuggestion").on("click", function() { SmoothLoadPage("addcharacter", "p1", `${$("#modCID").val()}&p2=${$(this).attr("d-sid")}`)});
    $("#modBtnAddChildWithNoPartner").on("click", function() { SmoothLoadPage("addcharacter", "p1", `${$("#modCID").val()}&p2=00000000-0000-0000-0000-000000000000`)});
    $("#newChildWithExistingPartnerForm").on("submit", (event) => {
        event.preventDefault();
        SmoothLoadPage("addcharacter", "p1", `${$("#modCID").val()}&p2=${$("#existingCharacter").val()}`);
    });

    $("#modBtnAddSibling").on("click", () => LoadModal("treeMenuSiblings", "c", $("#modCID").val()));
    $(".modBtnAddSiblingByParents").on("click", function() { SmoothLoadPage("addcharacter", "p1", `${$(this).attr("d-pida")}&p2=${$(this).attr("d-pidb")}`) } );
    
    // m --> Partner of m
    $("#modBtnAddNewPartner").on("click", () => SmoothLoadPage("addcharacter", "m", $("#modCID").val()));

    $("#newExistingPartnerForm").off().on("submit", (event) => {
        event.preventDefault();
        let formData = new FormData($("#newExistingPartnerForm")[0]);
        SmoothPost(formData, "addPartner", "characterinfo", "c", $("#cid").val());
    });

    $("#btnSetPrimaryImg").on("click", function() { LoadModal("imgPrimaryImg", "c", `${$("#cid").val()}&i=${$("#modIID").val()}`)});
    $("#btnEditAltText").on("click", function() { LoadModal("imgAltText", "c", `${$("#cid").val()}&i=${$("#modIID").val()}&idesc=${$("#imgDescription").html()}`)});
    $("#btnMarkSensitive").on("click", function() { LoadModal("imgMarkSensitive", "c", `${$("#cid").val()}&s=${$(this).attr("d-sens")}&i=${$("#modIID").val()}`)});
    $("#btnDeleteImage").on("click", function() { LoadModal("imgDelete", "c", `${$("#cid").val()}&i=${$("#modIID").val()}`)});
    $("#btnMoreActions").on("click", function() { LoadModal("imgMoreActions", "c", `${$("#cid").val()}&i=${$("#modIID").val()}`)});

    $("#formToggleSensitivity").off().on("submit", (event) => {
        event.preventDefault();
        $("#btnToggleSensitive").attr("disabled", "disabled");
        let formData = new FormData($("#formToggleSensitivity")[0]);
        SmoothPost(formData, "imgToggleSensitive", "characterinfo", "c", $("#modCID").val());
    });

    $("#formHideImage").off().on("submit", (event) => {
        event.preventDefault();
        $("#btnHideImg").attr("disabled", "disabled");
        let formData = new FormData($("#formHideImage")[0]);
        SmoothPost(formData, "imgToggleActive", "characterinfo", "c", $("#modCID").val());
    });

    $("#formPrimaryImage").off().on("submit", (event) => {
        event.preventDefault();
        $("#btnSetPrimaryImg").attr("disabled", "disabled");
        let formData = new FormData($("#formPrimaryImage")[0]);
        SmoothPost(formData, "imgSetPrimary", "characterinfo", "c", $("#modCID").val());
    });

    $("#formAltText").off().on("submit", (event) => {
        event.preventDefault();
        $("#btnSaveAltText").attr("disabled", "disabled");
        let formData = new FormData($("#formAltText")[0]);
        SmoothPost(formData, "imgSaveAltText", "characterinfo", "c", $("#modCID").val());
    });

    $("#btnOpenHiddenImgBrowser").on("click", () => SmoothLoadPage("hiddenImgGallery", "c", $("#modCID").val()));
    
    $(".galleryItem").each(function(){
        $(this).on("click",function(){
            if(!$(this).find("img").hasClass("permaImgBlur")){
                LoadModal("imgRestore", "c", `${$("#cid").val()}&i=${$(this).attr("d-iid")}`)
            }
            else {
                $(this).find("img").removeClass("permaImgBlur");
            }
        });
    });

    $("#formDeleteImage").off().on("submit", (event) => {
        event.preventDefault();
        $("#btnDeleteImage").attr("disabled", "disabled");
        let formData = new FormData($("#formDeleteImage")[0]);
        SmoothPost(formData, "imgDelete", "characterinfo", "c", $("#modCID").val());
    });
    
    
}

