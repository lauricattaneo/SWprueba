<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntidadResponsable
 *
 * @ORM\Table(name="entidad_responsable")
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\EntidadResponsableRepository")
 */

class EntidadResponsable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=30, nullable=true)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoJuridico", type="string", length=20, nullable=true)
     */
    private $tipoJuridico;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=11, unique=false, nullable=true)
     */
    private $cuit;


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
     * @return EntidadResponsable
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
     * Set tipoJuridico
     *
     * @param string $tipoJuridico
     *
     * @return EntidadResponsable
     */
    public function setTipoJuridico($tipoJuridico)
    {
        $this->tipoJuridico = $tipoJuridico;

        return $this;
    }

    /**
     * Get tipoJuridico
     *
     * @return string
     */
    public function getTipoJuridico()
    {
        return $this->tipoJuridico;
    }

    /**
     * Set cuit
     *
     * @param string $cuit
     *
     * @return EntidadResponsable
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return string
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}
