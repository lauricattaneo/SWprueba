<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Oficina
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\OficinaRepository")
 * @ORM\Table(
 *     name="oficina",
 * indexes={
 *    @ORM\Index(name="idx_oficina_1", columns={"zona_id"}),
 * }
 * )
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"oficina" = "Oficina", "organismo" = "Organismo"})
 */
class Oficina
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Groups({"groupOrganismo"})
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="UsuarioOrganismo", mappedBy="organismo",cascade="persist")
     */
    protected $usuarioOrganismos;

    /**
     * @ORM\OneToMany(targetEntity="Domicilio", mappedBy="organismo", cascade="persist")
     * @Assert\Valid
     */
    protected $domicilios;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=50, unique=false, nullable=false)
     * @Groups({"groupOrganismo"})
     * @Assert\Length(
     *      max = 50,
     *      maxMessage = "El nombre no debe tener mas de 50 caracteres.",
     * )
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=50, unique=false, nullable=true)
     * @Groups({"groupOrganismo"})
     */
    protected $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="correos", type="string", length=255, nullable=true)
     * @Groups({"groupOrganismo"})
     */
    protected $correos;

    /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="zona_id", referencedColumnName="id")
     * @Groups({"groupOrganismo"})
     */
    protected $zona;

    /**
     * @var string
     *
     * @ORM\Column(name="cuit", type="string", length=11, unique=false, nullable=true)
     * @Groups({"groupOrganismo"})
     * @Assert\Length(
     *     min = 11,
     *     max = 11,
     *     exactMessage = "El C.U.I.T. debe tener 11 caracteres.",
     * )
     */
    protected $cuit;

    protected $discr = 'oficina';
    
        public function getDiscr(){
        return $this->discr;
    }
    
    public function __construct()
    {
        $this->domicilios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usuarioOrganismos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->nombre;
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
     * Add usuarioOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo
     *
     * @return Oficina
     */
    public function addUsuarioOrganismo(\Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo)
    {
        $this->usuarioOrganismos[] = $usuarioOrganismo;

        return $this;
    }

    /**
     * Remove usuarioOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo
     */
    public function removeUsuarioOrganismo(\Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo)
    {
        $this->usuarioOrganismos->removeElement($usuarioOrganismo);
    }

    /**
     * Get usuarioOrganismos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarioOrganismos()
    {
        return $this->usuarioOrganismos;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Oficina
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
     * Add domicilio
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Domicilio $domicilio
     *
     * @return Organismo
     */
    public function addDomicilio(\Caja\SiafcaIntranetBundle\Entity\Domicilio $domicilio)
    {
        $domicilio->setOrganismo($this);
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
     * @Groups({"groupOrganismo"})
     */
    public function getDomicilio($tipoDomicilio = null)
    {
        return $this->getDomiciliodelTipo($tipoDomicilio);
    }

    protected function getDomicilioDelTipo($tipoDomicilio)
    {
        $dom = NULL;
        if (!$tipoDomicilio && !$this->domicilios->isEmpty()){
            $dom = $this->domicilios->first();
        }
        foreach ($this->domicilios as $domicilio)
        {
            if ($domicilio->getTipoDomicilio() == $tipoDomicilio)
            {
                $dom = $domicilio;
            }
        }

        return $dom;
    }

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

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Organismo
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }
    
   /**
     *  Función para utilizar en las Twig, para evitar los if
     *      dentro de las mismas.
     * @return string
     */
    public function getTelefonoToShow()
    {
        if (!$this->telefono) {
            return 'No hay telefono registrado';
        } else {
            return $this->telefono;
        }
    }

    /**
     * Set correos
     *
     * @param string $correos
     *
     * @return UsuarioOrganismo
     */
    public function setCorreos($correos)
    {
        $this->correos = $correos;

        return $this;
    }

    /**
     * Get correos
     *
     * @return string
     */
    public function getCorreos()
    {
        return $this->correos;
    }

    /**
     * Set zona
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $zona
     *
     * @return Organismo
     */
    public function setZona(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $zona = null)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getZona()
    {
        return $this->zona;
    }

    /**
     * Set cuit
     *
     * @param integer $cuit
     *
     * @return Organismo
     */
    public function setCuit($cuit)
    {
        $this->cuit = $cuit;

        return $this;
    }

    /**
     * Get cuit
     *
     * @return int
     */
    public function getCuit()
    {
        return $this->cuit;
    }

    public function getCuitToShow()
    {
        if ($this->cuit){
            $cuil = substr_replace($this->cuit, '-', 2, 0);
            return substr_replace($cuil, '-',11, 0);
        }
        return null;
    }
    

    public function getUsuarioResponsable()
    {
        $responsable = null;
        foreach ($this->usuarioOrganismos as $usuario) {
//            if ($usuario->isOrganismoAdmin() && $usuario->isValid()) {
            if ($usuario->isOrganismoAdmin() && true) {
                $responsable = $usuario;
                break;
            }
        }

        return $responsable;
    }

    /**
     * Para mostrar las propiedades de valores multiples en las TWIG
     * @return array[]
     */
    public function getPropiedadesMultiplesToShow()
    {
        $properties = array();
        $properties['Domicilios'] = array(
            'data' => $this->domicilios,
            'entityName' => 'Domicilio',
            'delete' => array(
                'path' => 'domicilio_delete',
                'enabled' => true,
                'params' => array('id' => '-entityId-'),
            ),
            'edit' => array(
                'enabled' =>true,
            ),
            'add' => array(
                'enabled' => true));
        $properties['Usuario responsable'] = array(
            'data' => $this->usuarioOrganismos,
            'entityName' => 'UsuarioOrganismo',
            'add' => array(
                'enabled' => true),
            'edit' => array(
                'enabled' =>true,
            ),
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
        if ($this->telefono) {
            $properties['Teléfono'] = $this->telefono;
        }
        if ($this->correos) {
            $properties['Correos'] = $this->correos;
        }
        if ($this->zona){
            $properties['Zona'] = $this->zona;
        }
        if ($this->domicilios){
            $properties['Domicilios'] = $this->domicilios[0];
        }
        if ($this->cuit){
            $properties['CUIT'] = $this->getCuitToShow();
        }

        return $properties;
    }
}
