// Función que setea cómo responder a los eventos cuando se presiona una tecla
// y verifica que tecla se presiona para un campo de formulario,
// Params:
//          -input: id del campo del formulario
//          -button: botón de submit del formulario
//          -path: path a donde debe redirigir en caso de que se haya presionado enter
//          -list: el elemento DOM <list> donde se muestran los resultados de la búsqueda
//          -placeholder: atributo placeholder que se va a mostrar en el campo del formulario
function buscador(input,button,path, list, placeholder)
{
    $( input).keyup(function(){             // Evento que se activa cada vez que se empieza a tipear
        var realpath = path + $( input ).val();
        $( button ).attr("href",realpath);
    });
    $( input ).keypress(function(event){    // Evento que se activa cada vez que se presiona una tecla
        if (event.keyCode == 13) {          // if se presionó el botón ENTER
            if (isEmpty($( input ).val()))  // Si el campo está vacío no hace nada
            {
            }
            else{                           // Si no está vacío redirige a 'realpath'
                var href = $( button ).attr('href');
                window.location.href = href;
            }
        }
    });
    $( input).attr('placeholder',placeholder);
    noResult(list);
}

// Función que chequea si un string está vacío
function isEmpty(str) {
    return (!str || 0 === str.length);
}

// Si <list> no tiene hijos, se muestra 'No se encontraron resultados'
function noResult(list)
{
    if ($(list).children().length <= 0 )
    {
        $( "<li class='list-group-item' ><h3>No se encontraron resultados.</h3></li>" ).appendTo(list);
    }
}
$( document ).ready(function() {
    $("#nombreOrg").keyup(function(){
        var realpath = $("#btnBuscar").data('path')+$("#nombreOrg").val();
        $("#btnBuscar").attr("href",realpath);
    });
    $("#nombreOrg").keypress(function(event){
        if (event.keyCode == 13) {
            if (isEmpty($("#nombreOrg").val()))
            {
            }
            else{
                var href = $("#btnBuscar").attr('href');
                window.location.href = href;
            }
        }
    });
    noResult();
});
function isEmpty(str) {
    return (!str || 0 === str.length);
}
function noResult()
{
    if ($("#listOrg").children().length <= 0 )
    {
        $( "<li class='list-group-item' ><h3>No se encontraron resultados.</h3></li>" ).appendTo("#listOrg");
    }
}
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
function addDatePicker(DOMelement)
{
    $( DOMelement ).datepicker({
        changeYear: true,
        changeMonth: true,
        yearRange: "1900:2018",
        minDate: "-150Y",
        monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
        monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
        dayNamesMin: [ "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab" ],
        showOn: "button",
        buttonImage: "/bundles/cajasiafcaintranet/nueva_base/images/ic_event_black_24dp_2x.png",
        buttonText: 'Seleccionar fecha',
        buttonImageOnly: true,
    });
}
$( document ).ready(function() {
    $('label').slideToggle(0);
    $('input, textarea, select').on('focus focusout' ,function (){
        var label = $(this).closest('.form-group').find('label');
        if (!$(label).length) {
            label = $(this).closest('.form-group-top').find('label');
        }
        $(label).slideToggle(400);
    });
});
var $collectionHolder;

// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="">Agregar Domicilio</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);

$( document ).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('#domicilioslist');

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm($collectionHolder, $newLinkLi);
    });
});

function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
}



function onlyNumbers(e)
{
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A
        (e.keyCode == 65 && e.ctrlKey === true) ||
        // Allow: Ctrl+C
        (e.keyCode == 67 && e.ctrlKey === true) ||
        // Allow: Ctrl+X
        (e.keyCode == 88 && e.ctrlKey === true) ||
        // Allow: home, end, left, right
        (e.keyCode >= 35 && e.keyCode <= 39) ||
        //Allow numbers and numbers + shift key
        ((e.shiftKey && (e.keyCode >= 48 && e.keyCode <= 57)) || (e.keyCode >= 96 && e.keyCode <= 105))) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((!e.shiftKey && (e.keyCode < 48 || e.keyCode > 57)) || (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
}
$( document ).ready(function() {
    $('.importe').text(function (index, value ) {
       return (parseFloat(value)).toFixed(2);
    });
    $('.importe').prepend('<span style="float: left;">$</span>');
});
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