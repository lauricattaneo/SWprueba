/**
 * Libreria de validación y verificación de formularios
 */
var forms; // Global
$(document).ready(function() {
    forms = {
        /**
         * Indica si el formulario tiene todos los campos requeridos completos
         * @param $form Es un objeto jQuery formado a partir del formulario a verificar
         * @returns {Boolean}
         */
        validateRequiredFields: function($form) {
           var $required = $form.find('[required]:visible');
           return $required.filter(function() { return this.value; }).length === $required.length;
        },
        /**
         * Indica si los valores de campos con atributo pattern respetan el formato solicitado
         * @param $form Es un objeto jQuery formado a partir del formulario a verificar
         * @param {Boolean} changeErrorClass Indica si a medida que se verifican los campos debería actualizarse las clases
         * @returns {Boolean}
         */
        validatePatternFields: function($form, changeErrorClass) {
           changeErrorClass = changeErrorClass || true;
           var valid = true;
           $form.find('[pattern]:visible').each(function() {
               var val = $(this).val();
               if (val.trim() != '' && val.match($(this).attr('pattern')) === null) {
                   valid = false;
                   if (changeErrorClass) { $(this).parents('.form-group').addClass('has-error'); }
               }
           });
           if (!valid && changeErrorClass) { $('.js-validation').removeClass('hidden'); }
           return valid;
        },
        /**
         * Muestra los errores resultantes del submit debajo de cada campo del formulario
         * @param array errors Array asociativo con keys igual al nombre de la propiedad validada
         * @param {type} $form Formulario al que pertenece la validación
         * @returns {undefined}
         */
        displayFormErrors: function(errors, $form) {
            var arrayErrors = new Array();
            (function flattenErrors(actual) {
                Object.keys(actual).map(function(key, index) {
                    if (Array.isArray(actual[key])) {
                        var errorItem = {};
                        errorItem[key] = actual[key];
                        arrayErrors.push(errorItem);
                    } else {
                        flattenErrors(actual[key]);
                    }
                });
            }(errors));
            for (var i=0; i<arrayErrors.length; i++) {
                Object.keys(arrayErrors[i]).map(function(key) {
                    var $field = $form.find('[id$='+key+']');
                    $field.closest('.form-group').addClass('has-error');
                    $field.closest('.input-wrapper').after($('<span class="help-block">'+arrayErrors[i][key]+'</span>'));
                });
            }
        },
        /**
         * Inicializa los eventos que permitiran validar dinamicamente el formulario
         * @returns {undefined}
         */
        init: function() {
            // Puede haber mas de un form dentro del main. Por eso dentro de cada handler
            // se busca nuevamente el formulario con el que se esta interactuando. (parent/closest)
            var $form = $('main form');

            /**
             * Si hay una barra de progreso, actualiza el porcentaje completo,
             * tambien hace la validacion de campos requeridos:
             * Si estan completos activa los botones submit,
             * En otro caso lo deshabilita asignandole un title
             */
            $form.on('input change', '[required]:visible', function() {
                var $parentForm = $(this).closest('form'),
                    $progressBar = $parentForm.find('.progress-bar'),
                    $required = $parentForm.find('[required]:visible'),
                    filled = $required.filter(function() { return this.value; }).length;

                if ($progressBar) {
                    $progressBar
                        .css('width', (filled*100/$required.length)+'%')
                        .attr('aria-valuenow', Math.ceil(filled*100/$required.length));
                    $progressBar.find('span.sr-only')
                        .text(Math.ceil(filled*100/$required.length)+'% Complete (success)');
                }

                if ($parentForm.find('.has-error').length === 0) {
                    if (!forms.validateRequiredFields($parentForm)) {
                        $parentForm.find('button[type=submit], input[type=submit]').attr({
                            disabled: 'disabled',
                            title: 'Complete todos los campos obligatorios'
                        });
                    } else {
                        $parentForm.find('button[type=submit], input[type=submit]').removeAttr('disabled title');
                    }
                }
            });

            // Utilizada para simular placeholders en selects
            $form.on('change', 'select.hasPlaceholder', function() {
                if (this.value.trim() === '') {
                    if (this.classList && !this.classList.contains("phSelected")) {
                        this.classList.add("phSelected");
                    } else if (!this.classList && !(/\bphSelected\b/g.test(this.className))) {
                        this.className = "phSelected";
                    }
                } else {
                    if (this.classList) { this.classList.remove("phSelected"); }
                    else { this.className = this.className.replace(/\bphSelected\b/g, ""); }
                }
            }).trigger('change');

            /**
             * Si la vista del formulario tiene errores cargados, a medida que los
             * valores de campos con attr pattern se corrigen, se quita el formato de error
             */
            $form.on('input change', '[pattern]:visible', function() {
                var $parentForm = $(this).closest('form'),
                    $group = $(this).parents('.form-group');
                if ($(this).is('[pattern]') && $(this).val().trim().match($(this).attr('pattern')) !== null) {
                    $group.removeClass('has-error');
                    $group.find('ul.errors').remove();
                    if ($parentForm.find('.has-error').length === 0) { $('.js-validation').addClass('hidden'); }

                    if (!forms.validatePatternFields($parentForm)) {
                        $parentForm.find('button[type=submit], input[type=submit]').attr({
                            disabled: 'disabled',
                            title: 'Complete todos los campos obligatorios'
                        });
                    } else {
                        $parentForm.find('button[type=submit], input[type=submit]').removeAttr('disabled title');
                    }
                }
            });

            /**
             * Antes de realizar submit, controla los campos obligatorios y los que tienen pattern definido.
             * Desactiva los submits en caso de encontrar algo incorrecto
             */
            $form.submit(function(event) {
                if (!forms.validateRequiredFields($(this)) || !forms.validatePatternFields($(this))) {
                    event.preventDefault();
                    $(this).find('button[type=submit], input[type=submit]').attr({
                        disabled: 'disabled',
                        title: 'Complete todos los campos obligatorios'
                    });
                }
            });

            // Deshabilita los botones de submit al resetear el formulario
            $form.on('click', 'button[type=reset], input[type=reset]', function() {
                $(this).closest('form').find('button[type=submit], input[type=submit]').attr({
                    disabled: 'disabled',
                    title: 'Complete todos los campos obligatorios'
                });
            });

            $form.find('[required]', 'select.hasPlaceholder').trigger('change');

            $.datepicker.setDefaults({
                changeYear: true,
                changeMonth: true,
                monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
                monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
                dayNamesMin: [ "Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab" ],
                showOn: "button",
                buttonText: 'Seleccionar fecha',
                buttonImageOnly: true,
                dateFormat: "dd/mm/yy"
            });
        }
    };

    forms.init();
});