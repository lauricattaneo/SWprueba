<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Caja\SiafcaIntranetBundle\Validator\Constraints\ValidaFechaNac;
use Caja\SiafcaIntranetBundle\Validator\Constraints\ValidaFechaNacValidator;

/**
 * Persona
 *
 * @ORM\Table(name="persona"
 * , indexes={
 *      @ORM\Index(name="idx_persona_1", columns={"sexo_id"}),
 *      @ORM\Index(name="idx_persona_2", columns={"estadocivil_id"}),
 *      @ORM\Index(name="idx_persona_3", columns={"tipodocumento_id"}),
 *      @ORM\Index(name="idx_persona_4", columns={"nacionalidad_id"}),
 *      @ORM\Index(name="idx_persona_5", columns={"documento"}),
  * }
 * , uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_persona_1", columns={"cuil"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\PersonaRepository")
 * @Soap\Alias("Persona")
 * @UniqueEntity(
 *     fields="cuil",
 *     message="Ya hay una persona con ese C.U.I.L. registrada en el sistema."
 * )
 */
class Persona
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupPersona1","groupPersona2"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidoPat", type="string", length=100, nullable=true)
     * @Groups({"groupPersona1","groupPersona2"})
     * 
     * @Assert\Regex(
     *     pattern="/^[A-Za-zƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèé êëìíîïðñòóôõöøùúûüýþÿ\s]*$/",
     *     message="El apellido no debe contener números u otros caracteres, sólo letras")
     */
    private $apellidoPat;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidoMat", type="string", length=100, nullable=true)
     * @Groups({"groupPersona1","groupPersona2"})
     */
    private $apellidoMat;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100, nullable=true)
     * @Groups({"groupPersona1","groupPersona2"})
     * 
     * @Assert\Regex(
     *     pattern="/^[A-Za-zƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèé êëìíîïðñòóôõöøùúûüýþÿ\s]*$/",
     *     message="El nombre no debe contener números u otros caracteres, sólo letras")
     * 
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=8, nullable=true)
     * @Groups({"groupPersona1","groupPersona2"})
     * @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      exactMessage = "El documento debe tener 8 caracteres.",
     * )
     */
    private $documento;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sexo")
     * @ORM\JoinColumn(name="sexo_id", referencedColumnName="id")
     * @Groups({"groupPersona1","groupPersona2"})
     */
    private $sexo;

    /**
     * @var string
     *
     * @ORM\Column(name="cuil", type="string", length=11, nullable=true)
     * @Groups({"groupPersona1","groupPersona2"})
     * @Assert\Length(
     *      min = 11,
     *      max = 11,
     *      exactMessage = "El C.U.I.L. debe tener 11 caracteres.",
     * )
     */
    private $cuil;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaNac", type="oracledatetime", nullable=true)
     * @Groups({"groupPersona1","groupPersona2"})
     * @Assert\NotBlank(
     *  message = "Debe seleccionar una fecha de nacimiento."
     * )
     * @ValidaFechaNac
     */
    private $fechaNac;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FECHA_FALL", type="oracledatetime", nullable=true)
     * @Groups({"groupPersona1"})
     */
    private $fecha_fall;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="discapacidad", type="string", length=1, nullable=true)
     * @Groups({"groupPersona1"})
     */
    private $discapacidad;
    
        
    /**
     * @ORM\OneToMany(targetEntity="Domicilio", mappedBy="persona",cascade="persist")
     * @Groups({"groupPersona1"})
     */
    private $domicilios;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Nacionalidad")
     * @ORM\JoinColumn(name="nacionalidad_id", referencedColumnName="id")
     * @Groups({"groupPersona1"})
     */
    private $nacionalidad;
    
    /**
     * @ORM\ManyToOne(targetEntity="TipoDocumento")
     * @ORM\JoinColumn(name="tipodocumento_id", referencedColumnName="id")
     * @Groups({"groupPersona1","groupPersona2"})
     */
    private $tipoDocumento;
    
    /**
     * @ORM\ManyToOne(targetEntity="EstadoCivil")
     * @ORM\JoinColumn(name="estadocivil_id", referencedColumnName="id")
     * @Groups({"groupPersona1"})
     */
    private $estadoCivil;
    
    /**
     * @ORM\OneToMany(targetEntity="Aportante", mappedBy="persona",cascade="persist")
     */
    private $aportantes;
    
    /**
     * @ORM\OneToMany(targetEntity="Firmante", mappedBy="persona",cascade="persist")
     */
    private $firmantes;
    
    /**
     * @ORM\OneToMany(targetEntity="Familiar", mappedBy="titular",cascade="persist")
     */
    private $familiares;
    
    /**
     * @ORM\OneToMany(targetEntity="Familiar", mappedBy="familiar")
     */
    private $familiarTitular;
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
     * Set apellidoPat
     *
     * @param string $apellidoPat
     *
     * @return Persona
     */
    public function setApellidoPat($apellidoPat)
    {
        $this->apellidoPat = $apellidoPat;

        return $this;
    }

    /**
     * Get apellidoPat
     *
     * @return string
     */
    public function getApellidoPat()
    {
        return $this->apellidoPat;
    }

    /**
     * Set apellidoMat
     *
     * @param string $apellidoMat
     *
     * @return Persona
     */
    public function setApellidoMat($apellidoMat)
    {
        $this->apellidoMat = $apellidoMat;

        return $this;
    }

    /**
     * Get apellidoMat
     *
     * @return string
     */
    public function getApellidoMat()
    {
        return $this->apellidoMat;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Persona
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
     * Set documento
     *
     * @param string $documento
     *
     * @return Persona
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set cuil
     *
     * @param string $cuil
     *
     * @return Persona
     */
    public function setCuil($cuil)
    {
        $this->cuil = $cuil;

        return $this;
    }

    /**
     * Get cuil
     *
     * @return string
     */
    public function getCuil()
    {
        return $this->cuil;
    }
    
    public function getCuilToShow()
    {
        if ($this->cuil){
            $cuil=$this->cuil;
            return $cuil2 = substr($cuil, 0, 2). "-" . substr($cuil, 2, 8). "-" .substr($cuil, 10, 1) ;
        }
        return null;
    }

    /**
     * Set fechaNac
     *
     * @param \DateTime $fechaNac
     *
     * @return Persona
     */
    public function setFechaNac($fechaNac)
    {
        $this->fechaNac = $fechaNac;

        return $this;
    }

    /**
     * Get fechaNac
     *
     * @return \DateTime
     */
    public function getFechaNac()
    {
        return $this->fechaNac;
    }
    
    /*
     * Setear el $domicilio, como único domicilio
     * actual de la persona, los demas pasan a estado
     * viejo
     * Param $domicilio type PersonaDomicilio
     */
    public function setDomicilio($domicilio)
    {
        $domicilioAEliminar = $this->getDomicilioDelTipo($domicilio->getTipoDomicilio());
        if ($domicilioAEliminar)
        {
            $this->domicilios->removeElement($domicilioAEliminar);
        }
        $this->addDomicilio($domicilio);
        return $domicilioAEliminar;
    }
    

    private function getDomicilioDelTipo($tipoDomicilio)
    {
        
        foreach ($this->domicilios as $domicilio)
        {
            if ($domicilio->getTipoDomicilio() == $tipoDomicilio)
            {
                return $domicilio;
            }
        }
        
        return null;
    }
    
    /**
     * @Groups({"groupPersona2"})
     */
    public function getDomicilio($tipoDomicilio = null)
    {
        if ($tipoDomicilio)
        {
            return $this->getDomiciliodelTipo($tipoDomicilio);
        }
        else
        {
            return $this->getDomicilioReal();
        }
    }
    
    
    private function getDomicilioReal()
    {
        foreach ($this->domicilios as $domicilio)
        {
            if ($domicilio->isDomicilioReal())
            {
                return $domicilio;
            }
        }
        
        return null; //NULL SI NO HAY UN DOMICILIO REAL, ES TEMPORAL
    }

    /**
     * Set nacionalidad
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Nacionalidad $nacionalidad
     *
     * @return Persona
     */
    public function setNacionalidad(\Caja\SiafcaIntranetBundle\Entity\Nacionalidad $nacionalidad = null)
    {
        $this->nacionalidad = $nacionalidad;

        return $this;
    }

    /**
     * Get nacionalidad
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Nacionalidad
     */
    public function getNacionalidad()
    {
        return $this->nacionalidad;
    }

    /**
     * Set tipoDocumento
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\TipoDocumento $tipoDocumento
     *
     * @return Persona
     */
    public function setTipoDocumento(\Caja\SiafcaIntranetBundle\Entity\TipoDocumento $tipoDocumento = null)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\TipoDocumento
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Add domicilio
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Domicilio $domicilio
     *
     * @return Persona
     */
    public function addDomicilio(\Caja\SiafcaIntranetBundle\Entity\Domicilio $domicilio)
    {
        $domicilio->setPersona($this);
        $this->domicilios[] = $domicilio;

        return $this;
    }

    /**
     * Remove domicilio
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Domicilio $domicilio
     */
    public function removeDomicilio(\Caja\SiafcaIntranetBundle\Entity\Domicilio $domicilio)
    {
        $this->domicilios->removeElement($domicilio);
    }

    /**
     * Get domicilios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDomicilios()
    {
        return $this->domicilios;
    }
    
    /**
     * Get nombreCompleto
     *
     * @return string
     */
    public function getNombreCompleto()
    {    if (!$this->apellidoMat and trim($this->nombre)){
        $nombreCompleto = $this->apellidoPat. ', ' .$this->nombre;
        }
        elseif ($this->apellidoMat and !trim($this->nombre)) {
        $nombreCompleto = $this->apellidoPat.' '.$this->apellidoMat;}
        
        elseif ($this->apellidoMat and trim($this->nombre)){
        $nombreCompleto = $this->apellidoPat.' '.$this->apellidoMat. ', ' .$this->nombre;}
        
        else{
            $nombreCompleto = $this->apellidoPat;
        
        }
        
        return $nombreCompleto;
    }

    /**
     * Set estadoCivil
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\EstadoCivil $estadoCivil
     *
     * @return Persona
     */
    public function setEstadoCivil(\Caja\SiafcaIntranetBundle\Entity\EstadoCivil $estadoCivil = null)
    {
        $this->estadoCivil = $estadoCivil;

        return $this;
    }

    /**
     * Get estadoCivil
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\EstadoCivil
     */
    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    /**
     * Set sexo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Sexo $sexo
     *
     * @return Persona
     */
    public function setSexo(\Caja\SiafcaIntranetBundle\Entity\Sexo $sexo = null)
    {
        $this->sexo = $sexo;

        return $this;
    }

    /**
     * Get sexo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Sexo
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Add aportante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Aportante $aportante
     *
     * @return Persona
     */
    public function addAportante(\Caja\SiafcaIntranetBundle\Entity\Aportante $aportante)
    {
        $aportante->setPersona($this);
        $this->aportantes[] = $aportante;
        
        return $this;
    }

    /**
     * Remove aportante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Aportante $aportante
     */
    public function removeAportante(\Caja\SiafcaIntranetBundle\Entity\Aportante $aportante)
    {
        $this->aportantes->removeElement($aportante);
    }

    /**
     * Get aportantes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAportantes()
    {
        return $this->aportantes;
    }
    
    public function __toString() {
        return $this->getNombreCompleto();
    }

    /**
     * Add firmante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Firmante $firmante
     *
     * @return Persona
     */
    public function addFirmante(\Caja\SiafcaIntranetBundle\Entity\Firmante $firmante)
    {
        $this->firmantes[] = $firmante;

        return $this;
    }

    /**
     * Remove firmante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Firmante $firmante
     */
    public function removeFirmante(\Caja\SiafcaIntranetBundle\Entity\Firmante $firmante)
    {
        $this->firmantes->removeElement($firmante);
    }

    /**
     * Get firmantes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFirmantes()
    {
        return $this->firmantes;
    }

    /**
     * Add familiare
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Familiar $familiare
     *
     * @return Persona
     */
    public function addFamiliare(\Caja\SiafcaIntranetBundle\Entity\Familiar $familiare)
    {
        $this->familiares[] = $familiare;

        return $this;
    }

    /**
     * Remove familiare
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Familiar $familiare
     */
    public function removeFamiliare(\Caja\SiafcaIntranetBundle\Entity\Familiar $familiare)
    {
        $this->familiares->removeElement($familiare);
    }

    /**
     * Get familiares
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFamiliares()
    {
        return $this->familiares;
    }
    
    /**
     * Get familiaresTitular
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFamiliaresTitular()
    {
        return $this->familiaresTitular;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->domicilios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aportantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->firmantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->familiares = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set discapacidad
     *
     * @param string $discapacidad
     *
     * @return Persona
     */
    public function setDiscapacidad($discapacidad)
    {
        $this->discapacidad = $discapacidad;

        return $this;
    }

    /**
     * Get discapacidad
     *
     * @return string
     */
    public function getDiscapacidad()
    {
        return $this->discapacidad;
    }
    
    public function initialize($personaArray) 
    {
        list(
            $this->nombre,
            $this->apellidoPat,
            $this->apellidoMat,
            $this->documento,
            $this->cuil,
            
        ) = array(
                $personaArray['nombre'],
                $personaArray['apellidoPat'],
                $personaArray['apellidoMat'],
                $personaArray['documento'],
                $personaArray['cuil'],
            );
        $this->setFechaNac($personaArray['fechaNac']);
    }

    /**
     * Set fechaFall
     *
     * @param oracledatetime $fechaFall
     *
     * @return Persona
     */
    public function setFechaFall($fechaFall)
    {
        $this->fecha_fall = $fechaFall;
        
        return $this;
    }

    /**
     * Get fechaFall
     *
     * @return oracledatetime
     */
    public function getFechaFall()
    {
        return $this->fecha_fall;
    }
    /**
     * Para mostrar las propiedades de valores multiples en las TWIG
     * @return array[]
     */
    public function getPropiedadesMultiplesToShow()
    {
        $properties = array();

        // Filtrado de colecciones: Se mostraran los firmantes y aportantes actuales
        // Para ver el historial completo hay una vista dedicada

        $properties['Aportante'] = array(
            'data' => 'aportantes',
            'entityName' => 'Aportante',
            'historic' => 'persona_historico_aportante');
        $properties['Domicilios'] = array(
            'data' => $this->domicilios,
            'entityName' => 'Domicilio',
            'delete' => array(
                'path' => 'domicilio_delete',
                'enabled' => true,
                'params' => array('id' => '-entityId-'),
            ),
            'add' => array(
                'enabled' => true,
            ),
            'edit' => array(
                'enabled' => true,
            ));
        $properties['Firmantes'] = array(
            'data' => 'firmantes',
            'entityName' => 'Firmante',
            'historic' => 'persona_historico_firmante',
            'edit' => array(
                'enabled' => true,
            ));

        $properties['Familiares'] = array(
            'data' => $this->familiares,
            'entityName' => 'Familiar',
            'add' => array(
                'enabled' => true,
            ),
                'delete' => array(
                'path' => 'familiar_delete_ajax',
                'enabled' => true,
                'params' => array('id' => '-entityId-'),

            ),
            'edit' => array(
                'enabled' => true,
            )
            );
        $properties['Titulares'] = array(
            'data' => $this->familiarTitular,
            'entityName' => 'T',
            'add' => array(
                'enabled' => true,
            ),
                'delete' => array(
                'path' => 'familiar_delete_ajax',
                'enabled' => true,
                'params' => array('id' => '-entityId-'),

            ),
            'edit' => array(
                'enabled' => true,
            )
            );

        return $properties;
    }

    /**
     * Para mostrar las propiedades de valores en las TWIG
     * @return array[]
     */
    public function getPropiedadesToShow()
    {
        $properties = array();
        /*if ($this->apellidoMat) {
            $properties['Apellido materno'] = $this->apellidoMat;
        }*/
        if ($this->apellidoPat) {
            $properties['Apellido'] = $this->apellidoPat;
        }
        if ($this->nombre) {
            $properties['Nombre'] = $this->nombre;
        }
        if ($this->documento) {
            if ($this->tipoDocumento){
            $properties['Documento'] = '('.$this->tipoDocumento.') '.$this->documento;}
            else {
            $properties['Documento'] = $this->documento;
            }
        }
        if ($this->sexo) {
            $properties['Sexo'] = $this->sexo;
        }
        if ($this->estadoCivil) {
            $properties['Estado civil'] = $this->estadoCivil;
        }
        if ($this->cuil) {
            $properties['Cuil'] = $this->cuil;
        }
        if ($this->fechaNac) {
            $properties['Fecha nacimiento'] = $this->fechaNac->format('d/m/Y');
        }
        if ($this->discapacidad) {
            $properties['Discapacidad'] = $this->discapacidad;
        }
        if ($this->fecha_fall) {
            $properties['Fecha fallecimiento'] = $this->fecha_fall->format('d/m/Y');
        }
        /*
        if ($this->familiarTitular[0]){
            $titulares = null;
            foreach($this->familiarTitular as $familiar){
                $titulares .= ", ";
                $titulares .= $familiar->toString($this->getId());
            }
            $properties['Titulares'] = $titulares;
        }
*/
        return $properties;
    }

    /**
     * Add familiarTitular
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Familiar $familiarTitular
     *
     * @return Persona
     */
    public function addFamiliarTitular(\Caja\SiafcaIntranetBundle\Entity\Familiar $familiarTitular)
    {
        $this->familiarTitular[] = $familiarTitular;

        return $this;
    }

    /**
     * Remove familiarTitular
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Familiar $familiarTitular
     */
    public function removeFamiliarTitular(\Caja\SiafcaIntranetBundle\Entity\Familiar $familiarTitular)
    {
        $this->familiarTitular->removeElement($familiarTitular);
    }

    /**
     * Get familiarTitular
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFamiliarTitular()
    {
        return $this->familiarTitular;
    }
}
