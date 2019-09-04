<?php

namespace Caja\SiafcaIntranetBundle\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * Provincia
 *
 * @ORM\Table(name="provincia"
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_provincia_1", columns={"nombre"}),
 *      @ORM\UniqueConstraint(name="uk_provincia_2", columns={"abreviaturaprovincia"}),
 *  }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ProvinciaRepository")
 */

class Provincia
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Groups({"groupProvincia"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     * @Groups({"groupProvincia"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="abreviaturaProvincia", type="string", length=150, nullable=true, unique=true)
     * @Groups({"groupProvincia"})
     */
    private $abreviaturaProvincia;
    
    /**
    * @ORM\OneToMany(targetEntity="Departamento", mappedBy="provincia")
    */
    private $departamentos;


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
     * @return Provincia
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
     * Set abreviaturaProvincia
     *
     * @param string $abreviaturaProvincia
     *
     * @return Provincia
     */
    public function setAbreviaturaProvincia($abreviaturaProvincia)
    {
        $this->abreviaturaProvincia = $abreviaturaProvincia;

        return $this;
    }

    /**
     * Get abreviaturaProvincia
     *
     * @return string
     */
    public function getAbreviaturaProvincia()
    {
        return $this->abreviaturaProvincia;
    }
    
    /**
     * Add departamento
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Departamento $departamento
     *
     * @return Provincia
     */
    public function addDepartamento(\Caja\SiafcaIntranetBundle\Entity\Departamento $departamento)
    {
        $this->departamentos[] = $departamento;

        return $this;
    }

    /**
     * Remove departamento
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Departamento $departamento
     */
    public function removeDepartamento(\Caja\SiafcaIntranetBundle\Entity\Departamento $departamento)
    {
        $this->departamentos->removeElement($departamento);
    }

    /**
     * Get departamentos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartamentos()
    {
        return $this->departamentos;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->departamentos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString() {
        return $this->nombre;
    }

}
