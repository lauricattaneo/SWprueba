/**
 * Libreria para funciones de autocompletado de entradas del usuario
 */
$(document).ready(function() {
    var autocomplete = {
        lastAutocomplete: new window.XMLHttpRequest(),
        query: '',
        getMatches: function(e) {
            clearTimeout(autocomplete.query);
            var $this = $(this),
                input = ($this.val()+event.key).trim();
            if (input.length >= 3 && event.key !== 'Backspace') {
                autocomplete.query = setTimeout(function() {
                    autocomplete.lastAutocomplete.abort();
                    autocomplete.lastAutocomplete = $.ajax({
                        type: "POST",
                        url: $this.data('url'),
                        data: {filter: input},
                        dataType: 'JSON',
                        success: function(response) {
                            $('ul#'+$this.attr('id')+'-Autocomplete').remove();
                            if (response.matchResults.length > 0) {
                                var $newList = $('<ul id="'+$this.attr('id')+'-Autocomplete" class="autocomplete-result list-group"></ul>');
                                for (var i=0; i<response.matchResults.length; i++) {
                                    $newList.append('<li class="list-group-item">'+response.matchResults[i].replace(new RegExp(input, 'i'), '<strong>'+input+'</strong>')+'</li>');
                                }

                                $newList.insertAfter($this.closest('.form-group'));
                                $newList.find('li').on('click', function() {
                                    $this.val($(this).text()).focus().trigger('change');
                                });
                                $($newList).append('<li class="hide-autocomplete list-group-item btn-link">Ocultar sugerencias</li>');

                                $(window).on('click', function removeAutocomplete() {
                                    $('ul#'+$this.attr('id')+'-Autocomplete').remove();
                                });
                            }
                        },
                    });
                }, 1000);
            } else {
                $('ul#'+$this.attr('id')+'-Autocomplete').remove();
            }
        },
        /**
         * Inicializa campos con autocompletado
         * @returns {undefined}
         */
        init: function() {
            $(".autocomplete-field").on('keypress', autocomplete.getMatches);
            
            $('body').on('click', '.hide-autocomplete', function() {
                $(this).closest('ul').remove();
                $(window).off('click', 'removeAutocomplete');
            });
        }
    };

    autocomplete.init();
});