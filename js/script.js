function LoadPage(page){
    $.get({
        url: `/page/${page}.php?${Math.random()}`,
        success: function(response){
            $("main").html(response);
            BindMenu();
            Bind();
        }
    });
}

function SmoothLoadPage(page){
    $(".contentWrapper").addClass("fadeOut");
    setTimeout(function(){
        LoadPage(page);
    },300);

}

function SmoothPost(data, postPage, redirectPage){
    $.post({
        url: `/post/${postPage}.php`,
        data: data,
        processData: false,
        contentType: false,
        encode: true,
        //success: () => SmoothLoadPage(redirectPage),
        success: function (errorThrown) { console.log(errorThrown); },
        error: function (errorThrown) { console.log(errorThrown); }
    });
}
