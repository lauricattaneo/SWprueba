<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Cargo
 *
 * @ORM\Table(name="cargo"
 * , indexes={
 *     @ORM\Index(name="idx_cargo_1", columns={"organismo_id"}),
 *     @ORM\Index(name="idx_cargo_2", columns={"estado_id"}), 
 *     @ORM\Index(name="idx_cargo_3", columns={"sectorpasivo_id"}), 
 *     @ORM\Index(name="idx_cargo_4", columns={"categoria_id"}),
 *     @ORM\Index(name="idx_cargo_6", columns={"origen_id"}),
 *     @ORM\Index(name="idx_cargo_8", columns={"tipoOrganismo_id"}), 
 *  }, uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_cargo_1", columns={"codigo","organismo_id","tipoOrganismo_id"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\CargoRepository")
 */

class Cargo
{   
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupCargo"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=15)
     * @Groups({"groupCargo"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     * @Groups({"groupCargo"})
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     * @Groups({"groupCargo"})
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupCargo"})
     */
    private $descripcion;


    /**
     * @ORM\ManyToOne(targetEntity="Estado", fetch="EAGER")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
     */
    private $estado;
    
    /**
     * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="cargos", fetch="EAGER")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
     */
    private $organismo;
    
    /**
     * @ORM\ManyToOne(targetEntity="SectorPasivo", fetch="EAGER")
     * @ORM\JoinColumn(name="sectorpasivo_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
     */
    private $sectorPasivo;
    
    /**
     * @ORM\OneToMany(targetEntity="Aportante", mappedBy="cargo",cascade="persist")
     */
    private $aportantes;
    
    /**
     * @var float
     * @ORM\Column(name="subsidio", type="decimal", precision=6, scale=2, nullable=true, options = {"default": 0})
     * @Groups({"groupCargo"})
     */
    private $subsidio;
    
    /**
     * @ORM\ManyToOne(targetEntity="TipoOrganismo")
     * @ORM\JoinColumn(name="tipoOrganismo_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
      */
    private $tipoOrganismo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
     * @Assert\NotBlank(groups={"MunCom"},
     *      message="Debe seleccionar la Categoria.")
     */
    private $categoria;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="origen_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
     */
    private $origen;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="clase_id", referencedColumnName="id")
     * @Groups({"groupCargo"})
     */
   
    private $clase_cargo;
    
   

      /**
     * Set clase_cargo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $clase_cargo
     *
     * @return Cargo
     */
    function setClase_cargo($clase_cargo) {
        $this->clase_cargo = $clase_cargo;
    }

   /**
     * Get tipoOrganismo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\TipoOrganismo 
     */
    
    function getTipoOrganismo() {
        return $this->tipoOrganismo;
    }

     /**
     * Get categoria
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    
    function getCategoria() {
        return $this->categoria;
    }

      /**
     * Get clase_cargo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    
    function getClase_cargo() {
        return $this->clase_cargo;
    }
    
     /**
     * Get origen
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
  
    function getOrigen() {
        return $this->origen;
    }
    
        /**
     * Set tipoOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\TipoOrganismo $tipoOrganismo
     *
     * @return Cargo
     */
    
    function setTipoOrganismo($tipoOrganismo) {
        $this->tipoOrganismo = $tipoOrganismo;
    }

     /**
     * Set categoria
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $categoria
     *
     * @return Cargo
     */
    
    function setCategoria($categoria) {
        $this->categoria = $categoria;
    }

 
     /**
     * Set origen
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $origen
     *
     * @return Cargo
     */
    function setOrigen($origen) {
        $this->origen = $origen;
    }
    
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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Cargo
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

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Cargo
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
     * Set area
     *
     * @param string $area
     *
     * @return Cargo
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Cargo
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
     * Set estado
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Estado $estado
     *
     * @return Cargo
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

    /**
     * Set organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Organismo $organismo
     *
     * @return Cargo
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
     * Set sectorPasivo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\SectorPasivo $sectorPasivo
     *
     * @return Cargo
     */
    public function setSectorPasivo(\Caja\SiafcaIntranetBundle\Entity\SectorPasivo $sectorPasivo = null)
    {
        $this->sectorPasivo = $sectorPasivo;

        return $this;
    }

    /**
     * Get sectorPasivo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\SectorPasivo
     */
    public function getSectorPasivo()
    {
        return $this->sectorPasivo;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aportantes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add aportante
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Aportante $aportante
     *
     * @return Cargo
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

    public function initialize($cargoArray) 
    {
        list(
            $this->codigo,
            $this->nombre,
            $this->area,
            $this->descripcion
        ) = array(
                $cargoArray['codigo'],
                $cargoArray['nombre'],
                $cargoArray['area'],
                $cargoArray['descripcion']
            );
    }


    /**
     * Set subsidio
     *
     * @param string $subsidio
     *
     * @return Cargo
     */
    public function setSubsidio($subsidio)
    {
        $this->subsidio = $subsidio;

        return $this;
    }

    /**
     * Get subsidio
     *
     * @return string
     */
    public function getSubsidio()
    {
        return $this->subsidio;
    }
    
    public function __toString() {
        return $this->nombre;
    }

    /**
     * Para mostrar las propiedades en las TWIG
     * @return array[]
     */
    public function getPropiedadesToShow()
    {
        $properties = array();
        if ($this->nombre) {
            $properties['Nombre'] = $this->nombre;
        }
        if ($this->codigo) {
            $properties['Código'] = $this->codigo;
        }
        if ($this->area) {
            $properties['Area'] = $this->area;
        }
        if ($this->descripcion) {
            $properties['Descripción'] = $this->descripcion;
        }
        if ($this->tipoOrganismo) {
            $properties['Tipo'] = $this->tipoOrganismo;
        }
       
        return $properties;
    }

    /**
     * Para mostrar las propiedades de valores multiples en las TWIG
     * @return array[]
     */
    public function getPropiedadesMultiplesToShow()
    {
        $properties = array();
        // Muestra el conteo de aportantes porque es la unica propiedad que puede impedir la accion del borrado
        // del cargo. Si hay mas de 0 aportantes con este cargo, desde el front end se deshabilita la accion de borrar
        $properties['Aportantes'] = array(
            'count' => count($this->getAportantes()),
            'entityName' => 'Usuario responsable');

        return $properties;
    }
    
    
}
