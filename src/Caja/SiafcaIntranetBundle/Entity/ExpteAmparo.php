<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExpteAmparo
 *
 * @ORM\Table(name="expte_amparo",
 * indexes={
 *         @ORM\Index(name="idx_expte_amparo_1", columns={"organismo_id"})
 * }
 * 
 *  , uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_expte_amparo_2", columns={"numero"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ExpteAmparoRepository")
 */

class ExpteAmparo
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
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="oracledatetime")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaResolucion", type="oracledatetime")
     */
    private $fechaResolucion;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=30, unique=true)
     */
    private $numero;
    
    /**
    * @ORM\OneToMany(targetEntity="ExpteAmpItem", mappedBy="expteAmparo", cascade={"persist"})
    */
    private $amparoItems;
    
    /**
     * @var string
     *
     * @ORM\Column(name="comentarios", type="string", length=255, nullable=true)
     */
    private $comentarios;
    
    /**
    * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="expteAmparos")
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return ExpteAmparo
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaResolucion
     *
     * @param \DateTime $fechaResolucion
     *
     * @return ExpteAmparo
     */
    public function setFechaResolucion($fechaResolucion)
    {
        $this->fechaResolucion = $fechaResolucion;

        return $this;
    }

    /**
     * Get fechaResolucion
     *
     * @return \DateTime
     */
    public function getFechaResolucion()
    {
        return $this->fechaResolucion;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return ExpteAmparo
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
     * Constructor
     */
    public function __construct()
    {
        $this->amparoItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add amparoItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparoItem
     *
     * @return ExpteAmparo
     */
    public function addAmparoItem(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparoItem)
    {
        $this->amparoItems[] = $amparoItem;

        return $this;
    }

    /**
     * Remove amparoItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparoItem
     */
    public function removeAmparoItem(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparoItem)
    {
        $this->amparoItems->removeElement($amparoItem);
    }

    /**
     * Get amparoItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAmparoItems()
    {
        return $this->amparoItems;
    }

    /**
     * Set comentarios
     *
     * @param string $comentarios
     *
     * @return ExpteAmparo
     */
    public function setComentarios($comentarios)
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    /**
     * Get comentarios
     *
     * @return string
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /**
     * Set organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Organismo $organismo
     *
     * @return ExpteAmparo
     */
    public function setOrganismo(\Caja\SiafcaIntranetBundle\Entity\Organismo $organismo = null)
    {
        $this->organismo = $organismo;

        return $this;
    }

    /**
     * Get organismo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Organismo
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }
    
    /**
     * To String
     */
    public function __toString()
    {
        return $this->getNumero();
    }
}
