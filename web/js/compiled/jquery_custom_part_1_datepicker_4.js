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