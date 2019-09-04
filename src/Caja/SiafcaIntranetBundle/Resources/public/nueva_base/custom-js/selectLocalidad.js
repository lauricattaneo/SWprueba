// Función que remueve todos los nodos hijos de
// un elemento DOM, excepto el primero
// (útil para los campos select de los formularios)
// Params:
//          -ids: los id de los elementos DOM a los cuales se le borran los nodos hijos
function removeElementsButFirst(ids)
{
    $.each(ids,function(key,value) 
    {
      $(value).children('option:not(:first)').remove();
    });
}
// Función que trae datos del servidor (vía JQuery AJAX) 
// y los coloca en un campo select
// Params:
//          -path: el path al controlador que valida el cuil
//          -id: el id del campo select
//          -idvalue: el id del valor sobre el cuál se va a realizar la búsqueda ( Ej: nombre del organismo)
//          -element: nombre de los elementos a buscar ( es el nombre con el cúal se va a indentificar
//                    el valor "idvalue" en el controlador, en el controlador se vería así:
//                    $request->query()->get('element');)
function searchElements(path,id,idvalue, element)
{
    if ( idvalue )
    {
        var value = {};
        value[ element ] = $(idvalue).val();
    }
    $.getJSON( path, value, function( data ) 
    {
        var items = [];
        $.each( data, function( key, val ) 
        {
          items.push( "<option value='" + key + "'>" + val + "</option>" );
        });

        $(id).append(items);
    });
}


function searchElements2(path,id,idvalue, element,newVal)
{
    if ( idvalue )
    {
        var value = {};
        value[ element ] = idvalue;
    }
    $.when($.getJSON( path, value, function( data ) 
    {
        var items = [];
        $.each( data, function( key, val ) 
        {
          items.push( "<option value='" + key + "'>" + val + "</option>" );
        });

        $(id).append(items);
    })).done(function(){$(id).val(newVal)});
}