<?php

namespace Caja\SiafcaIntranetBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidaFechaNac extends Constraint
{
    public $messageI   = 'No puede ingresar una persona menor de 18 años.';
    public $messageII  = 'La fecha de nacimiento no puede ser posterior a la fecha actual.';
    public $messageIII = 'La fecha ingresada es incorrecta, intente nuevamente.';
    public $messageIV  = 'Ingrese la fecha en el formato dd/mm/aaaa.'; 
 
    public function validatedBy()
    {
           return get_class($this).'Validator';
    }
}
