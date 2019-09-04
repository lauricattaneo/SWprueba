$( document ).ready(function() {
    $('.importe').text(function (index, value ) {
       return (parseFloat(value)).toFixed(2);
    });
    $('.importe').prepend('<span style="float: left;">$</span>');
});