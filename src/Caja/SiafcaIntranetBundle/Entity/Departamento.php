<?php

namespace Caja\SiafcaIntranetBundle\Entity;
use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departamento
 *
 * @ORM\Table(name="departamento"
 * , indexes={
 *     @ORM\Index(name="idx_departamento_1", columns={"provincia_id"}),
 *     
 *  }
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_departamento_1", columns={"provincia_id","codigo","nombre"})
 *  })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\DepartamentoRepository")
 */

class Departamento
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupDepartamento"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=2, unique=false)
     * @Groups({"groupDepartamento"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=false)
     * @Groups({"groupDepartamento"})
     */
    private $nombre;
    
    /**
     * @ORM\ManyToOne(targetEntity="Provincia", inversedBy="departamentos")
     * @ORM\JoinColumn(name="provincia_id", referencedColumnName="id")
     * @Groups({"groupDepartamento"})
     */
    private $provincia;
    
    /**
       * @ORM\OneToMany(targetEntity="Localidad", mappedBy="departamento")
       */
    private $localidades;


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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Departamento
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Departamento
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
        $this->localidades = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set provincia
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Provincia $provincia
     *
     * @return Departamento
     */
    public function setProvincia(\Caja\SiafcaIntranetBundle\Entity\Provincia $provincia = null)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get provincia
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Add localidade
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Localidad $localidades
     *
     * @return Departamento
     */
    public function addLocalidade(\Caja\SiafcaIntranetBundle\Entity\Localidad $localidades)
    {
        $this->localidades[] = $localidades;

        return $this;
    }

    /**
     * Remove localidade
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Localidad $localidades
     */
    public function removeLocalidade(\Caja\SiafcaIntranetBundle\Entity\Localidad $localidades)
    {
        $this->localidades->removeElement($localidades);
    }

    /**
     * Get localidades
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocalidades()
    {
        return $this->localidades;
    }
    
    public function __toString() {
        return $this->nombre;
    }
}
