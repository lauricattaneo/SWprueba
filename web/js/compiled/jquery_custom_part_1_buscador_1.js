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