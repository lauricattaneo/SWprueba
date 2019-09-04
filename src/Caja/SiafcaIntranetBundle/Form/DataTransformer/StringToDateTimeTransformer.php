<?php

namespace Caja\SiafcaIntranetBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transformer encargado de convertir fechas en formato string
 * a objetos DateTime (en casos donde el campo no este asociado a una entidad)
 */
class StringToDateTimeTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object to a string.
     *
     * @param  DateTime|null $datetime
     * @return string
     */
    public function transform($dateTime)
    {
        return is_null($dateTime)? '' : $dateTime->format('d/m/Y');
    }

    /**
     * Transforms a string to an object (DateTime).
     *
     * @param  string $stringDate
     * @return DateTime|null
     * @throws TransformationFailedException If format is wrong.
     */
    public function reverseTransform($stringDate)
    {
        $object = $matches = null;
        
        if (is_string($stringDate) && $stringDate) {
            // El formato de entrada en los formularios al ser tipo texto es: dd/mm/aaaa
            preg_match('/(\d\d)\/(\d\d)\/(\d{4})/', $stringDate, $matches);
            if (count($matches) == 4) {
                // Cambia el formato de fecha para ser correctamente parseado al de db en servicio
                list($day, $month, $year) = array($matches[1], $matches[2], $matches[3]);
                $object = (new \DateTime())->setDate($year, $month, $day);
                // Por defecto se asigna la hora actual, tener en cuenta en comparaciones con rangos de fecha
            }
        }
        
        if (is_string($stringDate) && trim($stringDate) !== '' && count($matches) !== 4) {
            throw new TransformationFailedException(sprintf(
                'El formato de la fecha es incorrecto',
                $stringDate
            ));
        }
        
        return $object;
    }
}
