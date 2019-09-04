<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Control
 *
 * @ORM\Table(name="control",
 * indexes={
 *   @ORM\Index(name="idx_control_2", columns={"control_id"}),
 *   @ORM\Index(name="idx_control_1", columns={"c_item_id"}),
 *   @ORM\Index(name="idx_control_3", columns={"c_control_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ControlRepository")
 */

class Control
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupControl"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupControl"})
     */
    private $descripcion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="prioridad", type="string", length=1, nullable=true)
     * @Groups({"groupControl"})
     */
    private $prioridad;
    
    /**
     * @ORM\ManyToOne(targetEntity="ConjuntoControl", inversedBy="controles")
     * @ORM\JoinColumn(name="c_control_id", referencedColumnName="id")
     */
    private $conjuntoControl;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="control_id", referencedColumnName="id")
     * @Groups({"groupControl"})
     */
    private $control;
    
    /**
     * @ORM\ManyToOne(targetEntity="ConceptoItem", inversedBy="controles")
     * @ORM\JoinColumn(name="c_item_id", referencedColumnName="id")
     * @Groups({"groupControl"})
     */
    private $conceptoItem;


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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Control
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
     * Set control
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $control
     *
     * @return Control
     */
    public function setControl(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $control = null)
    {
        $this->control = $control;

        return $this;
    }

    /**
     * Get control
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Set conceptoItem
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptoItem
     *
     * @return Control
     */
    public function setConceptoItem(\Caja\SiafcaIntranetBundle\Entity\ConceptoItem $conceptoItem = null)
    {
        $this->conceptoItem = $conceptoItem;

        return $this;
    }

    /**
     * Get conceptoItem
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\ConceptoItem
     */
    public function getConceptoItem()
    {
        return $this->conceptoItem;
    }

    /**
     * Set conjuntoControl
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ConjuntoControl $conjuntoControl
     *
     * @return Control
     */
    public function setConjuntoControl(\Caja\SiafcaIntranetBundle\Entity\ConjuntoControl $conjuntoControl = null)
    {
        $this->conjuntoControl = $conjuntoControl;

        return $this;
    }

    /**
     * Get conjuntoControl
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\ConjuntoControl
     */
    public function getConjuntoControl()
    {
        return $this->conjuntoControl;
    }

    /**
     * Set prioridad
     *
     * @param string $prioridad
     *
     * @return Control
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return string
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }
}
