<?php

namespace Caja\SiafcaIntranetBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Transformer encargado de convertir id de tipo string
 * a objetos de entidad ExpteAmpItem al enviar datos desde formularios
 */
class ExpteAmpItemToNumberTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (ExpteAmpItem) to a string (number).
     *
     * @param  ExpteAmpItem|null $issue
     * @return string
     */
    public function transform($ExpteAmpItem)
    {
        return (null === $ExpteAmpItem)? '' : $ExpteAmpItem->getId();
    }

    /**
     * Transforms a string (number) to an object (ExpteAmpItem).
     *
     * @param  string $ExpteAmpItemId
     * @return ExpteAmpItem|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($ExpteAmpItemId)
    {
        $ExpteAmpItem = null;
        
        if ($ExpteAmpItemId) {
            $ExpteAmpItem = $this->em
                ->getRepository('SiafcaIntranetBundle:ExpteAmpItem')
                ->find($ExpteAmpItemId);

            if (null === $ExpteAmpItem) {
                throw new TransformationFailedException(sprintf(
                    'No existe un amparado con id "%s"',
                    $ExpteAmpItem
                ));
            }
        }

        return $ExpteAmpItem;
    }
}
