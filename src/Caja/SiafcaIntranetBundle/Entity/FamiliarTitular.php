<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Familiar
 *
 * @ORM\Table(name="familiar_titular"
 * , uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_familiar_titular_1", columns={"persona_id"})
 * })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\FamiliarTitularRepository")
 */

class FamiliarTitular
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
     * @ORM\Column(name="fechaCarga", type="datetime")
     */
    private $fechaCarga;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaValidacion", type="oracledatetime",  nullable=true)
     */
    private $fechaValidacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaVencimiento", type="oracledatetime",  nullable=true)
     */
    private $fechaVencimiento;
    
    /**
     * @ORM\OneToMany(targetEntity="Familiar", mappedBy="titular", cascade="persist")
    * @Assert\Type(type="Caja\SiafcaIntranetBundle\Entity\Familiar")
     * @Assert\Valid()
     */
    private $familiares;
    
    /**
     * @ORM\OneToOne(targetEntity="Persona")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id", nullable=true)
     */
    private $persona;
    
    function getFamiliares() {
        return $this->familiares;
    }

    function setFamiliares($familiares) {
        $this->familiares = $familiares;
    }

    function getId() {
        return $this->id;
    }

    function getFechaCarga() {
        return $this->fechaCarga;
    }

    function getFechaValidacion() {
        return $this->fechaValidacion;
    }

    function getFechaVencimiento() {
        return $this->fechaVencimiento;
    }

    function getFamiliar() {
        return $this->familiar;
    }

    function setFechaCarga(\DateTime $fechaCarga) {
        $this->fechaCarga = $fechaCarga;
    }

    function setFechaValidacion(\DateTime $fechaValidacion) {
        $this->fechaValidacion = $fechaValidacion;
    }

    function setFechaVencimiento(\DateTime $fechaVencimiento) {
        $this->fechaVencimiento = $fechaVencimiento;
    }

    function setFamiliar($familiar) {
        $this->familiar = $familiar;
    }

    function getPersona() {
        return $this->persona;
    }

    function setPersona($persona) {
        $this->persona = $persona;
    }

    public function toString($id){
       return $this->persona->__toString();
    }
    
}
