<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Cargo
 *
 * @ORM\Table(name="ayuda"
 * , uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_codigo", columns={"codigo"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\AyudaRepository")
 */

class Ayuda
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupAyuda"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=15)
     * @Groups({"groupAyuda"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="text", nullable=true, options = {"default": "EMPTY_CLOB()"})
     * @Groups({"groupAyuda"})
     */
    private $texto;


    public function getId()
    {
        return $this->id;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getTexto()
    {
        return $this->texto;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    public function setTexto($texto)
    {
        $this->texto = $texto;
    }
    public function __toString()
    {
        return $this->texto;
    }

}
