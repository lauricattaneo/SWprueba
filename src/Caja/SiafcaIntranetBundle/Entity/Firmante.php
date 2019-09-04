<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * Firmante
 *
 * @ORM\Table(name="firmante"
 * * , indexes={
 *      @ORM\Index(name="idx_firmante_1", columns={"organismo_id"}),
 *      @ORM\Index(name="idx_firmante_2", columns={"persona_id"}),
 *      @ORM\Index(name="idx_firmante_3", columns={"estado_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\FirmanteRepository")
 */

class Firmante
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupFirmante"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comentario", type="string", length=255, nullable=true)
     *  @Groups({"groupFirmante"})
     */
    private $comentario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaDesde", type="oracledatetime", nullable=true)
     * @Assert\NotBlank(
     *  message = "Debe ingresar fecha de inicio del firmante en el cargo."
     * )
     *  @Groups({"groupFirmante"})
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaHasta", type="oracledatetime", nullable=true)
     *  @Groups({"groupFirmante"})
     */
    private $fechaHasta;
    
    /**
     * @ORM\ManyToOne(targetEntity="Persona", inversedBy="firmantes",cascade="persist")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     * @Assert\Type(type="Caja\SiafcaIntranetBundle\Entity\Persona")
     * @Assert\Valid()
     *  @Groups({"groupFirmante"})
     */
    private $persona;
    
    /**
     * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="firmantes")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     */
    private $organismo;

    /** 
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id", nullable=false)
     * @Groups({"groupFirmante"})
     */
    private $estado;
    
    /**
     * @var $historicColumns
     * Se utiliza para rellenar de forma dinamica la única plantilla de historicos en la carpeta Partials
     */
    public static $historicColumns = array(
        array(
            'displayName' => 'organismo',
            'backEndName' => 'organismo',
            'queryPath' => 'o.nombre',
        ),
        array(
            'displayName' => 'fecha de inicio',
            'backEndName' => 'fechaDesde',
            'queryPath' => 'e.fechaDesde',
        ),
        array(
            'displayName' => 'fecha final',
            'backEndName' => 'fechaHasta',
            'queryPath' => 'e.fechaHasta',
        ),
        array(
            'displayName' => 'comentario',
            'backEndName' => 'comentario',
            'queryPath' => 'e.comentario',
        ),
    );

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
     * Set comentario
     *
     * @param string $comentario
     *
     * @return Firmante
     */
    public function setComentario($comentario)
    {
        $this->comentario = $comentario;

        return $this;
    }

    /**
     * Get comentario
     *
     * @return string
     */
    public function getComentario()
    {
        return $this->comentario;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     *
     * @return Firmante
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
     * @return Firmante
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    public function isCreado(){
        return $this->estado->isEstado('1','FIRM');
    }
    
    public function isActivo(){
         return $this->estado->isEstado('2','FIRM');
    }
    
    public function isBaja(){
        return $this->estado->isEstado('3','FIRM');
    }

    public function isRechazado(){
        return $this->estado->isEstado('4','FIRM');
    }
  
    public function activar()
    {
        if($this->getEstado()->getEstado() == '1'){
                $success1 = $this->aplicarTransicion('T01');
            }
            elseif($this->getEstado()->getEstado() == '4')
                {
                $success1 = $this->aplicarTransicion('T04');
            }
            if ($success1)
                return TRUE;            
            else
                return false;
    }
    
     public function darBaja()
    {
        if($this->getEstado()->getEstado() == '2'){
                $success1 = $this->aplicarTransicion('T02');
            }
            if ($success1)
                return TRUE;            
            else
                return false;
    }
    
    public function rechazar()
    {
        if($this->getEstado()->getEstado() == '1'){
                $success1 = $this->aplicarTransicion('T03');
            }
           
            if ($success1)
                return TRUE;            
            else
                return false;
    }
    
    
    public function puedeActivarse()
    {
        return (!$this->isActivo() && !$this->isBaja() && ($this->isCreado() || $this->isRechazado()));
    }
    
    
    
    /**
     * Get fechaHasta
     *
     * @return \DateTime
     */
    public function getFechaHasta()
    {
        if($this->fechaHasta)
            return $this->fechaHasta;
        else return "-";
    }

    /**
     * Set persona
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Persona $persona
     *
     * @return Firmante
     */
    public function setPersona(\Caja\SiafcaIntranetBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Persona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Organismo $organismo
     *
     * @return Firmante
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
    
    public function actual()
    {
        $now = new DateTime('now');
        if ($this->fechaHasta && $now > $this->fechaHasta){
            return false;
        }
        return true;
    }
    
    public function __toString() {
        return $this->persona->__toString().' / '.$this->organismo->__toString().' / ( '.$this->fechaDesde->format('d/m/Y').((isset($this->fechaHasta))? ' - '.$this->fechaHasta->format('d/m/Y') : '').' )';
    }

    /**
     * Completa la propiedad fechaHasta con el valor de la fecha actual,
     * en caso que este vacia o la fecha existente sea mayor a la actual.
     */
    public function inhabilitar()
    {
        $now = new DateTime('now');
        if (!$this->fechaHasta || $this->fechaHasta > $now){
            $this->fechaHasta = $now;
        }
    }

    public function getHistoricColumns()
    {
        return self::$historicColumns;
    }
    
    /**
     * Set estado
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Estado $estado
     *
     * @return Firmante
     */
    public function setEstado(\Caja\SiafcaIntranetBundle\Entity\Estado $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Estado
     */
    public function getEstado()
    {
        return $this->estado;
    }
    
    private function aplicarTransicion($codigoTransicion)
    {
        $proximoEstado = $this->estado->proximoEstado($codigoTransicion, 'FIRM');
        if ($proximoEstado){
            $this->setEstado($proximoEstado);
            return true;
        }

        return false;
    }
    
     /**
     *  Función para utilizar en las Twig, para evitar los if
     *      dentro de las mismas.
     * @return string
     */
    public function getCuilShow()
    {
        if ($this->persona->getCuil()) {
            $cuil = $this->persona->getCuil();
            $cuil2 = substr($cuil, 0, 2). "-" . substr($cuil, 2, 8). "-" .substr($cuil, 10, 1) ;
            
            return $cuil2;
        } else {
             return null;
         }
            
    }
    
       /**
     * Para mostrar las propiedades de valores en las TWIG
     * @return array[]
     */
    public function getPropiedadesToShow()
    {
        $properties = array();
      
        if ($this->id) {
            $properties['Id'] = $this->id;
        }
        if ($this->estado) {
            $properties['Estado'] = $this->estado;
        }
        if ($this->persona->getapellidoPat()) {
            $properties['Apellido'] = $this->persona->getapellidoPat();
        }
        if ($this->persona->getnombre()) {
            $properties['Nombre'] = $this->persona->getnombre();
        }
        if ($this->persona->getdocumento()) {
            if ($this->persona->gettipoDocumento()){
            $properties['Documento'] = '('.$this->persona->gettipoDocumento().') '.$this->persona->getdocumento();}
            else {
            $properties['Documento'] = $this->persona->getdocumento();
            }
        }
        if ($this->persona->getsexo()) {
            $properties['Sexo'] = $this->persona->getsexo();
        }
        if ($this->persona->getestadoCivil()) {
            $properties['Estado civil'] = $this->persona->getestadoCivil();
        }
        if ($this->persona->getcuil()) {
            $properties['Cuil'] = $this->getCuilShow();
        }
        if ($this->persona->getfechaNac()) {
            $properties['Fecha nacimiento'] = $this->persona->getfechaNac()->format('d/m/Y');
        }
        if ($this->fechaDesde) {
            $properties['Firmante Desde'] = $this->fechaDesde->format('d/m/Y');
        }
        if ($this->persona->getdiscapacidad()) {
            $properties['Discapacidad'] = $this->persona->getdiscapacidad();
        }
        if ($this->persona->getfechaFall()) {
            $properties['Fecha fallecimiento'] = $this->persona->getfechaFall()->format('d/m/Y');
        }

        return $properties;
    }
    
}
