function LoadPage(page, getKey = null, getValue = null){

    let urlAppendix = "";
    if(getKey != null) urlAppendix = `&${getKey}=${getValue}`;
    $.get({
        url: `/page/${page}.php?${Math.random()}${urlAppendix}`,
        success: function(response){
            $("main").html(response);
            BindMenu();
            Bind();
        }
    });
}

function SmoothLoadPage(page, getKey = null, getValue = null){
    CloseModal();
    $(".contentWrapper").addClass("fadeOut");
    setTimeout(function(){
        LoadPage(page,getKey,getValue);
    },300);

}

function SmoothPost(data, postPage, redirectPage, getKey = null, getValue = null, callback = null){
    $.post({
        url: `/post/${postPage}.php`,
        data: data,
        processData: false,
        contentType: false,
        encode: true,
        // success: () => 
        // {
        //     if(callback != null) callback();
        //     if(redirectPage != null) SmoothLoadPage(redirectPage, getKey, getValue)
        // },
        success: function (response) { console.log(response); },
        error: function (errorThrown) { console.log(errorThrown); }
    });
}

function LoadModal(page, getKey = null, getValue = null){
    $(".modalWrapper").removeClass("modClose").addClass("modOpen").css("display", "block");
    let urlAppendix = "";
    if(getKey != null) urlAppendix = `&${getKey}=${getValue}`;

    $(".modalContent").removeClass("modContentFadeIn").addClass("modContentFadeOut");
    setTimeout(function(){
        $.get({
            url: `/modal/${page}.php?${Math.random()}${urlAppendix}`,
            success: function(response){
                $(".modalContent").html(response);
                $(".modalContent").removeClass("modContentFadeOut").addClass("modContentFadeIn");
                BindMenu();
                Bind();
            }
        });
    }, 200);
}

function CloseModal(){
    $(".modalWrapper").removeClass("modOpen").addClass("modClose"); 
    setTimeout(() => $(".modalWrapper").css("display", "none"), 200);
}


function b64DecodeUnicode(str) {
    // Going backwards: from bytestream, to percent-encoding, to original string.
    return decodeURIComponent(atob(str).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
}