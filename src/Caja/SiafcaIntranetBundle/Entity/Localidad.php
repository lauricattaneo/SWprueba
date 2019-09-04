<?php

namespace Caja\SiafcaIntranetBundle\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * Localidad
 *
 * @ORM\Table(name="localidad"
 * , indexes={
 *     @ORM\Index(name="idx_localidad_1", columns={"departamento_id"}),
 *  }
 *
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\LocalidadRepository")
 */

class Localidad
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Groups({"groupLocalidad"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Groups({"groupLocalidad"})
     */
    private $nombre;

    /**
     * @var string
     *@Groups({"groupLocalidad"})
     * @ORM\Column(name="codPostal", type="string", length=8)
     */
    private $codPostal;

    /**
     * @var string
     *@Groups({"groupLocalidad"})
     * @ORM\Column(name="subZona", type="string", length=3, nullable=true)
     */
    private $subZona;
    
    /**
     * @ORM\ManyToOne(targetEntity="Departamento", inversedBy="localidades")
     * @ORM\JoinColumn(name="departamento_id", referencedColumnName="id")
     * @Groups({"groupLocalidad"})
     */
    private $departamento;


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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Localidad
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
     * Set codPostal
     *
     * @param integer $codPostal
     *
     * @return Localidad
     */
    public function setCodPostal($codPostal)
    {
        $this->codPostal = $codPostal;

        return $this;
    }

    /**
     * Get codPostal
     *
     * @return int
     */
    public function getCodPostal()
    {
        return $this->codPostal;
    }
 

    /**
     * Set subZona
     *
     * @param integer $subZona
     *
     * @return Localidad
     */
    public function setSubZona($subZona)
    {
        $this->subZona = $subZona;

        return $this;
    }

    /**
     * Get subZona
     *
     * @return integer
     */
    public function getSubZona()
    {
        return $this->subZona;
    }
   
    public function __toString() {
        return $this->nombre;
    }

    /**
     * Set departamento
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Departamento $departamento
     *
     * @return Localidad
     */
    public function setDepartamento(\Caja\SiafcaIntranetBundle\Entity\Departamento $departamento = null)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * Get departamento
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Departamento
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
    }

}
