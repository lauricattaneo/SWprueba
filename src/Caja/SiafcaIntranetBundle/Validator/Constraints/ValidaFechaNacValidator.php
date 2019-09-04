<?php

namespace Caja\SiafcaIntranetBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class ValidaFechaNacValidator extends ConstraintValidator 
{
    public function __construct() {
    }
    
    public function validate($fechaNac, Constraint $constraint)
    {
        if(is_null($fechaNac)) 
        {
            $this->context->buildViolation($constraint->messageIV)
                     ->atPath('t')
                     ->addViolation();
            return false;
        }
        
        $fechaActual = date("Y/m/d"); 
         
        $fechaActualConvertida = \DateTime::createFromFormat('Y/m/d', $fechaActual);
      
        if(!$this->fechaPosterior($fechaNac, $fechaActualConvertida, $constraint))
        {
            $this->menorEdad($fechaActualConvertida, $fechaNac, $constraint);
        } 
    }
    
    
    private function menorEdad($fechaActualConvertida,$fechaNac,$constraint)
    {
        $salida = false;
        //obtengo la diferencia entre fechas
        $interval= $fechaActualConvertida->diff($fechaNac);
        //obtengo los años de la diferencia
        $años=$interval->format('%y');
        if ($años<18)
        {
            $this->context->buildViolation($constraint->messageI)
                       ->atPath('t')
                       ->addViolation();
            $salida = true;
        }
        return $salida;
    }
    
    private function fechaPosterior($fechaNac,$fechaActualConvertida,$constraint)
    {
        $salida = false;
        if($fechaNac > $fechaActualConvertida)
        {
                $this->context->buildViolation($constraint->messageII)
                     ->atPath('t')
                     ->addViolation();
                $salida = true;
        }
        return $salida;
    }
}
