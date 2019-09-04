<?php

namespace Caja\SiafcaIntranetBundle\Security;

use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Caja\SiafcaIntranetBundle\Entity\Usuario;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class OrganismoVoter extends Voter {
    
    const VIEW = 'view';
    const EDIT = 'edit';
    
    protected function supports($atributo, $subject) {
        
        // Si $atributo no es VIEW o EDIT return false
        if (!in_array($atributo, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // Votar solo para un instancia de Organismo
        if (!$subject instanceof Organismo) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($atributo, $subject, TokenInterface $token) {
        
        $usuario = $token->getUser();

        if (!$usuario instanceof Usuario) {
            // El Usuario debe estar loggeado, sino negar acceso
            return false;
        }

        $organismo = $subject;

        switch($atributo) {
            case self::VIEW:
                return $this->canView($organismo, $usuario);
            case self::EDIT:
                return $this->canEdit($organismo, $usuario);
        }

        throw new \LogicException('Este código no debe ser ejecutado!');
    }
    
    private function canView(Organismo $organismo, Usuario $usuario)
    {
        // Si pueden editar, pueden ver
        if ($this->canEdit($organismo, $usuario)) {
            return true;
        }

        $rol = $usuario->getRoles($organismo);
        if (in_array($rol, $this->getViewRoles()))
        {
            return true;
        }
        return false;
    }

    private function canEdit(Organismo $organismo, Usuario $usuario)
    {
        $rol = $usuario->getRoles($organismo);
        $rolstr = reset($rol);
        
        if (in_array($rolstr, $this->getEditRoles()))
        {
            return true;
        }
        
        
        return false;
    }
    
    private function getEditRoles()
    {
        return array(
                'ROLE_ORGANISMO_ADMIN',
                'ROLE_ADMIN',
                'ROLE_SUPER_ADMIN',
                'ROLE_CONTRALOR_ADMIN'
        );
    }
    
    private function getViewRoles()
    {
        return array(
            'ROLE_ORGANISMO_USER'
        );
    }
}
