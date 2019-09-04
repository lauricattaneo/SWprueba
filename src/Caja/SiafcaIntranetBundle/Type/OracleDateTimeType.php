<?php

namespace Caja\SiafcaIntranetBundle\Type;

use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

class OracleDateTimeType extends DateTimeType {
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            $toDb = null;
        } else {
            if ($value instanceof \DateTime) {
                $toDb = $value->format("Y-m-d H.i.s");
            } else if (is_array($value)) { // El serializer de symfony esta codificando DateTimes como array(date, timezone_type, timezone)
                $toDb = (new \DateTime($value['date']))->format("Y-m-d H.i.s");
            }
        }
        return $toDb;
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_null($value) || $value instanceof \DateTime) {
            return $value;
        }

        $val = \DateTime::createFromFormat("Y-m-d H.i.s", $value);
        if (!$val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), "Y-m-d H.i.s" );
        }
        return $val;
    }
}