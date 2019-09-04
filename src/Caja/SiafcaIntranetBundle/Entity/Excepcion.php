<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Excepción
 *
 * @ORM\Table(name="excepcion"
 * , indexes={
 *     @ORM\Index(name="idx_excepcion_1", columns={"codigo","archivo","linea"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\ExcepcionRepository")
 */

class Excepcion
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
     * @ORM\Column(name="codigo", type="string", length=20)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="archivo", type="string")
     */
    private $archivo;

    /**
     * @var string
     *
     * @ORM\Column(name="linea", type="integer")
     */
    private $linea;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="string")
     */
    private $mensaje;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string")
     */
    private $usuario;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ruta", type="string")
     */
    private $ruta;
    
    /**
     * @var string
     *
     * @ORM\Column(name="controlador", type="string")
     */
    private $controlador;

    /**
     * @var string
     *
     * @ORM\Column(name="parametros", type="string")
     */
    private $parametros;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="string")
     */
    private $fecha;

    /**
     * Get id
     *
     * @return integer
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
     * @return Excepcion
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
     * Set archivo
     *
     * @param string $archivo
     *
     * @return Excepcion
     */
    public function setArchivo($archivo)
    {
        $this->archivo = $archivo;

        return $this;
    }

    /**
     * Get archivo
     *
     * @return string
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * Set linea
     *
     * @param integer $linea
     *
     * @return Excepcion
     */
    public function setLinea($linea)
    {
        $this->linea = $linea;

        return $this;
    }

    /**
     * Get linea
     *
     * @return integer
     */
    public function getLinea()
    {
        return $this->linea;
    }

    /**
     * Set mensaje
     *
     * @param string $mensaje
     *
     * @return Excepcion
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }


    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return Excepcion
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set ruta
     *
     * @param string $ruta
     *
     * @return Excepcion
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta
     *
     * @return string
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set fecha
     *
     * @param string $fecha
     *
     * @return Excepcion
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return string
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set controlador
     *
     * @param string $controlador
     *
     * @return Excepcion
     */
    public function setControlador($controlador)
    {
        $this->controlador = $controlador;

        return $this;
    }

    /**
     * Get controlador
     *
     * @return string
     */
    public function getControlador()
    {
        return $this->controlador;
    }

    /**
     * Set parametros
     *
     * @param string $parametros
     *
     * @return Excepcion
     */
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;

        return $this;
    }

    /**
     * Get parametros
     *
     * @return string
     */
    public function getParametros()
    {
        return $this->parametros;
    }
}
