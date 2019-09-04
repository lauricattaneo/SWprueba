<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoletaDeposito
 *
 * @ORM\Table(name="boleta_deposito",
 * indexes={
 *    @ORM\Index(name="idx_boleta_deposito_1", columns={"tipoboleta_id"}),
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\BoletaDepositoRepository")
 */

class BoletaDeposito
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
     * @var string
     *
     * @ORM\Column(name="nroCuenta", type="string", length=255)
     */
    private $nroCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="codOrganismo", type="string", length=255)
     */
    private $codOrganismo;

    /**
     * @var string
     *
     * @ORM\Column(name="nomOrganismo", type="string", length=255)
     */
    private $nomOrganismo;

    /**
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", length=255)
     */
    private $periodo;

    /**
     * @var string
     *
     * @ORM\Column(name="impApPersonal", type="decimal", precision=12, scale=2)
     */
    private $impApPersonal;

    /**
     * @var string
     *
     * @ORM\Column(name="impApPatronal", type="decimal", precision=12, scale=2)
     */
    private $impApPatronal;

    /**
     * @var string
     *
     * @ORM\Column(name="impOtros", type="decimal", precision=12, scale=2)
     */
    private $impOtros;

    /**
     * @var string
     *
     * @ORM\Column(name="impTotal", type="decimal", precision=12, scale=2)
     */
    private $impTotal;
    
     /**
     * @ORM\ManyToOne(targetEntity="Parametrizacion")
     * @ORM\JoinColumn(name="tipoBoleta_id", referencedColumnName="id")
     */
    private $tipoBoleta;

    /**
     * @ORM\OneToOne(targetEntity="Liquidacion", inversedBy="boletaDeposito")
     * @ORM\JoinColumn(name="liquidacion_id", referencedColumnName="id", nullable=false)
     */
    private $liquidacion;
    
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
     * Set nroCuenta
     *
     * @param string $nroCuenta
     *
     * @return BoletaDeposito
     */
    public function setNroCuenta($nroCuenta)
    {
        $this->nroCuenta = $nroCuenta;

        return $this;
    }

    /**
     * Get nroCuenta
     *
     * @return string
     */
    public function getNroCuenta()
    {
        return $this->nroCuenta;
    }

    /**
     * Set codOrganismo
     *
     * @param string $codOrganismo
     *
     * @return BoletaDeposito
     */
    public function setCodOrganismo($codOrganismo)
    {
        $this->codOrganismo = $codOrganismo;

        return $this;
    }

    /**
     * Get codOrganismo
     *
     * @return string
     */
    public function getCodOrganismo()
    {
        return $this->codOrganismo;
    }

    /**
     * Set nomOrganismo
     *
     * @param string $nomOrganismo
     *
     * @return BoletaDeposito
     */
    public function setNomOrganismo($nomOrganismo)
    {
        $this->nomOrganismo = $nomOrganismo;

        return $this;
    }

    /**
     * Get nomOrganismo
     *
     * @return string
     */
    public function getNomOrganismo()
    {
        return $this->nomOrganismo;
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     *
     * @return BoletaDeposito
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * Set impApPersonal
     *
     * @param string $impApPersonal
     *
     * @return BoletaDeposito
     */
    public function setImpApPersonal($impApPersonal)
    {
        $this->impApPersonal = $impApPersonal;

        return $this;
    }

    /**
     * Get impApPersonal
     *
     * @return string
     */
    public function getImpApPersonal()
    {
        return $this->impApPersonal;
    }

    /**
     * Set impApPatronal
     *
     * @param string $impApPatronal
     *
     * @return BoletaDeposito
     */
    public function setImpApPatronal($impApPatronal)
    {
        $this->impApPatronal = $impApPatronal;

        return $this;
    }

    /**
     * Get impApPatronal
     *
     * @return string
     */
    public function getImpApPatronal()
    {
        return $this->impApPatronal;
    }

    /**
     * Set impOtros
     *
     * @param string $impOtros
     *
     * @return BoletaDeposito
     */
    public function setImpOtros($impOtros)
    {
        $this->impOtros = $impOtros;

        return $this;
    }

    /**
     * Get impOtros
     *
     * @return string
     */
    public function getImpOtros()
    {
        return $this->impOtros;
    }

    /**
     * Set impTotal
     *
     * @param string $impTotal
     *
     * @return BoletaDeposito
     */
    public function setImpTotal($impTotal)
    {
        $this->impTotal = $impTotal;

        return $this;
    }

    /**
     * Get impTotal
     *
     * @return string
     */
    public function getImpTotal()
    {
        return $this->impTotal;
    }

    /**
     * Set tipoBoleta
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Parametrizacion $tipoBoleta
     *
     * @return BoletaDeposito
     */
    public function setTipoBoleta(\Caja\SiafcaIntranetBundle\Entity\Parametrizacion $tipoBoleta = null)
    {
        $this->tipoBoleta = $tipoBoleta;

        return $this;
    }

    /**
     * Get tipoBoleta
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Parametrizacion
     */
    public function getTipoBoleta()
    {
        return $this->tipoBoleta;
    }

    /**
     * Set liquidacion
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion
     *
     * @return BoletaDeposito
     */
    public function setLiquidacion(\Caja\SiafcaIntranetBundle\Entity\Liquidacion $liquidacion)
    {
        $this->liquidacion = $liquidacion;

        return $this;
    }

    /**
     * Get liquidacion
     *
     * @return \Caja\SiafcaIntranetBundle\Entity\Liquidacion
     */
    public function getLiquidacion()
    {
        return $this->liquidacion;
    }
}
