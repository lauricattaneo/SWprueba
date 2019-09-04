function searchPersonas(path,id,elementToAppend,linkElements)
{
    if ( id )
    {
        var value = {};
        value['cuil'] = $(id).val();
    }
    $.getJSON( path, value, function( data ) 
    {
        var items = [];
        $(elementToAppend).empty();
        if (JSON.stringify(data) !== '[]'){
            $.each( data, function( key, val ) 
            {
                stringToPush = "<a href='"+linkElements+"?persona="+key+"' class='list-group-item active'>";
                stringToPush += "<h4 class='list-group-item-heading'>"+val.nombre+"</h4>";
                stringToPush += "<p class='list-group-item-text'>C.U.I.L.: "+ val.cuil;
                stringToPush += "</p> </a>";
                items.push(stringToPush);
            });
        } else{
            items.push("<p class='list-group-item-text'>No se encontraron resultados.</p> ");
        }   
        $(elementToAppend).append(items);
    });
}