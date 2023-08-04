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
    $(".contentWrapper").addClass("fadeOut");
    setTimeout(function(){
        LoadPage(page,getKey,getValue);
    },300);

}

function SmoothPost(data, postPage, redirectPage, getKey = null, getValue = null){
    $.post({
        url: `/post/${postPage}.php`,
        data: data,
        processData: false,
        contentType: false,
        encode: true,
        success: () => 
        {
            if(redirectPage != null) SmoothLoadPage(redirectPage, getKey, getValue)
        },
        //success: function (errorThrown) { console.log(errorThrown); },
        error: function (errorThrown) { console.log(errorThrown); }
    });
}
