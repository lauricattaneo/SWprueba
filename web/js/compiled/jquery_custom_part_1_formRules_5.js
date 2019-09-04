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