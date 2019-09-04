function setRequired(ids,value)
{
    if ($.isArray(ids)){
        setRequireds(ids,value);
    } else{
        var iput = ids +' :input';
        $.each($(iput ), function (){
            $(this).prop('required',value);
        });
    }
}

function setRequireds(ids,value)
{
    $.each(ids,function (i,val){
        var iput = val +' :input';
        $.each($(iput ), function (){
            $(this).prop('required',value);
        });
    });
}

function unSelect(DOMelements)
{
    $.each(DOMelements,function (i,val){
        var toUnselect = val + ' option:selected';
        $(toUnselect).removeAttr("selected");
    });
}