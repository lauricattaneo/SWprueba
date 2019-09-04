/**
 * Libreria de enmascaramiento de campos de formularios
 */
$(document).ready(function() {
    var masks = {
        /**
         * Enmascaramiento que permite solo caracteres numericos
         * @param {type} e
         * @returns {undefined}
         */
        onlyNumbers: function(e) {
            var valueChanged = false;
            if (e.type === 'propertychange') {
                valueChanged = e.originalEvent.propertyName === 'value';
            } else {
                valueChanged = true;
            }

            if (valueChanged) {
                var startPos = this.selectionStart,
                    newlVal = this.value,
                    filteredVal = newlVal.replace(/[^\d]/g, "");
                if (newlVal !== filteredVal) {
                    this.value = filteredVal;
                    var newCursorPos = startPos - (newlVal.length - filteredVal.length);
                    this.selectionStart = newCursorPos;
                    this.selectionEnd = newCursorPos;
                }
            }
        },
        /**
         * Enmascaramiento que permite solo fechas
         * @param {type} e
         * @returns {undefined}
         */
        onlyDates: function(e) {
            var valueChanged = false;
            if (e.type === 'propertychange') {
                valueChanged = e.originalEvent.propertyName === 'value';
            } else {
                valueChanged = true;
            }

            if (valueChanged) {
                var val = this.value,
                    caret_start = this.selectionStart,
                    caret_end = this.selectionEnd;

                val = val.replace(/[^\d]/g, '');
                val = val.replace(/^(\d{1,2})(\d{1,2})?(\d{1,4})?(.*)?$/g, function(all, a, b, c) {
                    var newValue = a;
                    if (b) newValue += '/'+b;
                    if (c) newValue += '/'+c;
                    return newValue;
                });

                $(this).val(val);

                if (caret_start === caret_end && (caret_start === 3 || caret_start === 6)) {
                    caret_end += 1;
                    caret_start += 1;
                } else if (caret_start !== caret_end) {
                    caret_start = caret_end;
                }
                this.selectionEnd = caret_end;
                this.selectionStart = caret_start;
                this.value = val;
            }
        },
        /**
         * Inicializa el enmascaramiento de campos
         * @returns {undefined}
         */
        init: function() {
            // Puede haber mas de un form dentro del main. Por eso dentro de cada handler
            // se busca nuevamente el formulario con el que se esta interactuando. (parent/closest)
            var $form = $('main form');
            
            $form.on('input change', '.only-numbers', masks.onlyNumbers);
            $form.on('input change', '.date-format', masks.onlyDates);
        }
    };

    masks.init();
});