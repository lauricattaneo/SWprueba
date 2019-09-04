<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * TipoDocumento
 *
 * @ORM\Table(name="tipo_documento"
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_tipodocumento_1", columns={"codigo"}),
 *      @ORM\UniqueConstraint(name="uk_tipodocumento_2", columns={"nombre"})
 *  })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\TipoDocumentoRepository")
 */

class TipoDocumento
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Groups({"groupTipoDocumento"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=1, unique=true, nullable=false)
     * @Groups({"groupTipoDocumento"})
     */
    private $codigo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=150, unique=true)
     * @Groups({"groupTipoDocumento"})
     */
    private $nombre;
    


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

     /**
     * Get codigo
     *
     * @return string
     */
     function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return $this
     */
    function setCodigo($codigo) {
        $this->codigo = $codigo;
        return $this;
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return TipoDocumento
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    public function __toString() {
        return $this->nombre;
    }
       
}
