<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;

/**
 * Rol
 *
 * @ORM\Table(name="rol"
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_rol_1", columns={"nombre"}),
 * }) 
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\RolRepository")
 */

class Rol
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Soap\ComplexType("int", nillable=true)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, unique=false)
     * @Soap\ComplexType("string")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="rol", type="integer", type="string", length=100)
     * @Soap\ComplexType("string")
     */
    private $rol;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="integer", type="string", length=255, unique=true)
     * @Soap\ComplexType("string")
     */
    private $nombre;
    
    /**
     * @ORM\OneToMany(targetEntity="UsuarioOrganismo", mappedBy="rol")
     */
    
    private $usuarioOrganismo;

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
     * Set description
     *
     * @param string $description
     *
     * @return Rol
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Set rol
     *
     * @param string $rol
     *
     * @return Rol
     */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return string
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Add usuarioOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo
     *
     * @return Rol
     */
    public function addUsuarioOrganismo(\Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo)
    {
        $this->usuarioOrganismo[] = $usuarioOrganismo;

        return $this;
    }

    /**
     * Remove usuarioOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo
     */
    public function removeUsuarioOrganismo(\Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo)
    {
        $this->usuarioOrganismo->removeElement($usuarioOrganismo);
    }

    /**
     * Get usuarioOrganismo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarioOrganismo()
    {
        return $this->usuarioOrganismo;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Rol
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
        $this->usuarioOrganismo = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString() {
        return $this->nombre;
    }

}
