<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * ConjuntoControl
 *
 * @ORM\Table(name="conjuntocontrol",
 * indexes={
 *   @ORM\Index(name="idx_conjuntocontrol_1", columns={"revisado_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ConjuntoControlRepository")
 */

class ConjuntoControl
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupConjuntoControl"})
     */
    private $id;
    
    /**
     * @ORM\OneToOne(targetEntity="Liquidacion", inversedBy="conjuntoControl")
     * @ORM\JoinColumn(name="liquidacion_id", referencedColumnName="id", nullable=false)
     */
    private $liquidacion;
    
    /**
    * @ORM\OneToMany(targetEntity="Control", mappedBy="conjuntoControl")
    * @Groups({"groupConjuntoControl"})
    */
    private $controles;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="revisado_id", referencedColumnName="id")
     * @Groups({"groupConjuntoControl"})
     */
    private $revisado;


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
     * Set liquidacion
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion
     *
     * @return ConjuntoControl
     */
    public function setLiquidacion(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion)
    {
        $this->liquidacion = $liquidacion;

        return $this;
    }

    /**
     * Get liquidacion
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function getLiquidacion()
    {
        return $this->liquidacion;
    }

    /**
     * Add controle
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Control $controle
     *
     * @return ConjuntoControl
     */
    public function addControle(\Caja\SiafcaIntranetBundle\Entity\Control $controle)
    {
        $this->controles[] = $controle;

        return $this;
    }

    /**
     * Remove controle
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Control $controle
     */
    public function removeControle(\Caja\SiafcaIntranetBundle\Entity\Control $controle)
    {
        $this->controles->removeElement($controle);
    }

    /**
     * Get controles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getControles()
    {
        return $this->controles;
    }

    /**
     * Set revisado
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $revisado
     *
     * @return ConjuntoControl
     */
    public function setRevisado(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $revisado = null)
    {
        $this->revisado = $revisado;

        return $this;
    }

    /**
     * Get revisado
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getRevisado()
    {
        return $this->revisado;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->controles = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
