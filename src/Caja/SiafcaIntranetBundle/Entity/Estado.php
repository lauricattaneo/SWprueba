<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Symfony\Component\Serializer\Annotation\Groups;

//    @ORM\UniqueConstraint(name="uk_estado_2", columns={"nombre"}),
        
/**
 * Estado
 *
 * @ORM\Table(name="estado"
 * , uniqueConstraints={
 *    @ORM\UniqueConstraint(name="uk_estado_1", columns={"clase", "estado"}),
  *
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\EstadoRepository")
 */

class Estado
{   
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"idEstadoInternet","groupEstado"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=6)
     * @Groups({"groupEstado"})
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=false)
     * @Groups({"groupEstado"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupEstado"})
     */
    private $descripcion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="clase", type="string", length=50, nullable=false)
     * @Groups({"groupEstado"})
     */
    private $clase;
    
    
    /**
     * @ORM\OneToMany(targetEntity="Transicion", mappedBy="estado_inicial",cascade="persist")
     */
    private $transiciones;
    
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
     * Set estado
     *
     * @param string $estado
     *
     * @return Estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Estado
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Estado
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    
    public function isValid()
    {
        if ($this->estado == '1')
        {
            return true;
        }
        return false;
    }
    
    public function __toString() {
        return $this->nombre;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transiciones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add transicione
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Transicion $transicione
     *
     * @return Estado
     */
    public function addTransicione(\Caja\SiafcaIntranetBundle\Entity\Transicion $transicione)
    {
        $this->transiciones[] = $transicione;

        return $this;
    }

    /**
     * Remove transicione
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Transicion $transicione
     */
    public function removeTransicione(\Caja\SiafcaIntranetBundle\Entity\Transicion $transicione)
    {
        $this->transiciones->removeElement($transicione);
    }

    /**
     * Get transiciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransiciones()
    {
        return $this->transiciones;
    }

    /**
     * Set clase
     *
     * @param string $clase
     *
     * @return Estado
     */
    public function setClase($clase)
    {
        $this->clase = $clase;

        return $this;
    }

    /**
     * Get clase
     *
     * @return string
     */
    public function getClase()
    {
        return $this->clase;
    }
    
    public function isEstado($estado,$clase)
    {
        return ($this->estado == $estado && $this->clase == $clase);
    }
    
    public function proximoEstado($codigoTransicion, $claseTransicion)
    {
        foreach ($this->transiciones as $transicion)
        {
            if ($transicion->isTransicion($codigoTransicion,$claseTransicion)){
                return $transicion->getEstadoFinal();
            }
        }
        
        return null;
    }
}
