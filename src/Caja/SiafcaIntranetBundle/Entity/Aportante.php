<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Aportante
 *
 * @ORM\Table(name="aportante"
 * , indexes={
 *      @ORM\Index(name="idx_aportante_1", columns={"organismo_id"}),
 *      @ORM\Index(name="idx_aportante_2", columns={"cargo_id"}),
 *      @ORM\Index(name="idx_aportante_3", columns={"revista_id"}),
 *      @ORM\Index(name="idx_aportante_4", columns={"persona_id"}),
 *      @ORM\Index(name="idx_aportante_5", columns={"estado_id"}),
 * }
 * , uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_aportante_1", columns={"persona_id", "organismo_id", "cargo_id", "revista_id", "nroliq"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\AportanteRepository")
 */

class Aportante
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupAportante"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaAlta", type="oracledatetime", nullable=true)
     * @Groups({"groupAportante"})
     */
    private $fechaAlta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaBaja", type="oracledatetime", nullable=true)
     * @Groups({"groupAportante"})
     */
    private $fechaBaja;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nroLiq", type="integer")
     * @Groups({"groupAportante"})
     */
    private $nroLiq;
    
    /**
     * @ORM\ManyToOne(targetEntity="Persona", inversedBy="aportantes")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id")
     * @Groups({"groupAportante"})
     */
    private $persona;
    
    /**
     * @ORM\ManyToOne(targetEntity="Organismo", inversedBy="aportantes")
     * @ORM\JoinColumn(name="organismo_id", referencedColumnName="id")
     * @Groups({"groupAportante"})
     */
    private $organismo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Cargo", inversedBy="aportantes")
     * @ORM\JoinColumn(name="cargo_id", referencedColumnName="id")
     * @Groups({"groupAportante"})
     */
    private $cargo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Revista")
     * @ORM\JoinColumn(name="revista_id", referencedColumnName="id")
     * @Groups({"groupAportante"})
     */
    private $revista;
    
    /**
    * @ORM\OneToMany(targetEntity="Item", mappedBy="aportante")
    */
    private $items;
    
    /**
    * @ORM\OneToMany(targetEntity="ExpteAmpItem", mappedBy="aportante")
    */
    private $amparos;
    
    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id")
     * @Groups({"groupAportante"})
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupAportante"})
     */
    private $descripcion;
    
    /**
     * @var $historicColumns
     * Se utiliza para rellenar de forma dinamica la única plantilla de historicos en la carpeta Partials
     */
    public static $historicColumns = array(
        // Cada sub-array es una columna dentro de la tabla de historial
        array(
            'displayName' => 'organismo', // Nombre utilizado para encabezados de columna y otros textos visibles por el usuario
            'backEndName' => 'organismo', // Nombre de variable con el que el sistema hace referencia internamente.
            'queryPath' => 'o.nombre', // La ruta de la propiedad tal como se utiliza en la consulta de doctrine. (Para ordenamientos de columna)
        ),
        array(
            'displayName' => 'cargo',
            'backEndName' => 'cargo',
            'queryPath' => 'c.nombre',
        ),
        array(
            'displayName' => 'revista',
            'backEndName' => 'revista',
            'queryPath' => 'r.nombre',
        ),
        array(
            'displayName' => 'estado',
            'backEndName' => 'estado',
            'queryPath' => 'es.nombre',
        ),
        array(
            'displayName' => 'nro. liq.',
            'backEndName' => 'nroLiq',
            'queryPath' => 'e.nroLiq',
        ),
        array(
            'displayName' => 'fecha de alta',
            'backEndName' => 'fechaAlta',
            'queryPath' => 'e.fechaAlta',
        ),
        array(
            'displayName' => 'fecha de baja',
            'backEndName' => 'fechaBaja',
            'queryPath' => 'e.fechaBaja',
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
     * Set fechaAlta
     *
     * @param \DateTime $fechaAlta
     *
     * @return Aportante
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
     * @return Aportante
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
     * Set persona
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Persona $persona
     *
     * @return Aportante
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
     * @return Aportante
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
     * Set cargo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Cargo $cargo
     *
     * @return Aportante
     */
    public function setCargo(\Caja\SiafcaIntranetBundle\Entity\Cargo $cargo = null)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get cargo
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Cargo
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set revista
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Revista $revista
     *
     * @return Aportante
     */
    public function setRevista(\Caja\SiafcaIntranetBundle\Entity\Revista $revista = null)
    {
        $this->revista = $revista;

        return $this;
    }

    /**
     * Get revista
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Revista
     */
    public function getRevista()
    {
        return $this->revista;
    }

    /**
     * Add item
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Item $item
     *
     * @return Aportante
     */
    public function addItem(\Caja\SiafcaIntranetBundle\Entity\Item $item)
    {
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

    /**
     * Add amparo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparo
     *
     * @return Aportante
     */
    public function addAmparo(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparo)
    {
        $this->amparos[] = $amparo;

        return $this;
    }

    /**
     * Remove amparo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparo
     */
    public function removeAmparo(\Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem $amparo)
    {
        $this->amparos->removeElement($amparo);
    }

    /**
     * Get amparos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAmparos()
    {
        return $this->amparos;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
        $this->amparos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->persona->__toString();
    }
    
    public function getCuil()
    {
        return $this->persona->getCuil();
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
     *  Función para utilizar en las Twig, para evitar los if
     *      dentro de las mismas.
     * @return string
     */
    public function getAportanteShow()
    {
        if ($this->persona) {
            $aportante = $this->persona->getApellidoPat(). " " .$this->persona->getApellidoMat(). " " .$this->persona->getNombre();
            return $aportante;
        } else {
             return null;
         }
            
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
     * Set estado
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $estado
     *
     * @return Aportante
     */
    public function setEstado(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $estado = null)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function getHistoricColumns()
    {
        return self::$historicColumns;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Aportante
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
}
