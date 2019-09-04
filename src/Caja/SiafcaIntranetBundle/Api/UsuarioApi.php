<?php

namespace Caja\SiafcaIntranetBundle\Api;

/**
 * Description of UsuarioApi
 *
 * @author cnass
 */
class UsuarioApi {
    private $id;
    
    private $username;
    
    private $password;

    private $salt;
    
    private $estado;
    
    private $persona;
    
    private $usuarioOrganismos;
    
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }


    public function getPassword() {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
    }

    public function getSalt() {
        return $this->salt;
    }
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    public function getEstado() {
        return $this->estado;
    }
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function getPersona() {
        return $this->persona;
    }

    public function setPersona($persona) {
        $this->persona = $persona;
    }

    public function getRol() {
        return $this->rol;
    }

    public function setRol($rol) {
        $this->rol = $rol;
    }
    
     public function addUsuarioOrganismo($usuarioOrganismo)
    {
        $this->usuarioOrganismos[] = $usuarioOrganismo;

        return $this;
    }

    public function removeUsuarioOrganismo($usuarioOrganismo)
    {
        $this->usuarioOrganismos->removeElement($usuarioOrganismo);
    }

    public function getUsuarioOrganismos()
    {
        return $this->usuarioOrganismos;
    }
}
