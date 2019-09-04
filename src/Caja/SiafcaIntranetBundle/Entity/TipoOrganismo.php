<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * TipoOrganismo
 *
 * @ORM\Table(name="tipo_organismo"
 *   , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_TipoOrganismo_1", columns={"codigo"}),
 *      @ORM\UniqueConstraint(name="uk_TipoOrganismo_2", columns={"nombre"}),
 *      @ORM\UniqueConstraint(name="uk_TipoOrganismo_3", columns={"nroCuenta"}),
 * })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\TipoOrganismoRepository")
 */

class TipoOrganismo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupTipoOrganismo"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=20, unique=true)
     * @Groups({"groupTipoOrganismo"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, unique=true)
     * @Groups({"groupTipoOrganismo"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="nroCuenta", type="string", length=255, nullable=true, unique=true)
     * @Groups({"groupTipoOrganismo"})
     */
    private $nroCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupTipoOrganismo"})
     */
    private $descripcion;
    
    /**
    * @ORM\OneToMany(targetEntity="Organismo", mappedBy="tipoOrganismo")
    */
    private $organismos;


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
     * @return TipoOrganismo
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
     * @return TipoOrganismo
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
     * Set nroCuenta
     *
     * @param string $nroCuenta
     *
     * @return TipoOrganismo
     */
    public function setNroCuenta($nroCuenta)
    {
        $this->nroCuenta = $nroCuenta;

        return $this;
    }

    /**
     * Get nroCuenta
     *
     * @return string
     */
    public function getNroCuenta()
    {
        return $this->nroCuenta;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return TipoOrganismo
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->organismos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Organismo $organismo
     *
     * @return TipoOrganismo
     */
    public function addOrganismo(\Caja\SiafcaIntranetBundle\Entity\Organismo $organismo)
    {
        $this->organismos[] = $organismo;

        return $this;
    }

    /**
     * Remove organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Organismo $organismo
     */
    public function removeOrganismo(\Caja\SiafcaIntranetBundle\Entity\Organismo $organismo)
    {
        $this->organismos->removeElement($organismo);
    }

    /**
     * Get organismos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrganismos()
    {
        return $this->organismos;
    }
    
    public function __toString() {
        return $this->nombre;
    }
}
