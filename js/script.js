function LoadPage(page, getKey = null, getValue = null){

    let urlAppendix = "";
    if(getKey != null) urlAppendix = `&${getKey}=${getValue}`;
    $.get({
        url: `/page/${page}.php?${Math.random()}${urlAppendix}`,
        success: function(response){
            $("main").html(response);
            BindUniversal();
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
        success: (response) => 
        {
            console.log(response); 
            if(callback != null) callback();
            if(redirectPage != null) SmoothLoadPage(redirectPage, getKey, getValue)
        },
        //success: function (response) { console.log(response); },
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
                BindUniversal();
                ModalBind();
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

const Directions = {
	Left: "L",
	Right: "R",
	Top: "T",
	Bottom: "B"
}


function DrawSvgBranches(){
    try{
        let paths = $("#pathDef").val().split(';');

        paths.forEach(function(value, index){

            let segments = value.split(':');

            let type = segments[0];
            let points = segments[1].split('&');
            let lineColor = segments[2];
            let lineType = segments[3];
            let lineSize = segments[4];

            let coords = "";
            if(type == 'N') coords = GetNormalConnectionPoints(points)
            else coords = GetBranchConnectionPoints(points);

            if(coords != ""){
                try{
                    let svg = $(".branch")[0];
                    let newElement = document.createElementNS("http://www.w3.org/2000/svg", 'path');
                    newElement.setAttribute("d",coords); 
                    newElement.style.stroke = lineColor; 
                    newElement.style.strokeWidth = lineSize + "px";
                    newElement.style.strokeLinecap = "round";
                    newElement.style.strokeDasharray = lineType;
                    newElement.style.fill = "none";
                    svg.appendChild(newElement);
                }
                catch(e){
                    //console.warn("Error drawing to target " + targetID);
                }
            }
        });
    }
    catch(e){}
}

function GetNormalConnectionPoints(points){
    let pointA = points[0];
    let elemIDA = points[1];
    let pointB = points[2];
    let elemIDB = points[3];

    if(elemIDA == undefined || elemIDA == "00000000-0000-0000-0000-000000000000"
    || elemIDB == undefined || elemIDB == "00000000-0000-0000-0000-000000000000") return "";

    let elemA = $(`.treeNode[d-chid="${elemIDA}"]`);
    let elemADom = elemA[0];
    let elementAContainer = elemA.parent()[0];

    let elemB = $(`.treeNode[d-chid="${elemIDB}"]`);
    let elemBDom = elemB[0];
    let elementBContainer = elemB.parent()[0];

    let aPos = GetBranchCoord(elemADom, elementAContainer, pointA);
    let bPos = GetBranchCoord(elemBDom, elementBContainer, pointB);

    return `M ${aPos.xPos} ${aPos.yPos} L ${bPos.xPos} ${bPos.yPos}`;
}

function GetBranchConnectionPoints(points){
    let pointA1 = points[0];
    let elemIDA1 = points[1];
    let pointA2 = points[2];
    let elemIDA2 = points[3];
    let pointB = points[4];
    let elemIDB = points[5];

    if(elemIDA1 == undefined || elemIDA1 == "00000000-0000-0000-0000-000000000000"
    || elemIDA2 == undefined || elemIDA2 == "00000000-0000-0000-0000-000000000000"
    || elemIDB == undefined || elemIDB == "00000000-0000-0000-0000-000000000000") return "";

    let elemA1 = $(`.treeNode[d-chid="${elemIDA1}"]`);
    let elemA1Dom = elemA1[0];
    let elementA1Container = elemA1.parent()[0];

    let elemA2 = $(`.treeNode[d-chid="${elemIDA2}"]`);
    let elemA2Dom = elemA2[0];
    let elementA2Container = elemA2.parent()[0];

    let elemB = $(`.treeNode[d-chid="${elemIDB}"]`);
    let elemBDom = elemB[0];
    let elementBContainer = elemB.parent()[0];

    let a1Pos = GetBranchCoord(elemA1Dom, elementA1Container, pointA1);
    let a2Pos = GetBranchCoord(elemA2Dom, elementA2Container, pointA2);
    
    let aPos = {
        xPos: (a1Pos.xPos + a2Pos.xPos)/2,
        yPos: (a1Pos.yPos + a2Pos.yPos)/2,
    };
    
    let bPos = GetBranchCoord(elemBDom, elementBContainer, pointB);

    return `M ${aPos.xPos} ${aPos.yPos} L ${bPos.xPos} ${bPos.yPos}`;



}


function GetBranchCoord(element, container, position){
    let x = element.offsetLeft + container.offsetLeft;
    let y = element.offsetTop + container.offsetTop;

    switch(position){
        case Directions.Left: return { xPos: x, yPos: y + element.clientHeight/2};
        case Directions.Right: return { xPos: x + element.clientWidth, yPos: y + element.clientHeight/2};
        case Directions.Top: return { xPos: x + element.clientWidth/2, yPos: y};
        case Directions.Bottom: return { xPos: x + element.clientWidth/2, yPos: y + element.clientHeight};
    }   
}
