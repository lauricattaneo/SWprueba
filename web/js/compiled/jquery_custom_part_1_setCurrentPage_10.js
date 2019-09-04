function showHidden(DOMelement)
{
    $( DOMelement ).removeClass('hidden');
}

function highlight(element)
{
    $( element ).addClass("active");
}

function hideElement(DOMelement)
{
    $( DOMelement ).addClass('hidden');
}

function hideElementsJQuery(DOMelements,duration)
{
    $.each(DOMelements, function (i,val){
        $(val).hide(duration);
    });
}

function showHiddenJQuery(DOMelement,duration)
{
    $(DOMelement).show(duration);
}

function showHiddenElementsJQuery(DOMelements,duration)
{
    $.each(DOMelements, function (i,val){
        $(val).show(duration);
    });
}