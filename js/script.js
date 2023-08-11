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
    $(".treeNode").each(function(){
        DrawBranchCollections($(this), Directions.Top, $(this).attr("d-tbranch"));
        DrawBranchCollections($(this), Directions.Bottom, $(this).attr("d-bbranch"));
        DrawBranchCollections($(this), Directions.Left, $(this).attr("d-lbranch"));
        DrawBranchCollections($(this), Directions.Right, $(this).attr("d-rbranch"));
    });
}

function DrawBranchCollections(element, direction, collection){
    let branches = collection.split(';');
    
    branches.forEach(branch => {
        let branchSections = branch.split(':');
        DrawBranch(element, direction, branchSections[1], branchSections[0], branchSections[2]);
    });
}

function DrawBranch(element, fromDirection, targetID, toDirection, color){
    if(targetID != undefined && targetID != "00000000-0000-0000-0000-000000000000") {

        let sourceElement = element[0];
        let elementContainer = element.parent()[0];

        let dest = $(`.treeNode[d-chid="${targetID}"]`);
        let destinationElement = dest[0];
        let destinationContainer = dest.parent()[0];

        try{
            let svg = $(".branch")[0];
            let newElement = document.createElementNS("http://www.w3.org/2000/svg", 'path');
            newElement.setAttribute("d",`M${GetBranchCoord(sourceElement, elementContainer, fromDirection)} ${GetBranchCoord(destinationElement, destinationContainer, toDirection)}`); 
            newElement.style.stroke = color; 
            newElement.style.strokeWidth = "2px";
            newElement.style.fill = "none";
            svg.appendChild(newElement);
        }
        catch(e){
            //console.warn("Error drawing to target " + targetID);
        }
    }
}

function GetBranchCoord(element, container, position, straight = false){
    let x = element.offsetLeft + container.offsetLeft;
    let y = element.offsetTop + container.offsetTop;

    if(straight){
        switch(position){
            case Directions.Left: return `V${y + element.clientHeight/2 } H${x}`;
            case Directions.Right: return `V${y + element.clientHeight/2} H${x + element.clientWidth}`;
            case Directions.Top: return `V${y} H${x + element.clientWidth/2}`;
            case Directions.Bottom: return `V${y + element.clientHeight} H${x + element.clientWidth/2}`;
        }
    }
    else{
        switch(position){
            case Directions.Left: return `${x} ${y + element.clientHeight/2}`;
            case Directions.Right: return `${x + element.clientWidth} ${y + element.clientHeight/2}`;
            case Directions.Top: return `${x + element.clientWidth/2} ${y}`;
            case Directions.Bottom: return `${x + element.clientWidth/2} ${y + element.clientHeight}`;
        }
    }
}
