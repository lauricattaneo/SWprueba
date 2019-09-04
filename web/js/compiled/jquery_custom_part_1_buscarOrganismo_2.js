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