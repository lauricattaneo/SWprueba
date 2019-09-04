<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Caja\SiafcaIntranetBundle\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ClobType extends Type {
    
    const MYTYPE = 'CLOB';
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {        
        return $value;        
    }
    
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_string($value)) {
            return $value;
        }

        try {
            $value = (string) $value;
            return $value;
        } catch (Exception $ex) {
            
        }
        return null;
    }
    
    public function getName()
    {
        return self::MYTYPE; // modify to match your constant name
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        
    }

}
