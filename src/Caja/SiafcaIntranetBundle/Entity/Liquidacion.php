<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Caja\SiafcaIntranetBundle\Entity\Item;
use Caja\SiafcaIntranetBundle\Entity\Usuario;
use Caja\SiafcaIntranetBundle\Entity\Presentacion;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * Liquidacion
 *
 * @ORM\Table(name="liquidacion"
 * , indexes={
 *     @ORM\Index(name="idx_liquidacion_1", columns={"organismo_id"}),
 *     @ORM\Index(name="idx_liquidacion_2", columns={"estado_id"}),
 *     @ORM\Index(name="idx_liquidacion_4", columns={"tipoliq_id"}),
 *     @ORM\Index(name="idx_liquidacion_5", columns={"fuenteliq_id"}),
 *     
 *  }
 * , uniqueConstraints={
 *    @ORM\UniqueConstraint(name="uk_liquidacion_1", columns={"organismo_id","anio","mes","tipoliq_id","nroLiq"}),
 *    @ORM\UniqueConstraint(name="uk_liquidacion_2", columns={"hijo_id"}),
 *    @ORM\UniqueConstraint(name="uk_liquidacion_3", columns={"padre_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\LiquidacionRepository")
 */

class Liquidacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupLiquidacion"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="anio", type="string", length=4)
     * @Assert\Length(
     *      min = 4,
     *      max = 4,
     *      exactMessage = "El año debe tener 4 caracteres.",
     * )
     * @Groups({"groupLiquidacion"})
     */
    private $anio;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", length=2)
     * @Groups({"groupLiquidacion"})
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=true)
     * @Groups({"groupLiquidacion"})
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupLiquidacion"})
     */
    private $descripcion;

     /**
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id", nullable=false)
     * @Groups({"groupLiquidacion"})
     */
    private $estado;
    
     /**
     * @var string
     *
     * @ORM\Column(name="importa_dat", type="text", nullable=true, options = {"default": "EMPTY_CLOB()"})
     */
    private $importaDat;
    
    /**
     * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="liquidaciones")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     */
    private $organismo;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="fuenteLiq_id", referencedColumnName="id")
     * @Groups({"groupLiquidacion"})
     */
    private $fuenteLiq;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="tipoLiq_id", referencedColumnName="id")
     * @Groups({"groupLiquidacion"})
     */
    private $tipoLiq;
    
    /**
     * @ORM\OneToOne(targetEntity="Liquidacion", inversedBy="padre")
     * @ORM\JoinColumn(name="hijo_id", referencedColumnName="id", nullable=true)
     */
    private $hijo;
    
    /**
     * @ORM\OneToOne(targetEntity="Liquidacion", mappedBy="hijo")
     * @ORM\JoinColumn(name="padre_id", referencedColumnName="id", nullable=true)
     */
    private $padre;
    
    /**
    * @ORM\OneToMany(targetEntity="Item", mappedBy="liquidacion", cascade="persist")
    * @Groups({"groupLiquidacion2"})
    */
    private $items;
    
    /**
    * @ORM\OneToOne(targetEntity="ConjuntoControl", mappedBy="liquidacion")
    * @Groups({"groupLiquidacion2"})
    */
    private $conjuntoControl;
    
    /**
    * 
    * @ORM\OneToOne(targetEntity="Informe", mappedBy="liquidacion")
    * 
    */
    private $informe;
    
    /**
    * 
    * @ORM\OneToOne(targetEntity="Presentacion", mappedBy="liquidacion", cascade= {"persist"})
    * 
    */
    private $presentacion;
    
    /**
    * 
    * @ORM\OneToOne(targetEntity="BoletaDeposito", mappedBy="liquidacion", cascade= {"persist"})
    * 
    */
    private $boletaDeposito;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nroLiq", type="integer", nullable=true)
     * @Groups({"groupLiquidacion"})
     */
    private $nroLiq;
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        $periodo = $this->getAnio()."/".$this->getMes();
        return $periodo."(".$this->getId().")";
    }


    public function isManual(){
        $fuenteLiq = $this->getFuenteLiq();
        return (($fuenteLiq->getCodigo()=='1' && $fuenteLiq->getClase()=='FUENTE') ? true : false);
    }


    /**
     * Set anio
     *
     * @param string $anio
     *
     * @return Liquidacion
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return string
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set mes
     *
     * @param string $mes
     *
     * @return Liquidacion
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     *
     * @return Liquidacion
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Liquidacion
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
     * Set organismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Organismo $organismo
     *
     * @return Liquidacion
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
     * Set fuenteLiq
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $fuenteLiq
     *
     * @return Liquidacion
     */
    public function setFuenteLiq(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $fuenteLiq = null)
    {
        $this->fuenteLiq = $fuenteLiq;

        return $this;
    }

    /**
     * Get fuenteLiq
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getFuenteLiq()
    {
        return $this->fuenteLiq;
    }

    /**
     * Set tipoLiq
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $tipoLiq
     *
     * @return Liquidacion
     */
    public function setTipoLiq(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $tipoLiq = null)
    {
        $this->tipoLiq = $tipoLiq;

        return $this;
    }

    /**
     * Get tipoLiq
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getTipoLiq()
    {
        return $this->tipoLiq;
    }

    /**
     * Set hijo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $hijo
     *
     * @return Liquidacion
     */
    public function setHijo(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $hijo = null)
    {
        $this->hijo = $hijo;

        return $this;
    }

    /**
     * Get hijo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function getHijo()
    {
        return $this->hijo;
    }

    /**
     * Set padre
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $padre
     *
     * @return Liquidacion
     */
    public function setPadre(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $padre = null)
    {
        $this->padre = $padre;

        return $this;
    }

    /**
     * Get padre
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function getPadre()
    {
        return $this->padre;
    }

    /**
     * Set importaDat
     *
     * @param string $importaDat
     *
     * @return Liquidacion
     */
    public function setImportaDat($importaDat)
    {
        $this->importaDat = $importaDat;

        return $this;
    }

    /**
     * Get importaDat
     *
     * @return string
     */
    public function getImportaDat()
    {
        return $this->importaDat;
    }

    /**
     * Add item
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Item $item
     *
     * @return Liquidacion
     */
    public function addItem(\Caja\SiafcaIntranetBundle\Entity\Item $item)
    {
        $item->setLiquidacion($this);
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Item $item
     */
    public function removeItem(\Caja\SiafcaIntranetBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
    
    public function initialize($liquidacionArray) 
    {
        list(
            $this->mes,
            $this->anio,
            $this->titulo,
            $this->descripcion
            
        ) = array(
                $liquidacionArray['mes'],
                $liquidacionArray['anio'],
                $liquidacionArray['titulo'],
                $liquidacionArray['descripcion']
            );
    }

    /**
     * Set conjuntoControl
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ConjuntoControl $conjuntoControl
     *
     * @return Liquidacion
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
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set estado
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Estado $estado
     *
     * @return Liquidacion
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
    
    public function datosCargados()
    {
        $succes = $this->aplicarTransicion('T07');
        return $succes;
    }
    
    public function aplicarTransicion($codigoTransicion)
    {
        $proximoEstado = $this->estado->proximoEstado($codigoTransicion, 'LIQ');
        if ($proximoEstado){
            $this->setEstado($proximoEstado);
            return true;
        }
        
        return false;
    }
    /**
     * 
     * @param \Doctrine\Common\Collections\Collection $items
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function updateItems($items)
    {
        $this->items->clear();
        foreach ($items as $item) {
            $this->addItem($item);
        }
        
        return $this;
    }
    
    public function getItemIds()
    {
        $ids = array();
        foreach ($this->items as $item) {
            array_push($ids, $item->getId());
        }
        return $ids;
    }

    /**
     * Set informe
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $informe
     *
     * @return Liquidacion
     */
    public function setInforme(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $informe = null)
    {
        $this->informe = $informe;

        return $this;
    }

    /**
     * Get informe
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Informe
     */
    public function getInforme()
    {
        return $this->informe;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasInforme()
    {
        if ($this->informe) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function copyData(Liquidacion $liquidacion)
    {
        foreach ($liquidacion->getItems() as $item) 
        {
            $newItem = new Item();
            $newItem->copyData($item);
            $this->addItem($newItem);
        }
        
        return $this;
    }

    /*
     * Si el estado de la liquidacion es 06 (Controlado) se instancia una
     * presentacion nueva y se aplica la trans T09 (Controlado->Presentado)
     * Si el estado de la liquidacion es 13 (Rechazado) se modifican los datos
     * de la presentacion original de la liquidacion y se aplica la trans T21
     * (Rechazado->Presentado)
     */

    public function presentar(Usuario $usuario)
    {
        $succes = false;
        if($this->getEstado()->getEstado() == '06')
            $succes = $this->aplicarTransicion('T09');
        else if($this->getEstado()->getEstado() == '13')
            $succes = $this->aplicarTransicion('T21');
        if(!$this->getPresentacion())
            $presentacion = new Presentacion();
        else $presentacion = $this->getPresentacion();
        $presentacion->setFechaPresentacion(new DateTime('now'));
        $presentacion->setUsuario($usuario);
        $this->setPresentacion($presentacion);
        
        return $succes;
    }
    
    /**
     * Conversion de formato de fecha.
     * Retorna el nombre del mes en formato texto a partir del numérico.
     * @return string
     */
    public function getNombreMes()
    {
        switch ($this->mes) {
            case '01': $mes = 'Enero'; break;
            case '02': $mes = 'Febrero'; break;
            case '03': $mes = 'Marzo'; break;
            case '04': $mes = 'Abril'; break;
            case '05': $mes = 'Mayo'; break;
            case '06': $mes = 'Junio'; break;
            case '07': $mes = 'Julio'; break;
            case '08': $mes = 'Agosto'; break;
            case '09': $mes = 'Septiembre'; break;
            case '10': $mes = 'Octubre'; break;
            case '11': $mes = 'Noviembre'; break;
            case '12': $mes = 'Diciembre'; break;
            default: throw new Exception('El formato del mes no es válido');
        }

        return $mes;
    }

    /**
     * Set presentacion
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Presentacion $presentacion
     *
     * @return Liquidacion
     */
    public function setPresentacion(\Caja\SiafcaIntranetBundle\Entity\Presentacion $presentacion = null)
    {
        $presentacion->setLiquidacion($this);
        $this->presentacion = $presentacion;
        

        return $this;
    }

    /**
     * Get presentacion
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Presentacion
     */
    public function getPresentacion()
    {
        return $this->presentacion;
    }
    
    /**
     * 
     * @return type
     */
    public function getPropiedadesToShow()
    {
        $properties = array();
        if ($this->fuenteLiq) {
            $properties['Fuente'] = $this->fuenteLiq;
        }
        if ($this->tipoLiq) {
            $properties['Tipo'] = $this->tipoLiq;
        }
        if ($this->presentacion) {
            $properties['Presentado'] = $this->presentacion->getFechaPresentacion()->format('d/m/Y');
        }
        if ($this->informe) {
            $properties['Cant. Aportantes'] = $this->informe->getCantidadAportantes();
        }
        
        return $properties;
    }

    /**
     * Set boletaDeposito
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\BoletaDeposito $boletaDeposito
     *
     * @return Liquidacion
     */
    public function setBoletaDeposito(\Caja\SiafcaIntranetBundle\Entity\BoletaDeposito $boletaDeposito = null)
    {
        $this->boletaDeposito = $boletaDeposito;

        return $this;
    }

    /**
     * Get boletaDeposito
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\BoletaDeposito
     */
    public function getBoletaDeposito()
    {
        return $this->boletaDeposito;
    }

    /**
     * Set nroLiq
     *
     * @param string $nroLiq
     *
     * @return Aportante
     */
    public function setNroLiq($nroLiq)
    {
        $this->nroLiq = $nroLiq;

        return $this;
    }

    /**
     * Get nroLiq
     *
     * @return string
     */
    public function getNroLiq()
    {
        return $this->nroLiq;
    }
    
    /**
     * Devuelve un array con las propiedades que identifican como única a la liquidación
     * @return array
     */
    public function getUniqueKey()
    {
        $uk = array(
            'mes' => $this->mes,
            'anio' => $this->anio,
            'tipoLiq' => $this->tipoLiq,
            'organismo' => $this->organismo,
            'nroLiq' => $this->nroLiq
        );
        return $uk;
    }
}
