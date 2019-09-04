<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Symfony\Component\Serializer\Annotation\Groups;
use DateInterval;
use DateTime;



/**
 * UsuarioOrganismo
 *
 * @ORM\Table(name="usuario_organismo"
 * , indexes={
 *      @ORM\Index(name="idx_usuario_organismo_1", columns={"organismo_id"}),
 *      @ORM\Index(name="idx_usuario_organismo_2", columns={"rol_id"}),
 *      @ORM\Index(name="idx_usuario_organismo_3", columns={"estado_id"}),
 *      @ORM\Index(name="idx_usuario_organismo_4", columns={"usuario_id"}),
 * }
 * , uniqueConstraints={
 *      @ORM\UniqueConstraint(name="uk_usuario_organismo_1", columns={"usuario_id", "organismo_id", "fechaDesde", "fechaHasta"})
 *  })
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\UsuarioOrganismoRepository")
 */

class UsuarioOrganismo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Soap\ComplexType("int", nillable=true)
     * @Groups({"groupUsuarioOrganismo"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaDesde", type="oracledatetime", nullable=true)
     * @Soap\ComplexType("dateTime", nillable=true)
     * @Groups({"groupUsuarioOrganismo"})
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaHasta", type="oracledatetime", nullable=true)
     * @Soap\ComplexType("dateTime", nillable=true)
     */
    private $fechaHasta;

    /**
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     */
    private $estado;
    
    /**
     * @var string
     *
     * @ORM\Column(name="correo", type="string", length=255, nullable=true)
     * @Soap\ComplexType("string", nillable=true)
     * @Groups({"groupUsuarioOrganismo"})
     */
    private $correo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Rol", inversedBy="usuarioOrganismo", fetch="EAGER")
     * @ORM\JoinColumn(name="rol_id", referencedColumnName="id")
     * @Soap\ComplexType("Caja\SiafcaIntranetBundle\Entity\Rol")
     */
    private $rol;
    
    /**
     * @ORM\ManyToOne(targetEntity="Oficina", inversedBy="usuarioOrganismos", fetch="EAGER")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     */
    private $organismo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="usuarioOrganismos")
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     */
    private $usuario;

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
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     *
     * @return UsuarioOrganismo
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     *
     * @return UsuarioOrganismo
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;
        
        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set estado
     *
     * @param integer $estado
     *
     * @return UsuarioOrganismo
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return int
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set rol
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Rol $rol
     *
     * @return UsuarioOrganismo
     */
    public function setRol(\Caja\SiafcaIntranetBundle\Entity\Rol $rol = null)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Rol
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * Set organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Oficina $organismo
     *
     * @return UsuarioOrganismo
     */
    public function setOrganismo(\Caja\SiafcaIntranetBundle\Entity\Oficina $organismo = null)
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
     * Set usuarios
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Usuario $usuario
     *
     * @return UsuarioOrganismo
     */
    public function setUsuario(\Caja\SiafcaIntranetBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuarios
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set correo
     *
     * @param string $correo
     *
     * @return UsuarioOrganismo
     */
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }

    /**
     * Get correo
     *
     * @return string
     */
    public function getCorreo()
    {
        return $this->correo;
    }
    
    public function isValid()
    {
        return $this->estado->isValid();
    }
    
    public function isOrganismoAdmin()
    {
        return $this->rol->getRol() == 'ROLE_ORGANISMO_ADMIN';
    }

    /**
     * Para mostrar las propiedades en las TWIG
     * @return array[]
     */
    public function getPropiedadesToShow()
    {
        if ($this->rol) {
            $properties['Rol'] = $this->rol;
        }
        if ($this->fechaDesde) {
            $properties['Fecha de Inicio'] = $this->fechaDesde->format('d/m/Y');
        }
        if ($this->fechaHasta) {
            $properties['Fecha de Finalización'] = $this->fechaHasta->format('d/m/Y');
        }
        if ($this->estado) {
            $properties['Estado'] = $this->estado;
        }
        if ($this->correo) {
            $properties['Email'] = $this->correo;
        }

        return $properties;
    }

    public function isHabilitado(){

        $fechaActual = new DateTime();
        if($this->getFechaDesde()<=$fechaActual && $this->getFechaHasta()>=$fechaActual){
            return $this->getEstado()->getEstado();
        }else return "no habilitado";
        /*elseif($this->getFechaDesde()<$fechaActual)
            return "5";
        elseif($this->getFechaHasta()>$fechaActual)
            return "6";
        else return "-1";
         */

    }
    
    public function __toString() {
            return $this->usuario->__toString(). 
                " (" . (($this->fechaDesde) ? $this->fechaDesde->format('d/m/y') : " ") . 
                " - " . (($this->fechaHasta) ? $this->fechaHasta->format('d/m/y') : "Actualidad") . 
                ") - " . $this->getRol()->__toString() .
                " - " . $this->getEstado()->__toString() .
                " - " . $this->getCorreo();
        }
        
        public function validar() {
        $success = $this->aplicarTransicion('T01');
        return $success;
    }

    public function bloquear() {
        $success = $this->aplicarTransicion('T02');
        return $success;
    }

    public function desbloquear() {
        $success = $this->aplicarTransicion('T03');
        return $success;
    }

    public function inhabilitarHab() {
        $success = $this->aplicarTransicion('T04');
        return $success;
    }

    public function inhabilitarBloq() {
        $success = $this->aplicarTransicion('T05');
        return $success;
    }

    public function aplicarTransicion($codigoTransicion) {
        $proximoEstado = $this->estado->proximoEstado($codigoTransicion, 'USR');
        if ($proximoEstado) {
            $this->setEstado($proximoEstado);
            return true;
        }
        return false;
    }

}
