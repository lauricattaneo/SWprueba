/* 
 * Validar el ingreso del cuil, no permite el ingreso de teclas no numericas.
 */
//ref: https://es.stackoverflow.com/questions/122777/como-bloquear-el-copiar-y-pegar-en-un-input
//accedo al elemento input
var input_cuil = document.getElementById('searchForm_cuil');

$(function()
{
    $("#searchForm_cuil").on("change",function()
    {
        var texto_ingresado = input_cuil.value;
        //ref: https://code.i-harness.com/es/q/9eacd3
        var str = texto_ingresado.replace(/[^0-9\.]+/g, "");
        input_cuil.value = str;
    });
});

//agrego un lisener al presskey y lo asocio a una funcion anonima
//keydown es el evento de tecla presionada
input_cuil.addEventListener('keydown', function(evento) 
{
  var longitud = input_cuil.value.length;
    
  //leo la tecla presionada, llega en la variable evento
  var teclaPresionadaCodigo = evento.which;
//  alert(teclaPresionadaCodigo);
  
  //  Ref:https://css-tricks.com/snippets/javascript/javascript-keycodes/
  var esnumero = teclaPresionadaCodigo > 47 && teclaPresionadaCodigo < 58 
                 || teclaPresionadaCodigo > 95 && teclaPresionadaCodigo < 106 ;
  
  var esflechita = teclaPresionadaCodigo > 36 && teclaPresionadaCodigo < 41;
  
  var esborrar = teclaPresionadaCodigo === 8 || teclaPresionadaCodigo === 46;
  
  var esenter = teclaPresionadaCodigo === 13;
  
  if(esnumero === false && esflechita === false && esborrar === false && esenter === false)
  {
         evento.preventDefault();
  }
 
  
  if(longitud > 11)
  {
      if(esflechita === false && esborrar === false)
      {
          evento.preventDefault();
      }
  }
    
}
);
