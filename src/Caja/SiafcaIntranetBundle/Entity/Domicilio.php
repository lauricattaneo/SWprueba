<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Domicilio
 *
 * @ORM\Table(name="domicilio"
 * , indexes={
 *      @ORM\Index(name="idx_domicilio_1", columns={"organismo_id"}),
 *      @ORM\Index(name="idx_domicilio_2", columns={"localidad_id"}),
 *      @ORM\Index(name="idx_domicilio_3", columns={"tipodomicilio_id"}),
 *      @ORM\Index(name="idx_domicilio_4", columns={"persona_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\DomicilioRepository")
 */

class Domicilio
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Groups({"groupDomicilio"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="calle", type="string", length=255, nullable=true)
     * @Groups({"groupDomicilio"})
     * @Assert\NotBlank(message="Se debe indicar la calle")
     */
    private $calle;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=100, nullable=true)
     * @Groups({"groupDomicilio"})
     * @Assert\NotBlank(message="Indicar el número del portal")
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="depto",type="string", length=100, nullable=true)
     * @Groups({"groupDomicilio"})
     */
    private $depto;

    /**
     * @var string
     *
     * @ORM\Column(name="piso", type="string", length=100, nullable=true)
     * @Groups({"groupDomicilio"})
     */
    private $piso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="manzana", type="string", length=100, nullable=true)
     * @Groups({"groupDomicilio"})
     */
    private $manzana;
    
    /**
     * @var string
     *
     * @ORM\Column(name="lote", type="string", length=100, nullable=true)
     * @Groups({"groupDomicilio"})
     */
    private $lote;
    
    /**
     * @var string
     *
     * @ORM\Column(name="monoblock", type="string", length=100, nullable=true)
     * @Groups({"groupDomicilio"})
     */
    private $monoblock;
    
    /**
     * @ORM\ManyToOne(targetEntity="TipoDomicilio")
     * @ORM\JoinColumn(name="tipodomicilio_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank(message="Especificar el tipo de domicilio")
     */
    private $tipoDomicilio;
    
    /**
     * @ORM\ManyToOne(targetEntity="Localidad")
     * @ORM\JoinColumn(name="localidad_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Indicar la localidad")
     * @Groups({"groupDomicilio"})
     */
    private $localidad;
    
    /**
     * @ORM\ManyToOne(targetEntity="Persona", inversedBy="domicilios")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     */
    private $persona;
    
    /**
     * @ORM\ManyToOne(targetEntity="Oficina", inversedBy="domicilios")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     */
    private $organismo;
    
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
     * Set calle
     *
     * @param string $calle
     *
     * @return Domicilio
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    /**
     * Get calle
     *
     * @return string
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Domicilio
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set depto
     *
     * @param string $depto
     *
     * @return Domicilio
     */
    public function setDepto($depto)
    {
        $this->depto = $depto;

        return $this;
    }

    /**
     * Get depto
     *
     * @return string
     */
    public function getDepto()
    {
        return $this->depto;
    }

    /**
     * Set piso
     *
     * @param string $piso
     *
     * @return Domicilio
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;

        return $this;
    }

    /**
     * Get piso
     *
     * @return string
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * Set manzana
     *
     * @param string $manzana
     *
     * @return Domicilio
     */
    public function setManzana($manzana)
    {
        $this->manzana = $manzana;

        return $this;
    }

    /**
     * Get manzana
     *
     * @return string
     */
    public function getManzana()
    {
        return $this->manzana;
    }

    /**
     * Set lote
     *
     * @param string $lote
     *
     * @return Domicilio
     */
    public function setLote($lote)
    {
        $this->lote = $lote;

        return $this;
    }

    /**
     * Get lote
     *
     * @return string
     */
    public function getLote()
    {
        return $this->lote;
    }

    /**
     * Set monoblock
     *
     * @param string $monoblock
     *
     * @return Domicilio
     */
    public function setMonoblock($monoblock)
    {
        $this->monoblock = $monoblock;

        return $this;
    }

    /**
     * Get monoblock
     *
     * @return string
     */
    public function getMonoblock()
    {
        return $this->monoblock;
    }

    /**
     * Set localidad
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Localidad $localidad
     *
     * @return Domicilio
     */
    public function setLocalidad(\Caja\SiafcaIntranetBundle\Entity\Localidad $localidad = null)
    {
        $this->localidad = $localidad;

        return $this;
    }

    /**
     * Get localidad
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Localidad
     */
    public function getLocalidad()
    {
        return $this->localidad;
    }

    /**
     * Set persona
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Persona $persona
     *
     * @return Domicilio
     */
    public function setPersona(\Caja\SiafcaIntranetBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Persona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set tipoDomicilio
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\TipoDomicilio $tipoDomicilio
     *
     * @return Domicilio
     */
    public function setTipoDomicilio(\Caja\SiafcaIntranetBundle\Entity\TipoDomicilio $tipoDomicilio = null)
    {
        $this->tipoDomicilio = $tipoDomicilio;

        return $this;
    }

    /**
     * Get tipoDomicilio
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\TipoDomicilio
     */
    public function getTipoDomicilio()
    {
        return $this->tipoDomicilio;
    }

    /**
     * Set organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Oficina $oficina
     *
     * @return Domicilio
     */
    public function setOrganismo(\Caja\SiafcaIntranetBundle\Entity\Oficina $oficina = null)
    {
        $this->organismo = $oficina;

        return $this;
    }

    /**
     * Get organismo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Oficina
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }
    
    public function isDomicilioReal()
    {
        return $this->tipoDomicilio->isTipoDomicilioReal();
    }
    
    public function __toString() {
        return $this->tipoDomicilio.' - '.$this->calle.' '.$this->numero.' ('.$this->localidad->getNombre().' - '.$this->localidad->getCodPostal().')';
        
    }
    
    public function getEntidadToShow()
    {
        if (!$this->persona && $this->organismo) {
            return $this->organismo->__toString();
        } elseif ($this->persona && !$this->organismo) {
            return $this->persona->__toString();
        }
        
        return null;
        
    }
}
