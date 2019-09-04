<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Reporte
 *
 * @ORM\Table(name="reporte"
 * , indexes={
 *      @ORM\Index(name="idx_reporte_1", columns={"modulo"}),
 *  }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ReporteRepository")
 */

class Reporte
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"groupReporte"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="modulo", type="string", length=255)
     * @Groups({"groupReporte"})
     */
    private $modulo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255)
     * @Groups({"groupReporte"})
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     * @Groups({"groupReporte"})
     */
    private $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     * @Groups({"groupReporte"})
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=255, nullable=true)
     * @Groups({"groupReporte"})
     */
    private $valor;


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
     * Set modulo
     *
     * @param string $modulo
     *
     * @return Reporte
     */
    public function setModulo($modulo)
    {
        $this->modulo = $modulo;

        return $this;
    }

    /**
     * Get modulo
     *
     * @return string
     */
    public function getModulo()
    {
        return $this->modulo;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return Reporte
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return Reporte
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Reporte
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
     * Set valor
     *
     * @param string $valor
     *
     * @return Reporte
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string
     */
    public function getValor()
    {
        return $this->valor;
    }
}
