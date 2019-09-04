<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use Caja\SiafcaIntranetBundle\Entity\Firmante;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Organismo
 *
 * @ORM\Table(
 *     name="organismo",
 *     indexes={
 *         @ORM\Index(name="idx_organismo_2", columns={"circuns_id"}),
 *         @ORM\Index(name="idx_organismo_3", columns={"juri_id"}),
 *         @ORM\Index(name="idx_organismo_4", columns={"tipoorganismo_id"}),
 *         @ORM\Index(name="idx_organismo_5", columns={"ta00_id"}),
 *         @ORM\Index(name="idx_organismo_6", columns={"ta02_id"}),
 *         @ORM\Index(name="idx_organismo_7", columns={"ta05_id"}),
 *         @ORM\Index(name="idx_organismo_9", columns={"estado_id"}),
 *         @ORM\Index(name="idx_organismo_10", columns={"entidadResponsable_id"}) },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_organismo_1", columns={"codigo"}),
 *         @ORM\UniqueConstraint(name="uk_organismo_2", columns={"expediente"}),
 *         @ORM\UniqueConstraint(name="uk_organismo_3", columns={"resolucion"}) }
 * )
 * 
 * @UniqueEntity(
 *     fields="expediente",
 *     message="El expediente ya está registrado para otro Organismo."
 * )
 * @UniqueEntity(
 *     fields="resolucion",
 *     message="La resolución ya está registrado para otro Organismo."
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\OrganismoRepository")
 */

class Organismo extends Oficina
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaAlta", type="oracledatetime", nullable=true)
     * @Groups({"groupOrganismo"})
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaBaja", type="oracledatetime", nullable=true)
     * @Groups({"groupOrganismo"})
     */
    private $fechaBaja;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaAprobacion", type="oracledatetime", nullable=true)
     * @Groups({"groupOrganismo"})
     * @Assert\NotBlank(
     *      message="Por favor, ingrese la fecha de aprobación del Organismo."
     * )
     */
    private $fechaAprobacion;

    /**
     * @ORM\ManyToOne(targetEntity="Estado", fetch="EAGER")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     * @Groups({"groupOrganismo"})
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=10, nullable=true)
     * @Groups({"groupOrganismo"})
     */
    private $codigo;

    /**
     * @var \Decimal
     *
     * @ORM\Column(name="porcentajeSubsidio", type="decimal", precision=5, scale=2, nullable=true)
     * @Groups({"groupOrganismo"})
     */
    private $porcentajeSubsidio;

    /**
     * @var string
     *
     * @ORM\Column(name="expediente", type="string", length=50, unique=true, nullable=true)
     * @Groups({"groupOrganismo"})
     * @Assert\NotBlank(
     *      message="Por favor, ingrese el expediente que figura en el formulario."
     * )
     * @Assert\Regex(
     *     pattern="/^[0-9A-Za-z]*$/",
     *     message="El expediente no debe contener signos u otros caracteres, sólo letras y numeros")
   
     */
    private $expediente;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=4, unique=false, nullable=true)
     * @Groups({"groupOrganismo"})
     * @Assert\NotBlank(groups={"EscPriv"},
     *      message="Debe ingresar el número de escuela (hasta 4 dígitos).")
     * @Assert\Length(groups={"EscPriv"},
     *      min = "1",
     *      max = "4",
     *      minMessage = "El número de escuela por lo menos debe tener {{ limit }} caracteres de largo",
     *      maxMessage = "El número de escuela no puede tener más de {{ limit }} caracteres de largo"
     * )
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="resolucion", type="string", length=50, unique=true, nullable=true)
     * @Groups({"groupOrganismo"})
     * @Assert\NotBlank(
     *      message="Por favor, ingrese la resolución que figura en el formulario."
     * )
     */
    private $resolucion;

    /**
     * @ORM\OneToMany(targetEntity="Cargo", mappedBy="organismo",cascade="persist")
     * @ORM\OrderBy({"nombre" = "ASC"})
     */
    private $cargos;

    /**
     * @ORM\OneToMany(targetEntity="Aportante", mappedBy="organismo",cascade="persist")
     */
    private $aportantes;

    /**
     * @ORM\OneToMany(targetEntity="Firmante", mappedBy="organismo",cascade="persist")
     */
    private $firmantes;

