<?php

namespace Caja\SiafcaIntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Usuario
 *
 * @ORM\Table(name="usuario"
 * , indexes={
 *     @ORM\Index(name="idx_usuario_1", columns={"estado_id"}),
 * 
 *  }
 * ,uniqueConstraints={
 *  @ORM\UniqueConstraint(name="uk_usuario_1", columns={"username"})
 * }
 * )
 * @ORM\Entity(repositoryClass="Caja\SiafcaIntranetBundle\Repository\UsuarioRepository")
 * @Soap\Alias("Usuario")
 * @UniqueEntity(
 *      fields= { "username"},
 *      message= "El nombre de usuario ya está en uso, por favor elija otro."
 * )
 */
class Usuario implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE"))
     * @Soap\ComplexType("int", nillable=true)
     * @Groups({"group1"})
     */
    private $id;
    /**
     * @ORM\Column(name="username", type="string", length=100)
     * @Soap\ComplexType("string")
     * @Groups({"group1"})
     */
    private $username;
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Soap\ComplexType("string")
     * @Groups({"group1"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Soap\ComplexType("string", nillable=true)
     * @Groups({"group1"})
     */
    private $salt;
    
   /**
     * @ORM\ManyToOne(targetEntity="Estado", fetch="EAGER")
     * @ORM\JoinColumn(name="estado_id", referencedColumnName="id", nullable=false)
     * @Soap\ComplexType("Caja\SiafcaIntranetBundle\Entity\Estado")
     * @Groups({"group1"})
     */
    private $estado;
    
    /**
     * @ORM\OneToMany(targetEntity="UsuarioOrganismo", mappedBy="usuario",cascade="persist",fetch="EAGER")
     * @Groups({"group1"})
     */
    private $usuarioOrganismos;
    
    /**
     * Variable no mapeada en Doctrine, por lo tanto no se persiste. Tiene
     *  como propósito setear el Organismo que está utilizando el Usuario
     *  para trabajar en el sistema.
     * @var Organismo
     */
    private $organismoActual;
    
     /**
     * @ORM\OneToMany(targetEntity="Presentacion", mappedBy="usuario", cascade="persist")
     */
    private $presentaciones;
    

    public function changePassword($newPassword){
        try{
            $this->password = $newPassword;
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    public function orgAdm() {
        foreach ($this->getUsuarioOrganismos() as $uorganismo) {
            $organismo = $uorganismo->getOrganismo();
            $discdr = $organismo->getDiscr();
            if ($discdr == 'oficina') {
                $response['adm'] = 'oficina';
                $response['id'] = $organismo->getId();
                $response['roles'] = $this->getRoles();
                return $response;
            } else {
                $response['adm'] = 'organismo';
                $response['id'] = $organismo->getId();
                $response['roles'] = $this->getRoles();
            }
        }
        return $response;
    }

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
     * Set username
     *
     * @param string $username
     *
     * @return Usuario
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Usuario
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    

    /**
     * Add usuarioOrganismo
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo
     *
     * @return Usuario
     */
    public function addUsuarioOrganismo(\Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo $usuarioOrganismo)
    {
        $this->usuarioOrganismos[] = $usuarioOrganismo;
        $usuarioOrganismo->setUsuario($this);

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
     * Set estado
     *
     * @param string $estado
     *
     * @return Usuario
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    public function eraseCredentials() {
        
    }
    
    /**
     * 
     * @return array
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @Groups({"group1"})
     */
    public function getRoles() {
        
//        if ($this->organismoActual && !$oficina) {
//            $oficina = $this->organismoActual;
//        }
        
//        if ($organismo)
//        {   
            $i = 0;
            foreach ($this->usuarioOrganismos as $usorg)
            {   
                $roles[$i] = $usorg->getRol()->getRol();
                $i++;
                    
            }
//        }
//        else{
//             throw new \Symfony\Component\HttpKernel\Exception\HttpException('No tiene oficina asociada');
//        }
        if (empty($roles))
        {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException('No tiene rol asociado');
        }
//        dump($roles);
//        die;
        return $roles;
        
//        $usuarioOrganismo = $this->usuarioOrganismos->first();
//        $rol = $usuarioOrganismo->getRol();
//        return array($rol->getRol());
    }

//     /**
//     * @return array
//     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
//     * @Groups({"group1"})
//     */
//    public function getRoles() {
//        if ($this->usuario_rol) {
//            $i = 0;
//            foreach ($this->usuario_rol as $urol)
//            {       
//                if ($urol->getRol()) {
//                    $rol = $urol->getRol();
//                    $roles[$i] = $rol->getRol();
//                    $i++;
//                }
//            }
//        }
//
//        if (empty($roles))
//        {
//            throw new \Symfony\Component\HttpKernel\Exception\HttpException('Usuario sin rol asignado');
//        }
//       return $roles;
//    }
    
    public function getSalt() {
        return $this->salt;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        if ($this->estado == '1') 
        {
            return false;
        }
        return true;
    }

    public function serialize() {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->estado,
        ));

    }

    public function unserialize($serialized) {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->estado,
        ) = unserialize($serialized);

    }


    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Usuario
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }
    
    public function getOrganismos($tipoReturn = null)
    {
        $organismos = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($this->usuarioOrganismos as $usuorg)
        {
            $organis = $usuorg->getOrganismo();
//            if ($usuorg->isValid() && $organis->isValid())
//            {
                $organismos->add($organis);
//            }
        }
        if ($tipoReturn == 'array')
        {
            return $organismos->toArray();
        } 
        return $organismos;
    }
    
    public function __toString() 
    {
        return $this->username;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarioOrganismos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Setea el Organismo asociado al Usuario actualmente, con fin de poder
     * obtener el rol correcto asociado al mismo
     * @param Organismo $organismoActual
     */
    function setOrganismoActual($organismoActual) {
        $this->organismoActual = $organismoActual;
    }
    
    public function resetPassword()
    {
        $this->password = $this->username;
    }


    /**
     * Add presentacione
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Presentacion $presentacione
     *
     * @return Usuario
     */
    public function addPresentacione(\Caja\SiafcaIntranetBundle\Entity\Presentacion $presentacione)
    {
        $this->presentaciones[] = $presentacione;

        return $this;
    }

    /**
     * Remove presentacione
     *
     * @param \Caja\SiafcaIntranetBundle\Entity\Presentacion $presentacione
     */
    public function removePresentacione(\Caja\SiafcaIntranetBundle\Entity\Presentacion $presentacione)
    {
        $this->presentaciones->removeElement($presentacione);
    }

    /**
     * Get presentaciones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPresentaciones()
    {
        return $this->presentaciones;
    }
}