//    /**
//     * @ORM\ManyToOne(targetEntity="Parametrizacion")
//     * @ORM\JoinColumn(name="uorg_id", referencedColumnName="id")
//     * @Assert\NotBlank(groups={"Descentralizado", "AdmCentral"},
//     *      message="Debe seleccionar la Unidad de Organización.")
//     */
//    private $uorg;

    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="circuns_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"EscPriv", "adherido"},
     *      message="Debe seleccionar la Circunscripción.")
     */
    private $circuns;

    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="juri_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"Descentralizado", "AdmCentral"},
     *      message="Debe seleccionar la Jurisdicción.")
     */
    private $juri;


    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="ta00_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"EscPriv"},
     *      message="Debe seleccionar el tipo de escuela.")
     */
    private $ta00;

    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="ta02_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"MunCom"},
     *      message="Debe seleccionar el Tipo de Dependencia.")
     */
    private $ta02;

    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="ta05_id", referencedColumnName="id")
     * @Assert\NotBlank(groups={"adherido"},
     *      message="Debe seleccionar el Tipo de Establecimiento.")
     */
    private $ta05;

    /**
     * @ORM\ManyToOne(targetEntity="EntidadResponsable")
     * @ORM\JoinColumn(name="entidadResponsable_id", referencedColumnName="id")
     */
    private $entidadResponsable;

    /**
     * @ORM\OneToMany(targetEntity="Liquidacion", mappedBy="organismo",cascade="persist")
     */
    private $liquidaciones;

    /**
     * @ORM\ManyToOne(targetEntity="TipoOrganismo", inversedBy="organismos")
     * @ORM\JoinColumn(name="tipoorganismo_id", referencedColumnName="id")
     * @Groups({"groupOrganismo"})
     */
    private $tipoOrganismo;

    /**
     * @ORM\OneToMany(targetEntity="ExpteAmparo", mappedBy="organismo", cascade="persist")
     */
    private $expteAmparos;

    
    protected $discr = 'organismo';
    
    public function getDiscr(){
        return $this->discr;
    }
    /**
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     *
     * @return Organismo
     */
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get fechaAlta
     *
     * @return \DateTime
     */
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     *
     * @return Organismo
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;
        
        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set estado
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Estado $estado
     *
     * @return Organismo
     */
    public function setEstado($estado)
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

    /**
     * Set porcentajeSubsidio
     *
     * @param float $porcentajeSubsidio
     *
     * @return Organismo
     */
    public function setPorcentajeSubsidio($porcentajeSubsidio)
    {
        $this->porcentajeSubsidio = $porcentajeSubsidio;

        return $this;
    }

    /**
     * Get porcentajeSubsidio
     *
     * @return decimal
     */
    public function getPorcentajeSubsidio()
    {
        return $this->porcentajeSubsidio;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Organismo
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

    public function  isValid()
    {
        return $this->estado->isValid();
    }

    /**
     * Add cargo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Cargo $cargo
     *
     * @return Organismo
     */
    public function addCargo(\Caja\SiafcaIntranetBundle\Entity\Cargo $cargo)
    {
        $this->cargos[] = $cargo;

        return $this;
    }

    /**
     * Remove cargo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Cargo $cargo
     */
    public function removeCargo(\Caja\SiafcaIntranetBundle\Entity\Cargo $cargo)
    {
        $this->cargos->removeElement($cargo);
    }

    /**
     * Get cargos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCargos()
    {
        return $this->cargos;
    }


    /**
     * Add aportante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Aportante $aportante
     *
     * @return Organismo
     */
    public function addAportante(\Caja\SiafcaIntranetBundle\Entity\Aportante $aportante)
    {
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

    /**
     * Add firmante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Firmante $firmante
     *
     * @return Organismo
     */
    public function addFirmante(\Caja\SiafcaIntranetBundle\Entity\Firmante $firmante)
    {
        $firmante->setOrganismo($this);
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
     * Set uorg
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $uorg
     *
     * @return Organismo
     */
//    public function setUorg(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $uorg = null)
//    {
//        $this->uorg = $uorg;
//
//        return $this;
//    }

    /**
     * Get uorg
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
//    public function getUorg()
//    {
//        return $this->uorg;
//    }

    /**
     * Set circuns
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $circuns
     *
     * @return Organismo
     */
    public function setCircuns(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $circuns = null)
    {
        $this->circuns = $circuns;

        return $this;
    }

    /**
     * Get circuns
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getCircuns()
    {
        return $this->circuns;
    }

    /**
     * Set juri
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $juri
     *
     * @return Organismo
     */
    public function setJuri(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $juri = null)
    {
        $this->juri = $juri;

        return $this;
    }

    /**
     * Get juri
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getJuri()
    {
        return $this->juri;
    }

    /**
     * Set ta00
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $ta00
     *
     * @return Organismo
     */
    public function setTa00(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $ta00 = null)
    {
        $this->ta00 = $ta00;

        return $this;
    }

    /**
     * Get ta00
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getTa00()
    {
        return $this->ta00;
    }

    /**
     * Set ta02
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $ta02
     *
     * @return Organismo
     */
    public function setTa02(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $ta02 = null)
    {
        $this->ta02 = $ta02;

        return $this;
    }

    /**
     * Get ta02
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getTa02()
    {
        return $this->ta02;
    }

    /**
     * Set ta05
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $ta05
     *
     * @return Organismo
     */
    public function setTa05(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $ta05 = null)
    {
        $this->ta05 = $ta05;

        return $this;
    }

    /**
     * Get ta05
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getTa05()
    {
        return $this->ta05;
    }

    /**
     * Set fechaAprobacion
     *
     * @param oracledatetime $fechaAprobacion
     *
     * @return Organismo
     */
    public function setFechaAprobacion($fechaAprobacion)
    {
        $this->fechaAprobacion = $fechaAprobacion;
        
        return $this;
    }

    /**
     * Get fechaAprobacion
     *
     * @return oracledatetime
     */
    public function getFechaAprobacion()
    {
        return $this->fechaAprobacion;
    }

    /**
     * Set expediente
     *
     * @param string $expediente
     *
     * @return Organismo
     */
    public function setExpediente($expediente)
    {
        $this->expediente = $expediente;

        return $this;
    }

    /**
     * Get expediente
     *
     * @return string
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Organismo
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
     * Set resolucion
     *
     * @param string $resolucion
     *
     * @return Organismo
     */
    public function setResolucion($resolucion)
    {
        $this->resolucion = $resolucion;

        return $this;
    }

    /**
     * Get resolucion
     *
     * @return string
     */
    public function getResolucion()
    {
        return $this->resolucion;
    }

    public function generarCodigo($nextValue)
    {
        switch ($this->tipoOrganismo->getCodigo())
        {
            case '0':
                $codigoGenerado = $this->codigoEscuelaPrivada();
                break;
            case '1':
                $codigoGenerado = $this->codigoAdministracionCentral($nextValue);
                break;
            case '2':
                $codigoGenerado = $this->codigoMunicipalidadComuna($nextValue);
                break;
            case '4':
                $codigoGenerado = $this->codigoOrganismoDescentralizado($nextValue);
                break;
            case '5':
                $codigoGenerado = $this->codigoOrganismoAdherido($nextValue);
                break;
        }
        $this->codigo = $codigoGenerado;
    }

    private function codigoEscuelaPrivada()
    {
        $c = $this->circuns->getCodigo();
        $t = $this->ta00->getCodigo();
        $nnnn = $this->numero;
        return '0'.$c.$t.$nnnn.'000';
    }

    private function codigoAdministracionCentral($autoincremental)
    {
        $jj = $this->juri->getCodigo();
//        $uuuuuuu = $this->uorg->getCodigo();
//        return '1'.$jj.$uuuuuuu;
        return '1'.$jj.$autoincremental.'00';
    }

    private function codigoMunicipalidadComuna($autoincremental)
    {
        $dd = $this->getDomicilio()->getLocalidad()->getDepartamento()->getCodigo();
        $tt = $this->ta02->getCodigo();
        return '2'.$dd.$autoincremental.$tt.'00';
    }

    private function codigoOrganismoDescentralizado($autoincremental)
    {
        $jj = $this->juri->getCodigo();
//        $uuuuuuu = $this->uorg->getCodigo();
//        return '4'.$jj.$uuuuuuu;
        return '4'.$jj.$autoincremental.'00';
    }

    private function codigoOrganismoAdherido($autoincremental)
    {
        $t = $this->ta05->getCodigo();
        $c = $this->circuns->getCodigo();
        return '5'.$t.$autoincremental.$c.'00';
    }

    private function datosBasicosCompletos()
    {
        if (!$this->cuit) {
            return false;
        } elseif (!$this->expediente){
            return false;
        } elseif (!$this->fechaAprobacion){
            return false;
        } elseif (!$this->resolucion){
            return false;
        } elseif (!$this->telefono) {
            return false;
        } elseif (!$this->tipoOrganismo){
            return false;
        } elseif(!$this->getFirmante()){
            return false;
        }else{
            return true;
        }
    }
    private function datosEscuelaPrivadaCompletos()
    {
        if (!$this->ta00){
            return false;
        } elseif (!$this->numero){
            return false;
        } elseif (!$this->circuns){
            return false;
        }
//        elseif (!$this->entidadResponsable){
//            return false;
//        }
        else{
            return true;
        }
    }

    private function datosAdministracionCentralCompletos()
    {
        if (!$this->juri){
            return false;
//        } elseif (!$this->uorg){
//            return false;
        } else{
            return true;
        }
    }

    private function datosMunicipalidadComunaCompletos()
    {
        if (!$this->ta02){
            return false;
        } else{
            return true;
        }
    }

    private function datosAdheridosCompletos()
    {
        if (!$this->ta05){
            return false;
        } elseif (!$this->circuns) {
            return false;
        } else {
            return true;
        }
    }

    private function datosTipoOrganismoCompletos()
    {
        $tipoOrganismo = $this->tipoOrganismo? $this->tipoOrganismo->getCodigo() : substr($this->codigo, 0,1);
        switch ($tipoOrganismo)
        {
            case '0':
                return $this->datosEscuelaPrivadaCompletos();
            case '1':
                return $this->datosAdministracionCentralCompletos();
            case '2':
                return $this->datosMunicipalidadComunaCompletos();
            case '4':
                return $this->datosAdministracionCentralCompletos();
            case '5':
                return $this->datosAdheridosCompletos();
        }
    }


    public function datosCompletos()
    {
        if (!$this->datosBasicosCompletos()){
            return false;
        } elseif (!$this->datosTipoOrganismoCompletos()){
            return false;
        } else{
            return true;
        }
    }

    public function organismoRecienCreado($estado)
    {
        $fechaAprobacion = $this->getFechaAprobacion();
        $this->setFechaAlta(new DateTime('now'));
        $this->setEstado($estado);
    }

    public function getDatosFaltantes()
    {
        $datosFaltantes = array();
        if (!$this->nombre) { array_push($datosFaltantes, 'Nombre'); }
        if(!$this->cuit) { array_push($datosFaltantes, 'Cuit'); }
        if(!$this->getUsuarioResponsable()) { array_push($datosFaltantes, 'Usuario Responsable'); }
        if(!$this->getFirmante()) { array_push($datosFaltantes, 'Firmante'); }
        if (!$this->expediente){ array_push($datosFaltantes, 'Expediente'); }
        if (!$this->fechaAprobacion){ array_push($datosFaltantes, 'Fecha de Aprobación'); }
        if (!$this->resolucion){ array_push($datosFaltantes, 'Resolución'); }
        if (!$this->telefono) { array_push($datosFaltantes, 'Teléfono'); }
        if (!$this->tipoOrganismo){ array_push($datosFaltantes, 'Tipo de Organismo'); }
        //$todosLosDatosFaltantes = array_merge($datosFaltantes,  $this->getDatosFaltantesSegunTipo());
        return $datosFaltantes;
    }

    /**
     * Set entidadResponsable
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\EntidadResponsable $entidadResponsable
     *
     * @return Organismo
     */
    public function setEntidadResponsable(\Caja\SiafcaIntranetBundle\Entity\EntidadResponsable $entidadResponsable = null)
    {
        $this->entidadResponsable = $entidadResponsable;

        return $this;
    }

    /**
     * Get entidadResponsable
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\EntidadResponsable
     */
    public function getEntidadResponsable()
    {
        return $this->entidadResponsable;
    }

    public function hasCodigo()
    {
        if ($this->codigo){
            return true;
        } else{
            return false;
        }
    }

    private function aplicarTransicion($codigoTransicion)
    {
        $proximoEstado = $this->estado->proximoEstado($codigoTransicion, 'ORG');
        if ($proximoEstado){
            $this->setEstado($proximoEstado);
            return true;
        }

        return false;
    }

    public function isHabilitado()
    {
        return $this->estado->isEstado('1','ORG');
    }

    /*returns:
     * 1: habilitado sin firmante
     * 2: habilitado
     * 3: no se pudo habilitar
     */

    public function habilitar()
    {
        if($this->getFirmante()){
            if($this->getEstado()->getEstado() == '4'){
                $success1 = $this->aplicarTransicion('T53');
            }elseif($this->getEstado()->getEstado() == '2'){
                $success1 = $this->aplicarTransicion('T13');
            }
            if (($success1 ))
                return 1;            
            else
                return 3;
       }
//        else{
//            if($this->getEstado()->getEstado() == '0')
//                $success = $this->aplicarTransicion('T01');
//            else if($this->getEstado()->getEstado() == '2')
//                $success = $this->aplicarTransicion('T13');
//            \Symfony\Component\VarDumper\VarDumper::dump($success);
//            if($success)
//                return 2;
//            else
//                return 3;
//        }
    }

    public function getFirmante()
    {
        foreach ($this->firmantes as $firmante)
        {
            if ($firmante->actual()){ return $firmante; }
        }

        return null;
    }

    public function tieneDatosNecesarios()
    {
        return ($this->datosCompletos() && $this->getDomicilio() && $this->getFirmante() && $this->getUsuarioResponsable());
    }

    public function getDatosNecesariosFaltantes()
    {
        $datosFaltantes = array();
        if (!$this->getDomicilio()) { array_push($datosFaltantes, 'Domicilio del Organismo'); }
        //if (!$this->getFirmante()) { array_push($datosFaltantes, 'Firmante del Organismo'); }
        $todosLosDatosFaltantes = array_merge($datosFaltantes, $this->getDatosFaltantesSegunTipo());
        $todosLosDatosFaltantes2 = array_merge($todosLosDatosFaltantes, $this->getDatosFaltantes());
        return $todosLosDatosFaltantes2  ;
    }

    private function inhabilitarFirmantes()
    {
        foreach ($this->firmantes as $firmante)
        {
            $firmante->inhabilitar();
        }
    }

    public function setFirmante(Firmante $firmante)
    {
        $this->inhabilitarFirmantes();
        $this->addFirmante($firmante);
        return $this;
    }

    private function getDatosFaltantesSegunTipo()
    {
        $tipoOrganismo = $this->tipoOrganismo? $this->tipoOrganismo->getCodigo() : substr($this->codigo, 0,1);
        switch ($tipoOrganismo)
        {
            case '0':
                return $this->getDatosEscuelaPrivadaFaltantes();
            case '1':
                return $this->getDatosAdministracionCentralFaltantes();
            case '2':
                return $this->getDatosMunicipalidadComunaFaltantes();
            case '4':
                return $this->getDatosAdministracionCentralFaltantes();
            case '5':
                return $this->getDatosAdheridosFaltantes();
        }
    }

    private function getDatosEscuelaPrivadaFaltantes()
    {
        $datosFaltantes = array();
        if (!$this->ta00){
            array_push($datosFaltantes,'Tipo De Establecimiento');
        } if (!$this->numero){
            array_push($datosFaltantes,'Número de Escuela');
        } if (!$this->circuns){
            array_push($datosFaltantes,'Circunscripción');
        }
//        if (!$this->entidadResponsable){
//            array_push($datosFaltantes,'Entidad Responsable');
//        }
        return $datosFaltantes;
    }

    private function getDatosAdministracionCentralFaltantes()
    {
        $datosFaltantes = array();
        if (!$this->juri){
            array_push($datosFaltantes,'Jurisdicción');
        } 
//        if (!$this->uorg){
//            array_push($datosFaltantes,'Unidad de Organización');
//        }
        return $datosFaltantes;
    }

    private function getDatosMunicipalidadComunaFaltantes()
    {
        $datosFaltantes = array();
        if (!$this->ta02){
            array_push($datosFaltantes,'Tipo (Municipalidad o Comuna)');
        }
        return $datosFaltantes;
    }

    private function getDatosAdheridosFaltantes()
    {
        $datosFaltantes = array();
        if (!$this->ta05){
            array_push($datosFaltantes,'Tipo (Cajas, Banco, Mutuales o Cooperativas, Colegios, Comité, S.A.M.C.O.)');
        } if (!$this->circuns) {
            array_push($datosFaltantes,'Circunscripción');
        }
        return $datosFaltantes;
    }

    public function debeHabilitarse()
    {
        return (!$this->hasCodigo() && !$this->isHabilitado() && $this->tieneDatosNecesarios());
    }

    public function estadoSinFirmante()
    {
        if($this->getEstado()->getEstado() == '0')
        return $this->aplicarTransicion('T01');
        elseif ($this->getEstado()->getEstado() == '1')
        return $this->aplicarTransicion('T35');
    }

    public function debeSerSinFirmante()
    {   if($this->getEstado()->getEstado() == '1')
        return (!$this->getFirmante());
        elseif($this->getEstado()->getEstado() == '0'){
          return (!$this->getFirmante() && $this->getUsuarioResponsable());  
        }
    }

    public function isBloqueado()
    {
        return $this->estado->isEstado('2','ORG');
    }

    public function isInhabilitado()
    {
        return $this->estado->isEstado('3','ORG');
    }

    public function isCreado()
    {
        return $this->estado->isEstado('0','ORG');
    }

    public function isSinFirmante()
    {
        return $this->estado->isEstado('4','ORG');
    }

    public function puedeOperar()
    {
        return ($this->isHabilitado());
    }

    public function estadoCreado()
    {
        return (!$this->getFirmante() && !$this->getUsuarioResponsable() && $this->isCreado());
    }
    
    
    public function debeSerConFirmante()
    {
        return ($this->getFirmante() && $this->isSinFirmante());
    }

    public function estadoConFirmante()
    {
        return $this->aplicarTransicion('T53');
    }
    
    public function debeSerConUsuarioResponsable()
    {
        return (!$this->getFirmante() && $this->getUsuarioResponsable() && $this->isCreado());
    }

    public function estadoConUsuarioResponsable()
    {
        return $this->aplicarTransicion('T01');
    }

    public function bloquear()
    {
        return $this->aplicarTransicion('T12');
    }
    
    public function inhabilitar()
    {   if ($this->getEstado()->getEstado() == '1'){
            return $this->aplicarTransicion('T34');}
        elseif ($this->getEstado()->getEstado() == '2') {
            return $this->aplicarTransicion('T14');}
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->cargos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->aportantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->firmantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->liquidaciones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add liquidacione
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacione
     *
     * @return Organismo
     */
    public function addLiquidacione(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacione)
    {
        $this->liquidaciones[] = $liquidacione;

        return $this;
    }

    /**
     * Remove liquidacione
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacione
     */
    public function removeLiquidacione(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacione)
    {
        $this->liquidaciones->removeElement($liquidacione);
    }

    /**
     * Get liquidaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidaciones()
    {
        return $this->liquidaciones;
    }

    public function getUsuarioResponsable()
    {
        $responsable = null;
        foreach ($this->usuarioOrganismos as $usuario) {
//            if ($usuario->isOrganismoAdmin() && $usuario->isValid()) {
            if ($usuario->isOrganismoAdmin() && true) {
                $responsable = $usuario->getUsuario();
                break;
            }
        }

        return $responsable;
    }

    public function getUsuarioResponsableMail()
    {
        $responsable = null;
        foreach ($this->usuarioOrganismos as $usuario) {
//            if ($usuario->isOrganismoAdmin() && $usuario->isValid()) {
            if ($usuario->isOrganismoAdmin() && true) {
                $responsable = $usuario->getCorreo();
                break;
            }
        }

        return $responsable;
    }

    /**
     *  Recibe un array de IDs de Aportantes, y realiza la operación de conjuntos
     * Diferencia, entre los aportantes que se pasaron por parametro y los que están
     * asociados al Organismo
     * @param type $idsAportantes
     * @return array | null
     */
    public function getAportantesFaltantes($idsAportantes)
    {
        $aportatensFaltantes = array();
        if (!$this->aportantes) {
            return null;
        }
        foreach ($this->aportantes as $aportante)
        {
            if (!in_array($aportante->getId(), $idsAportantes)) {
                array_push($aportatensFaltantes, $aportante);
            }
        }

        return $aportatensFaltantes;
    }

    /**
     * Set tipoOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\TipoOrganismo $tipoOrganismo
     *
     * @return Organismo
     */
    public function setTipoOrganismo(\Caja\SiafcaIntranetBundle\Entity\TipoOrganismo $tipoOrganismo = null)
    {
        $this->tipoOrganismo = $tipoOrganismo;

        return $this;
    }

    /**
     * Get tipoOrganismo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\TipoOrganismo
     */
    public function getTipoOrganismo()
    {
        return $this->tipoOrganismo;
    }

    public function getExpedienteToShow()
    {
        if ($this->expediente){
            $expediente = substr_replace($this->expediente, '-', 5, 0);
            return substr_replace($expediente, '-',13, 0);
        }
        return null;
    }
    
    /**
     * Para mostrar las propiedades en las TWIG
     * @return array[]
     */
    public function getPropiedadesToShow()
    {
        $properties = parent::getPropiedadesToShow();
        if ($this->codigo) {
            $properties['Código'] = $this->codigo;
        }
        if ($this->fechaAlta) {
            $properties['Fecha de Alta'] = $this->fechaAlta->format('d/m/Y');
        }
        if ($this->expediente) {
            $properties['Expendiente'] = $this->getExpedienteToShow();
        }
        if ($this->resolucion) {
            $properties['Resolución'] = $this->resolucion;
        }

        return $properties;
    }

    /**
     * Para mostrar las propiedades de valores multiples en las TWIG
     * @return array[]
     */
    public function getPropiedadesMultiplesToShow()
    {
        $properties = parent::getPropiedadesMultiplesToShow();
        $properties['Entidad responsable'] = array(
            'data' => $this->entidadResponsable,
            'entityName' => 'Entidad responsable',
            'edit' => array(
                'enabled' =>true,
                'params' => null),
            'delete' => array(
                'path' => 'entidadresponsable_delete_ajax',
                'params' => array('id' => '-entityId-', 'organismo' => '-orgId-'),
                'enabled' => true),
            'add' => array(
                'enabled' => true)
            );
        $properties['Firmantes'] = array(
            'data' => $this->firmantes,
            'entityName' => 'Firmantes',
            'edit' => array(
                'enabled' =>true),
            'delete' => array(
                'path' => 'firmante_delete_ajax',
                'params' => array('id' => '-entityId-'),
                'enabled' => true),
            'add' => array(
                'enabled' => true));
        
        return $properties;
    }

    /**
     * Para mostrar las propiedades en las TWIG
     * @return array[]
     */
    public function getPropiedadesToShowSegunTipoOrganismo()
    {
        $tipoOrganismo = $this->tipoOrganismo? $this->tipoOrganismo->getCodigo() : substr($this->codigo, 0,1);
        switch ($tipoOrganismo)
        {
            case '0':
                return $this->getPropiedadesToShowEscuelaPrivada();
            case '1':
                return $this->datosAdministracionCentralCompletos();
            case '2':
                return $this->datosMunicipalidadComunaCompletos();
            case '4':
                return $this->datosAdministracionCentralCompletos();
            case '5':
                return $this->datosAdheridosCompletos();
        }
    }

    private function getPropiedadesToShowEscuelaPrivada()
    {
        $datos = array();
        if ($this->numero) {
            array_push($datos, 'N° '.$this->numero);
        }
        return $datos;
    }

    /**
     * Add expteAmparo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmparo $expteAmparo
     *
     * @return Organismo
     */
    public function addExpteAmparo(\Caja\SiafcaIntranetBundle\Entity\ExpteAmparo $expteAmparo)
    {
        $this->expteAmparos[] = $expteAmparo;

        return $this;
    }

    /**
     * Remove expteAmparo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmparo $expteAmparo
     */
    public function removeExpteAmparo(\Caja\SiafcaIntranetBundle\Entity\ExpteAmparo $expteAmparo)
    {
        $this->expteAmparos->removeElement($expteAmparo);
    }

    /**
     * Get expteAmparos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExpteAmparos()
    {
        return $this->expteAmparos;
    }
    
    /**
     * Determina los grupos de validación de acuerdo al tipo de organismo
     * @return array[]
     */
    public static function determineValidationGroups($form){
        
        $organismo = $form->getData();
        $t = $organismo->getTipoOrganismo()->getCodigo();
        
        switch ($t) {
            case '0':
                $grupos = array('Default', 'EscPriv');
                break;
            case '1':
                $grupos = array('Default', 'AdmCentral');
                break;
            case '2':
                $grupos = array('Default', 'MunCom');
                break;
            case '4':
                $grupos = array('Default', 'Descentralizado');
                break;
            case '5':
                $grupos = array('Default', 'adherido');
                break;
             default:
                 $grupos = array('Default');
        }
         
        return $grupos;
    }
}
