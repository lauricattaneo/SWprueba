<?php

namespace Caja\SiafcaIntranetWsBundle\Services;

use BeSimple\SoapBundle\ServiceDefinition\Annotation as Soap;
use Caja\SiafcaIntranetBundle\Entity\Estado;
use Caja\SiafcaIntranetBundle\Entity\Usuario;
use Caja\SiafcaIntranetBundle\Entity\Firmante;
use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use SoapFault;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Caja\SiafcaIntranetBundle\Entity\Cargo;
use Caja\SiafcaIntranetBundle\Entity\Aportante;
use Caja\SiafcaIntranetBundle\Entity\Persona;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Doctrine\ORM\Query;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebService extends ContainerAware
{

    
    /**
     * @Soap\Method("getEstadoActivo")
     * @Soap\Result(phpType = "string")
     */
    public function getEstadoActivoAction()
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $estadoActivo = $em->getRepository("SiafcaIntranetBundle:Parametrizacion")->findOneBy(array('clase' => 'APORTANTE', 'codigo' => '0'));

        if (!$estadoActivo) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('Error, estado activo no encontrado.'));
        }

        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($estadoActivo, 'json', array('groups' => array(
            "groupParametrizacion"
        )));

        return $jsonContent;
    }

    /**
     * @Soap\Method("getTipoDocDNI")
     * @Soap\Result(phpType = "string")
     */
    public function getTipoDocDNIAction()
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $tipoDocDNI = $em->getRepository("SiafcaIntranetBundle:TipoDocumento")->findOneBy(array('codigo'=> '3'));
        
        if(!$tipoDocDNI){
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('Error, tipo de doc DNI no encontrado.'));
        }
        
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($tipoDocDNI, 'json', array('groups' => array('groupTipoDocumento')));
        
        return $jsonContent;
    }
    /**
     *
     * @param string $queryString
     * @param string $entity
     * @param array $groups
     * @param boolean $multiple
     * @return string
     * @throws SoapFault
     */
    private function getQueryJSON($queryString, $entity, $groups)
    {
        $jsonContent = null;

        try {
            $em = $this->container->get('Doctrine')->getManager();
            $query = $em->createQuery($queryString);
            $entidades = $query->getResult();
            if (!$entidades)
            {
                throw new Exception('Entidad ['.$entity.'] no encontrada', 1);
                //throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay '.$entity));
            }
            $serializer = $this->container->get('serializer');
            $jsonContent = $serializer->serialize($entidades, 'json', $groups);
        }
        catch (Exception $e) {
            // Accedo al servicio de log en la BD
            $ex_db = $this->get('excepciones.log');
            $ex_db->persistir($e);
        }

        return $jsonContent;
    }

    /**
     * @Soap\Method("resetPassword")
     * @Soap\Param("userId", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function resetPasswordAction($userId){
        $em = $this->container->get('Doctrine')->getManager();

        $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($userId);

        $usuario->resetPassword();
        $usuario->setSalt(bin2hex(random_bytes(50)));
        $encoder = $this->container->get('security.password_encoder');
            //$encoded = $encoder->encodePassword($usuario, $usuario->getPassword());

    }

    /**
     * @Soap\Method("editPersona")
     * @Soap\Param("personaJSON", phpType = "string")
     * @Soap\Result(phpType = "boolean")
     */
    public function editPersonaAction($personaJSON){
        $em = $this->container->get('Doctrine')->getManager();

        $personaArray = json_decode($personaJSON,true);

        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')
                ->find($personaArray['id']);
        $tipoDoc = $em->getRepository('SiafcaIntranetBundle:TipoDocumento')
                ->find($personaArray['tipoDocumento']['id']);
        $sexo = $em->getRepository('SiafcaIntranetBundle:Sexo')
                ->find($personaArray['sexo']['id']);
        $nacionalidad = $em->getRepository('SiafcaIntranetBundle:Nacionalidad')
                ->find($personaArray['nacionalidad']['id']);
        $estadoCivil = $em->getRepository('SiafcaIntranetBundle:EstadoCivil')
                ->find($personaArray['estadoCivil']['id']);

        $persona->setNombre($personaArray['nombre']);
        $persona->setApellidoMat($personaArray['apellidoMat']);
        $persona->setApellidoPat($personaArray['apellidoPat']);
        $persona->setTipoDocumento($tipoDoc);
        $persona->setDocumento($personaArray['documento']);
        $persona->setSexo($sexo);
        $persona->setNacionalidad($nacionalidad);
        $persona->setEstadoCivil($estadoCivil);
        //$fechaNac = DateTime::createFromFormat('d/m/Y', $personaArray['fechaNac']);
        $persona->setFechaNac($personaArray['fechaNac']);

        $em->persist($persona);
        $em->flush();

        
        }


    /**
     * @Soap\Method("ayuda")
     * @Soap\Param("codigo", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function ayudaAction($codigo){
        $em = $this->container->get('Doctrine')->getManager();
        
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => $codigo));
        
        if(!is_null($ayuda)){
            return $ayuda->getTexto();
        }
        else return "";
    }
    
    /**
     * @Soap\Method("getFechaActual")
     * @Soap\Result(phpType = "string")
     */
    public function getFechaActualAction (){
        $salida = date("Y/m/d");
        return $salida;
    }



    /**
     * @Soap\Method("transUsuarioOrganismo")
     * @Soap\Param("idUsuario", phpType = "string")
     * @Soap\Param("codeTrans", phpType = "string")
     * @Soap\Param("idOrg", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function transUsuarioOrganismoAction($idUsuario,$codeTrans,$idOrg){
        $em = $this->container->get('Doctrine')->getManager();

        $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($idUsuario);
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idOrg);
        $usuarioOrg = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->findOneBy(array(
                                            'usuario'=>$usuario,
                                            'organismo'=>$organismo));
        try{
            $success = $usuarioOrg->aplicarTransicion($codeTrans);
            $em->persist($usuarioOrg);
            $em->flush();
        }catch(SoapFault $ex){
            throw new \Symfony\Component\HttpKernel\Exception\HttpException($ex->getMessage());
        }

        return $success;
    }





    /**
     * @Soap\Method("changePassword")
     * @Soap\Param("idUsuario", phpType = "string")
     * @Soap\Param("encodedNewPassword", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function changePasswordAction($idUsuario,$encodedNewPassword){
        $em = $this->container->get('Doctrine')->getManager();

        $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($idUsuario);

        try{
            $success = $usuario->changePassword($encodedNewPassword);

            $em->persist($usuario);

            $em->flush();
        } catch (SoapFault $ex) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException($ex->getMessage());
        }
        return $success;

    }

    /**
     * @Soap\Method("isHabilitado")
     * @Soap\Param("idUsuario", phpType = "string")
     * @Soap\Param("idOrganismo", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function isHabilitadoAction($idUsuario,$idOrganismo){
        $em = $this->container->get('Doctrine')->getManager();

        $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($idUsuario);
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idOrganismo);

        /*if(is_null($usuario))
            return "u es null";

        if(is_null($organismo))
            return "o es null";
         *
         */
        $usuarioOrganismo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->findOneBy(array(
         'usuario' => $usuario,
         'organismo' => $organismo
         ));
        
        if(is_null($usuarioOrganismo))
            return "uo es null";
        
        $habilitado = $usuarioOrganismo->isHabilitado();


        return $habilitado;

    }

    /**
    * @Soap\Method("getUsuario")
    * @Soap\Param("name", phpType = "string")
    * @Soap\Param("orgId", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getUsuarioAction($name, $orgId)
    {

        $em = $this->container->get('Doctrine')->getManager();

        $user = $em->getRepository('SiafcaIntranetBundle:Usuario')->findOneBy(array(
         'username' => $name,
         ));
        if ($orgId) {
            $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($orgId);
            $user->setOrganismoActual($organismo);
        }

        if (!$user) {
            throw new SoapFault('USER_NOT_FOUND', sprintf('Usuario "%s" no encontrado', $name));
        }

        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($user, 'json', array('groups' => array(
            'group1','groupEstado','groupUsuarioOrganismo',
        )));

        return $jsonContent;
    }

    /**
    * @Soap\Method("getUsuarios")
    * @Soap\Param("orgId", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getUsuariosAction($orgId)
    {
        $groups = array('groups' => array(
            'group1','groupEstado','groupUsuarioOrganismo'
        ));

        $em = $this->container->get('Doctrine')->getManager();
        $query = $em->createQuery('SELECT '
                                    . 'u.id, u.username, u.password, '
                                    . 'o.correo, r.nombre roles, '
                                    . 'e.nombre estado, e.estado estadoid '
                                . 'FROM '
                                    . 'SiafcaIntranetBundle:Usuario u '
                                    . 'JOIN u.usuarioOrganismos o '
                                    . 'JOIN o.rol r '
                                    . 'JOIN o.estado e '
                                . 'WHERE '
                                    . 'o.organismo = '.$orgId);
        $entidades = $query->getResult();
        if (!$entidades)
        {
            throw new Exception('Entidad no encontrada', 1);
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($entidades, 'json', $groups);

        return $jsonContent;
    }

    /**
    * @Soap\Method("getOrganismos")
    * @Soap\Param("usrId", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getOrganismosAction($usrId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();

        $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($usrId);
        if (!$usuario) {
            throw new SoapFault('ORGANISMOS_NOT_FOUND', sprintf('Usuario id "%s" no encontrado', $usrId));
        }
        $organismos = $usuario->getOrganismos();

        if (!$organismos) {
            throw new SoapFault('ORGANISMOS_NOT_FOUND', sprintf('El usuario id: "%s", no tiene organismos asociados, no está autorizado, o no se encuentran activos.', $usrId));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($organismos, 'json', array('groups' => array(
            "groupOrganismo","groupEstado", "groupTipoOrganismo", "groupDomicilio"
        )));
        return $this->container->get('besimple.soap.response')->setReturnValue($jsonContent);
    }

    /**
    * @Soap\Method("getOrganismo")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getOrganismoAction($id)
    {
        $groups = array('groups' => array(
            "groupOrganismo","groupEstado", "groupDomicilio", "groupTipoOrganismo", "groupParametrizacion"
        ));
        $criteria = array( 'id' => $id);
        return $this->getOneByJSON('Organismo', $groups, $criteria);
    }
    
   
    /**
    * @Soap\Method("getDomicilioFR")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getDomicilioFRAction($id)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
    
        $entidades = $em->getRepository('SiafcaIntranetBundle:Domicilio')->domicilioByOrganismo($id);
        
        if (!$entidades)
        {
            throw new Exception('Entidad no encontrada', 1);
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($entidades, 'json', array('groups' => array(
            "groupDomicilio",
            "groupOrganismo",
            "groupEstado", 
            "groupLocalidad", 
            "groupDepartamento", 
            "groupProvincia", 
            "groupTipoOrganismo", 
            "groupParametrizacion")));

        return $jsonContent;
    }
    

      /**
    * @Soap\Method("getCargosApp")
    * @Soap\Param("tipoorgId", phpType = "int")
    * @Soap\Param("page", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getCargosAppAction($tipoorgId, $page)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
   
        $tipoorganismo = $em->getRepository('SiafcaIntranetBundle:TipoOrganismo')->find($tipoorgId);
        $offset  = ($page * 20) - 20 ;
        $limite = 20;
        $or = 323;
        if (!$tipoorganismo)
        {
            throw new SoapFault('ORGANISMO_NOT_FOUND', sprintf('Tipo de Organismo "%s" no encontrado', $tipoorganismo));
        }
        $cargos = $em->getRepository('SiafcaIntranetBundle:Cargo')->cargosportipoorganismoapp($tipoorganismo, $or, $offset, $limite);
        
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($cargos, 'json', array('groups' => array(
            "groupCargo","groupSectorPasivo", "groupParametrizacion", "groupTipoOrganismo"
        )));
        return $jsonContent;
    }
    
     /**
    * @Soap\Method("getCargosTodos")
    * @Soap\Param("tipoorgId", phpType = "string")
    * @Soap\Param("page", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getCargosTodosAction($tipoorgId, $page)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
   
        $tipoorganismo = $em->getRepository('SiafcaIntranetBundle:TipoOrganismo')->find($tipoorgId);
        $offset  = ($page * 20) - 20 ;
        $limite = 20;
        if (!$tipoorganismo)
        {
            throw new SoapFault('ORGANISMO_NOT_FOUND', sprintf('Tipo de Organismo "%s" no encontrado', $tipoorganismo));
        }
        
        $cargos = $em->getRepository('SiafcaIntranetBundle:Cargo')->cargosportipoorganismotodos($tipoorganismo, $offset, $limite);
       
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($cargos, 'json', array('groups' => array(
            "groupCargo","groupSectorPasivo", "groupParametrizacion", "groupTipoOrganismo"
        )));
        return $jsonContent;
    }
    
      /**
    * @Soap\Method("getCargosAppCodigo")
    * @Soap\Param("tipoorganismo_id", phpType = "string")
    * @Soap\Param("codigo", phpType = "string")
    * @Soap\Param("page", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getCargosAppCodigoAction($tipoorganismo_id, $codigo, $page)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
   
        $tipoorganismo = $em->getRepository('SiafcaIntranetBundle:TipoOrganismo')->find($tipoorganismo_id);
        $offset  = ($page * 20) - 20 ;
        $limite = 20;
        $origen = 323;
        if (!$tipoorganismo)
        {
            throw new SoapFault('ORGANISMO_NOT_FOUND', sprintf('Tipo de Organismo "%s" no encontrado', $tipoorganismo));
        }
        
        $cargos = $em->getRepository('SiafcaIntranetBundle:Cargo')->cargosTipoOrganismoCodigo($tipoorganismo, $origen, $codigo, $offset, $limite);     
              
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($cargos, 'json', array('groups' => array(
            "groupCargo","groupSectorPasivo", "groupParametrizacion", "groupTipoOrganismo"
        )));
        return $jsonContent;
    }
    
          /**
    * @Soap\Method("getCargosAppNombre")
    * @Soap\Param("tipoorganismo_id", phpType = "string")
    * @Soap\Param("nombre", phpType = "string")
    * @Soap\Param("page", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getCargosAppNombreAction($tipoorganismo_id, $nombre, $page)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
   
        $tipoorganismo = $em->getRepository('SiafcaIntranetBundle:TipoOrganismo')->find($tipoorganismo_id);
        $offset  = ($page * 20) - 20 ;
        $limite = 20;
        $origen = 323;
        if (!$tipoorganismo)
        {
            throw new SoapFault('ORGANISMO_NOT_FOUND', sprintf('Tipo de Organismo "%s" no encontrado', $tipoorganismo));
        }
        
        $cargos = $em->getRepository('SiafcaIntranetBundle:Cargo')->cargosTipoOrganismoNombre($tipoorganismo, $origen, $nombre, $offset, $limite);     
              
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($cargos, 'json', array('groups' => array(
            "groupCargo","groupSectorPasivo", "groupParametrizacion", "groupTipoOrganismo"
        )));
        return $jsonContent;
    }

    
        
    /**
    * @Soap\Method("get")
    * @Soap\Param("entidad", phpType = "string")
    * @Soap\Result(phpType = "string")
    */

    public function get($entidad)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $entidades = $em->getRepository('SiafcaIntranetBundle:'.$entidad)->findAll();
        if (!$entidades)
        {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay '.$entidad));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($entidades, 'json', array('groups' => array(
            "group".$entidad
        )));
        return $jsonContent;
    }

    /**
    * @Soap\Method("saveCargo")
    * @Soap\Param("cargoJSON", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function saveCargoAction($cargoJSON)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $cargoArray = json_decode($cargoJSON, true);
        $cargo = new Cargo();
        try {
            $cargo->initialize($cargoArray);
            $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($cargoArray['organismo']['id']);
            $tipoOrganismo = $em->getRepository('SiafcaIntranetBundle:TipoOrganismo')->find($cargoArray['tipoOrganismo']['id']);
            $sectorPasivo = $em->getRepository('SiafcaIntranetBundle:SectorPasivo')->find($cargoArray['sectorPasivo']['id']);
            $estadoInicial = $em->getRepository('SiafcaIntranetBundle:Estado')->find(1);
            $origen = $em->getRepository('SiafcaIntranetBundle:Parametrizacion')->find($cargoArray['origen']['id']);
            $categoria = $em->getRepository('SiafcaIntranetBundle:Parametrizacion')->find($cargoArray['categoria']['id']);

            $cargoArray['tipoOrganismo']['id'] = $tipoOrganismo->getId();
            $cargo->setOrganismo($organismo);
            $cargo->setOrigen($origen);
            $cargo->setSectorPasivo($sectorPasivo);
            $cargo->setTipoOrganismo($tipoOrganismo);
            $cargo->setCategoria($categoria);
            $cargo->setEstado($estadoInicial);

            $em->persist($cargo);
            $em->flush();

            return true;
        } catch (Exception $ex) {
            return $ex;
        }
    }

    /**
    * @Soap\Method("editAportante")
    * @Soap\Param("aportanteJSON", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function editAportanteAction($aportanteJSON)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $aportanteArray = json_decode($aportanteJSON,true);
        try{
            $aportante = $em->getRepository('SiafcaIntranetBundle:Aportante')->find($aportanteArray['id']);
            //$persona = $em->getRepository('SiafcaIntranetBundle:Persona')->find($aportanteArray['persona']['id']);
            $revista = $em->getRepository('SiafcaIntranetBundle:Revista')->find($aportanteArray['revista']['id']);
            //$organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($aportanteArray['organismo']['id']);
            $cargo = $em->getRepository('SiafcaIntranetBundle:Cargo')->find($aportanteArray['cargo']['id']);
            $estado = $em->getRepository('SiafcaIntranetBundle:Parametrizacion')->find($aportanteArray['estado']['id']);

            //$aportante->setFechaAlta($aportanteArray['fechaalta']);
            //$aportante->setFechaBaja($aportanteArray['fechabaja']);
            //$aportante->setOrganismo($organismo);
            //$aportante->setArea($aportanteArray['cargo']);
            $aportante->setDescripcion($aportanteArray['descripcion']);
            $aportante->setNroLiq($aportanteArray['nroLiq']);
            $aportante->setRevista($revista);
            //$aportante->setPersona($persona);
            $aportante->setEstado($estado);
            $aportante->setCargo($cargo);
            
            $em->persist($aportante);
            $em->flush();
            
            return true;
        } catch (\SoapFault $ex) {
            throw new NotFoundHttpException($ex->getMessage());
        }
    }

     /**
    * @Soap\Method("editCargo")
    * @Soap\Param("cargoJSON", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function editCargoAction($cargoJSON)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $cargoArray = json_decode($cargoJSON,true);
        try{
            $cargo = $em->getRepository('SiafcaIntranetBundle:Cargo')->find($cargoArray['id']);
            $sectorPasivo = $em->getRepository('SiafcaIntranetBundle:SectorPasivo')->find($cargoArray['sectorPasivo']['id']);
        
            $cargo->setCodigo($cargoArray['codigo']);
            $cargo->setNombre($cargoArray['nombre']);
            $cargo->setArea($cargoArray['area']);
            $cargo->setDescripcion($cargoArray['descripcion']);
            $cargo->setSectorPasivo($sectorPasivo);
            $em->persist($cargo);
            $em->flush();
            
            return true;
        } catch (\SoapFault $ex) {
            return false;
        }
    }
  /**
    * @Soap\Method("getAportantescuil")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("cuil", phpType = "string")
    * @Soap\Param("page", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantescuilAction($idEntidad, $cuil)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $organismo= $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idEntidad);
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'cuil'=> $cuil));
        
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->findBy(array(
            'organismo' => $organismo,
            'persona' => $persona));
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes con cuil ingresado para el organismo "%s"', $organismo));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        return $jsonContent;
    }

     /**
    * @Soap\Method("getAportantesdni")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("dni", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesdniAction($idEntidad, $dni)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $organismo= $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idEntidad);
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'documento'=> $dni));
        
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->findBy(array(
            'organismo' => $organismo,
            'persona' => $persona));
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes con el dni ingresado para el organismo "%s"', $organismo));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        return $jsonContent;
    }
    
      /**
    * @Soap\Method("getAportantesapellido")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("apellido", phpType = "string")
     * @Soap\Param("nombre", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesapellidoAction($idEntidad, $apellido, $nombre)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $organismo= $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idEntidad);
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->buscarApellido($apellido, $nombre);
        
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->findBy(array(
            'organismo' => $organismo,
            'persona' => $persona));
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes con apellido y nombre ingresados para el organismo "%s"', $organismo));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        return $jsonContent;
    }
    
    
     /**
    * @Soap\Method("getAportantesliqcuil")
    * @Soap\Param("idLiq", phpType = "string")
    * @Soap\Param("cuil", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesliqcuilAction($idLiq, $cuil)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'cuil'=> $cuil));
        $cuilPersona = $persona->getCuil();
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->getAportantescuilByLiquidacion($idLiq, $cuilPersona);
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes para esta liquidacion "%s"', $idLiq));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        
       return $jsonContent;
    }

     /**
    * @Soap\Method("getAportantesliqdni")
    * @Soap\Param("idLiq", phpType = "string")
    * @Soap\Param("dni", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesliqdniAction($idLiq, $dni)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'documento'=> $dni));
        $dniPersona = $persona->getDocumento();
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->getAportanteDniLiquidacion($idLiq, $dniPersona);
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes para esta liquidacion "%s"', $idLiq));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        
       return $jsonContent;
    }    
    
     /**
    * @Soap\Method("getAportantesliqapellidobyliq")
    * @Soap\Param("idLiq", phpType = "string")
    * @Soap\Param("apellido", phpType = "string")
    * @Soap\Param("nombre", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesliqApellidobyliqAction($idLiq, $apellido, $nombre)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array('apellidoPat'=> $apellido, 'nombre'=>$nombre));
        
        $apePersona = $persona->getApellidoPat();
        $nomPersona = $persona->getNombre();
        
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->getAportantesapeByLiq($idLiq, $apePersona, $nomPersona);
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes para esta liquidacion "%s"', $idLiq));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        
       return $jsonContent;
       
    } 
    
    /**
    * @Soap\Method("getAportantes")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("criterioBusqueda", phpType = "string")
    * @Soap\Param("page", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesAction($idEntidad,$criterioBusqueda, $page)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $offset  = ($page * 20) - 20 ;
        $limite = 20;
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->getAportantesByOrganismo($idEntidad,$offset,$limite);
          
        
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes para el organismo "%s"', $idEntidad));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        return $jsonContent;
    }
    
        /**
    * @Soap\Method("getFirmanteId")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getFirmanteIdAction($id)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $firmante = $em->getRepository('SiafcaIntranetBundle:Firmante')->find($id);
        
        if (!$firmante) {
            
            throw new SoapFault('FIRMANTE_NOT_FOUND', sprintf('No hay existe el firmante con id "%s"', $id));
        }

        $serializer = $this->container->get('serializer');

        $jsonContent = $serializer->serialize($firmante, 'json', array('groups' => array(
            "groupFirmante","groupPersona2","groupPersona", "groupOrganismo", "groupEstado", "idEstadoInternet","idFirmanteInternet",
            "groupoFirmanteInternet", "idSexoInternet", "groupSexo", "groupTipoDocumento", "idTipoDocumentoInternet",
        )));

        return $jsonContent;
    }
    
     /**
    * @Soap\Method("getFirmanteBaja")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getFirmanteBajaAction($id){
        $em = $this->container->get('Doctrine')->getEntityManager();
        $firmante = $em->getRepository('SiafcaIntranetBundle:Firmante')->find($id);
       
        $firmante->darBaja();
        $firmante->setFechaHasta(new DateTime('now'));
        
        try{
        $em->persist($firmante);
        $em->flush();
       } 
        catch (Exception $ex) {
            return $ex;
        }
        
        $serializer = $this->container->get('serializer');

        $jsonContent = $serializer->serialize($firmante, 'json', array('groups' => array(
            "groupFirmante","groupPersona2","groupPersona", "groupOrganismo", "groupEstado", "idEstadoInternet","idFirmanteInternet",
            "groupoFirmanteInternet", "idSexoInternet", "groupSexo", "groupTipoDocumento", "idTipoDocumentoInternet",
        )));

        return $jsonContent;
    }

    /**
    * @Soap\Method("getFirmantes")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getFirmantesAction($idEntidad)
    {   
        $em = $this->container->get('Doctrine')->getEntityManager();
       
        $firmantes = $em->getRepository('SiafcaIntranetBundle:Firmante')->getFirmantesByOrganismo($idEntidad);
      
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($firmantes, 'json', array('groups' => array(
            "groupFirmante","groupPersona1","groupPersona2","groupPersona", "groupOrganismo", "groupEstado", "idEstadoInternet","idFirmanteInternet",
            "groupoFirmanteInternet","idSexoInternet", "idTipoDocumentoInternet"     )));
        return $jsonContent;
    }
    
    /**
    * @Soap\Method("getEstadoOrganismo")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
   public function getEstadoOrganismoAction($id){
        $em = $this->container->get('Doctrine')->getEntityManager();
        //return $id;
        //$query = $em->createQuery('SELECT o.estado FROM SiafcaIntranetBundle:Organismo o WHERE o.id = ' .$id);
        
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($id);
        $estado = $organismo->getEstado();
        
        if (!$estado)
        {
            throw new Exception('Entidad no encontrada', 1);
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($estado, 'json', array('groups' => array(
            "groupOrganismo","groupEstado", "idEstadoInternet", 
        )));
        return $jsonContent;
    }
   
    /**
    * @Soap\Method("getEstadoFirmante")
    * @Soap\Result(phpType = "string")
    */
   public function getEstadoFirmanteAction(){
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT e FROM SiafcaIntranetBundle:Estado e WHERE e.id = 34');
        $estado = $query->getSingleResult();
        
        // return 'id es ' . $estado->getId();
        if (!$estado) {
            throw new SoapFault('ESTADO_NOT_FOUND', sprintf('Error, estado no encontrado.'));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($estado, 'json', array('groups' => array(
            "groupEstado", "idEstadoInternet"
        )));
        return $jsonContent;
       
   }
   
      /**
    * @Soap\Method("getTodosAportantes")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("criterioBusqueda", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getTodosAportantesAction($idEntidad,$criterioBusqueda)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $aportantes = $em->getRepository('SiafcaIntranetBundle:Aportante')->findBy(array(
            $criterioBusqueda => $idEntidad
        ));
        if (!$aportantes)
        {
            throw new SoapFault('APORTANTES_NOT_FOUND', sprintf('No hay aportantes para el organismo "%s"', $idEntidad));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo","groupPersona", "groupOrganismo", "groupItem","groupParametrizacion"
        )));
        return $jsonContent;
    }

    /**
    * @Soap\Method("getAportante")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportanteAction($id)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $aportante = $em->getRepository('SiafcaIntranetBundle:Aportante')->find($id);

        if (!$aportante) {
            throw new SoapFault('APORTANTE_NOT_FOUND', sprintf('No hay existe el aportante con id "%s"', $id));
        }

        $serializer = $this->container->get('serializer');

        $jsonContent = $serializer->serialize($aportante, 'json', array('groups' => array(
            "groupAportante","groupPersona1","groupPersona2", "groupRevista","groupCargo", "groupPersona", "groupOrganismo", "groupItem"
        )));

//        $ret = $this->container->get('util')->varDump($jsonContent);

        return $jsonContent;
    }

    /**
    * @Soap\Method("getTotalAportantes")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("criterioBusqueda", phpType = "string")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalAportantesAction($idEntidad,$criterioBusqueda)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(a) FROM SiafcaIntranetBundle:Aportante a WHERE a.'.$criterioBusqueda.' = '.$idEntidad);
        $result = intval($query->getSingleScalarResult());
        return $result;
    }

    
       /**
    * @Soap\Method("getCantidadFirmantesCreadosRechazados")
    * @Soap\Param("idOrganismo", phpType = "string")
    * @Soap\Result(phpType = "int")
     */
    public function getCantidadFirmantesCreadosRechazadosAction($idOrganismo)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(f) '
                . 'FROM SiafcaIntranetBundle:Firmante '
                . 'f WHERE f.estado IN(34,37) '
                . 'AND f.organismo = '.$idOrganismo);
        return intval($query->getSingleScalarResult());
    }

    /**
    * @Soap\Method("getTotalCargosTipoOrgApp")
    * @Soap\Param("tipoorg", phpType = "int")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalCargosTipoOrgAppAction($tipoorg)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(c) FROM SiafcaIntranetBundle:Cargo c WHERE c.origen = 323'
                . ' AND c.tipoOrganismo = '.$tipoorg);
        
        $result = intval($query->getSingleScalarResult());
        return $result;
    }
    
          /**
    * @Soap\Method("getTotalCargosTipoOrgTodos")
    * @Soap\Param("tipoorg", phpType = "int")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalCargosTipoOrgTodosAction($tipoorg){
        $em = $this->container->get('Doctrine')->getEntityManager();
               
        $query = $em->createQuery('SELECT COUNT(c) FROM SiafcaIntranetBundle:Cargo c WHERE c.tipoOrganismo = '.$tipoorg);
        $result = intval($query->getSingleScalarResult());
        return $result;
    }
    
         /**
    * @Soap\Method("getTotalCargosTipoOrgCodigoApp")
    * @Soap\Param("tipoOrganismoId", phpType = "int")
     * @Soap\Param("codigo", phpType = "string")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalCargosTipoOrgCodigoAppAction($tipoOrganismoId, $codigo)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(c) '
                . 'FROM SiafcaIntranetBundle:Cargo c '
                . 'WHERE c.origen = 323 '
                . 'AND c.tipoOrganismo = :tOrg '
                . 'AND c.codigo = :cod '
                . 'ORDER BY c.nombre ASC '
                )->setParameter('cod', $codigo)
                ->setParameter('tOrg', $tipoOrganismoId);
        
        $result = intval($query->getSingleScalarResult());
        return $result;
    }
    
           /**
    * @Soap\Method("getTotalCargosTipoOrgNombreApp")
    * @Soap\Param("tipoOrganismoId", phpType = "int")
     * @Soap\Param("nombre", phpType = "string")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalCargosTipoOrgNombreAppAction($tipoOrganismoId, $nombre)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT(c) '
                . 'FROM SiafcaIntranetBundle:Cargo c '
                . 'WHERE c.origen = 323 '
                . 'AND c.tipoOrganismo = :tOrg '
                . 'AND upper(c.nombre) LIKE :nombre '
                . 'ORDER BY c.nombre ASC '
                )->setParameter('nombre', '%' . $nombre . '%')
                ->setParameter('tOrg', $tipoOrganismoId);
        
        $result = intval($query->getSingleScalarResult());
        return $result;
    }
    
    /**
    * @Soap\Method("getTotalAportantesCuil")
    * @Soap\Param("idEntidad", phpType = "string")
    * @Soap\Param("cuil", phpType = "string")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalAportantesCuilAction($idEntidad,$cuil)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($idEntidad);
       
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'cuil'=> $cuil));
        $persona_id= $persona->getId();
        
        $query = $em->createQuery('SELECT COUNT(a) FROM SiafcaIntranetBundle:Aportante a WHERE a.organismo'.' = '.$idEntidad.' and a.persona'.' = ' .$persona_id);
        $result = intval($query->getSingleScalarResult());
        return $result;
    }
    
     /**
    * @Soap\Method("getTotalAportantesLiquidacion")
    * @Soap\Param("idLiq", phpType = "int")
    * @Soap\Result(phpType = "int")
     */
    public function getTotalAportantesLiquidacionAction($idLiq)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $query = $em->createQuery('SELECT COUNT (i) FROM SiafcaIntranetBundle:Item i LEFT JOIN i.liquidacion l WHERE l.id ='. $idLiq);

       // $query = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->getTotalAportantesLiq($idLiq);       
        $result = intval($query->getSingleScalarResult());
        return $result;
    }
    
   /**
    * @Soap\Method("saveAportantePersona")
    * @Soap\Param("personaAportanteJSON", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function saveAportantePersonaAction($personaAportanteJSON)
        {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $personaAportanteArray = json_decode($personaAportanteJSON,true);
        
        try{
            if (is_null($personaAportanteArray ['persona']['id']) ) {
            $sexo = $em->getRepository('SiafcaIntranetBundle:Sexo')->find($personaAportanteArray['persona']['sexo']['id']);
            $nacionalidad = $em->getRepository('SiafcaIntranetBundle:Nacionalidad')->find($personaAportanteArray['persona']['nacionalidad']['id']);
            $tipoDocumento = $em->getRepository("SiafcaIntranetBundle:TipoDocumento")->findOneBy(array('codigo'=> '3'));
            $estadoCivil = $em->getRepository('SiafcaIntranetBundle:EstadoCivil')->find($personaAportanteArray['persona']['estadoCivil']['id']);
        
            $persona = new Persona();
            $persona->initialize($personaAportanteArray['persona']);
            $persona->setSexo($sexo);
            $persona->setNacionalidad($nacionalidad);
            $persona->setTipoDocumento($tipoDocumento);
            $persona->setEstadoCivil($estadoCivil);
        
            $em->persist($persona);
            $em->flush();
  
            } else {
            
                $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->find($personaAportanteArray['persona']['id']);
            }
         
            $personaAportanteArray['persona']['id'] = $persona->getId();
           
//            $aportante = $em->getRepository('SiafcaIntranetBundle:Aportante')->crearAportante($personaAportanteArray);
            $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->find($personaAportanteArray['persona']['id']);
            $descripcion = $personaAportanteArray['descripcion'];
            $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($personaAportanteArray['organismo']['id']);
            $revista = $em->getRepository('SiafcaIntranetBundle:Revista')->find($personaAportanteArray['revista']['id']);
            $cargo = $em->getRepository('SiafcaIntranetBundle:Cargo')->find($personaAportanteArray['cargo']['id']);
            $estado = $em->getRepository('SiafcaIntranetBundle:Parametrizacion')->find($personaAportanteArray['estado']['id']);
            $nroLiq = 0;
//             $nroLiq = $em->createQuery( 
//                    "SELECT count(a)
//                        FROM SiafcaIntranetBundle:Aportante a 
//                        JOIN a.organismo o
//                        JOIN a.persona p
//                        JOIN a.cargo c
//                        WHERE o.id = :orgId 
//                            AND p.id = :perId
//                            AND c.id   = :carId" )
//                    ->setParameters( array(
//                            'orgId' => $personaAportanteArray['organismo']['id'],
//                            'perId' => $personaAportanteArray['persona']['id'],
//                            'carId' => $personaAportanteArray['cargo']['id']
//                        ))->getSingleScalarResult();
//        
//        if ($nroLiq < 10) {
//            $nroLiq = '0'.$nroLiq;
//        };
    
            $aportante = new Aportante();
            $aportante->setCargo($cargo);
            $aportante->setPersona($persona);
            $aportante->setDescripcion($descripcion);
            $aportante->setOrganismo($organismo);
            $aportante->setRevista($revista);
            $aportante->setEstado($estado);
            $aportante->setFechaAlta(new DateTime('now'));
            $aportante->setNroLiq($nroLiq);

            // MANEJO DE ERRORES, POR SI LA BASE DE DATOS TIRA UN ERROR DE CONSTRAIN
            // por existir el aportante la base de datos genera un error 
            try 
            {
                $em->persist($aportante);
                $em->flush();
            } 
            catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) 
            {
                return 'APORTANTE_EXISTENTE';
            }
            return 'APORTANTE_CREADO';
        } 
        catch (Exception $ex) {
            return $ex->getMessage().$ex->getCode();
        }
    }

    /**
    * @Soap\Method("saveFirmantePersona")
    * @Soap\Param("personaFirmanteJSON", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function saveFirmantePersonaAction($personaFirmanteJSON)
        {
        $em = $this->container->get('Doctrine')->getEntityManager();
       
        $personaFirmanteArray = json_decode($personaFirmanteJSON,true);

        try{
            if (is_null($personaFirmanteArray ['persona']['id']) ) {
            $sexo = $em->getRepository('SiafcaIntranetBundle:Sexo')->find($personaFirmanteArray['persona']['sexo']['id']);
            $nacionalidad = $em->getRepository('SiafcaIntranetBundle:Nacionalidad')->find($personaFirmanteArray['persona']['nacionalidad']['id']);
            $tipoDocumento = $em->getRepository("SiafcaIntranetBundle:TipoDocumento")->findOneBy(array('codigo'=> '3'));
            $estadoCivil = $em->getRepository('SiafcaIntranetBundle:EstadoCivil')->find($personaFirmanteArray['persona']['estadoCivil']['id']);
        
            $persona = new Persona();
            $persona->initialize($personaFirmanteArray['persona']);
            $persona->setSexo($sexo);
            $persona->setNacionalidad($nacionalidad);
            $persona->setTipoDocumento($tipoDocumento);
            $persona->setEstadoCivil($estadoCivil);
        
            $em->persist($persona);
            $em->flush();
  
            } else {
            
                $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->find($personaFirmanteArray['persona']['id']);
            }
                  
            $personaFirmanteArray['persona']['id'] = $persona->getId();
            
            $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->find($personaFirmanteArray['persona']['id']);
            $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($personaFirmanteArray['organismo']['id']);
            $estado = $em->getRepository('SiafcaIntranetBundle:Estado')->find($personaFirmanteArray['estado']['id']);
            $fechaDesde = $em->getRepository('SiafcaIntranetBundle:Firmante')->getMaximaFecha($organismo->getId());
            $comentario = $personaFirmanteArray['comentario'];
            
            
            $firmante = new Firmante();
            $firmante->setPersona($persona);
            $firmante->setOrganismo($organismo);
            $firmante->setEstado($estado);
            $firmante->setComentario($comentario);
            $firmante->setFechaDesde(new DateTime($fechaDesde[0][1]));
                                  
           
            // MANEJO DE ERRORES, POR SI LA BASE DE DATOS TIRA UN ERROR DE CONSTRAIN
            // por existir el aportante la base de datos genera un error 
            try 
            {
                $em->persist($firmante);
                $em->flush();
            } 
            catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) 
            {
                return 'FIRMANTE_EXISTENTE';
            }
            return 'FIRMANTE_CREADO';
        } 
        catch (Exception $ex) {
            return $ex->getMessage().$ex->getCode();
        }
    }
    
    /**
     * @Soap\Method("saveUsuario")
     * @Soap\Param("idOrganismo", phpType = "string")
     * @Soap\Param("nombreUsuario", phpType = "string")
     * @Soap\Param("correoUsuario", phpType = "string")
     * @Soap\Param("encoded", phpType = "string")
     * @Soap\Param("salt", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function saveUsuarioAction($idOrganismo, $nombreUsuario, $correoUsuario,$encoded,$salt){
        $em = $this->container->get('Doctrine')->getEntityManager();
        $uo = $em->getRepository('SiafcaIntranetBundle:UsuarioOrganismo')->crearUsuarioOrganismo($idOrganismo, $nombreUsuario, $correoUsuario,$encoded,$salt);

        return $uo;
    }

    /**
    * @Soap\Method("getReporte")
    * @Soap\Param("liqId", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getReporteAction($liqId){
        $modulo1 = "SP_INFORME_LIQ".str_pad($liqId,10, "0", STR_PAD_LEFT);
        $modulo2 = "SP_INFORME-".str_pad($liqId,10,"0",STR_PAD_LEFT);
        $modulo3 = "SP_JUBI_DAT-".str_pad($liqId,12,"0",STR_PAD_LEFT);

        $groups = array('groups' => array('groupReporte'));
        $criteria = array('modulo' => array($modulo1,$modulo2,$modulo3));
        return $this->getByJSON("Reporte",$groups,$criteria);
    }


    /**
    * @Soap\Method("getParametrizacion")
    * @Soap\Param("clase", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getParametrizacionAction($clase)
    {
        $groups = array('groups' => array(
            "groupParametrizacion"
        ));
        $criteria = array( 'clase' => $clase );
        return $this->getByJSON('Parametrizacion', $groups, $criteria);
    }

    /**
    * @Soap\Method("validCuil")
    * @Soap\Param("cuil", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function validCuilAction($cuil)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $persona = $em->getRepository('SiafcaIntranetBundle:Persona')->findOneBy(array(
            'cuil' => $cuil
        ));
        if (!$persona){
            return true;
        }
        return false;
    }

    /**
    * @Soap\Method("cargarImportadat")
    * @Soap\Param("importadatSubido", phpType = "boolean")
    * @Soap\Param("liqId", phpType = "int")
    * @Soap\Result(phpType = "boolean")
    */
    public function cargarImportadatAction($importadatSubido,$liqId)
    {
        if (!$importadatSubido){
            return false;
        }

        $em = $this->container->get('Doctrine')->getEntityManager();
        $succes = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->cargarIMPORTADAT($liqId);

        return $succes;
    }

    /**
    * @Soap\Method("getLiquidaciones")
    * @Soap\Param("orgId", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getLiquidacionesAction($orgId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();

        $query = $em->createQuery(
            'SELECT '.
                'partial l.{id, anio, mes}, '.
                'partial e.{id, estado, nombre}, '.
                'partial tl.{id, nombre}, '.
                'partial fl.{id, nombre, codigo} '.
            'FROM SiafcaIntranetBundle:Liquidacion l '.
                'JOIN l.estado e '.
                'JOIN l.tipoLiq tl '.
                'JOIN l.fuenteLiq fl '.
            'WHERE l.organismo = ?1 '.
            'ORDER BY l.anio DESC, l.mes DESC'
        )->setParameter(1, $orgId)
        ->setHint(Query::HINT_FORCE_PARTIAL_LOAD,1);

        $entidades = $query->getResult(Query::HYDRATE_ARRAY);

        if (!$entidades) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay liquidaciones'));
        }

        $serializer = $this->container->get('serializer');
        $groups =  array('groups' => array(
            "groupLiquidacion", "groupParametrizacion", "groupEstado", "groupLiquidacion2", "groupItem",
            "groupConceptoItem", "groupConjuntoControl", "groupControl", "groupAportante", "groupConcepto",
            "groupPersona2", "groupCargo"
        ));
        $jsonContent = $serializer->serialize($entidades, 'json', $groups);
        return $jsonContent;
    }
  
    /**
     * @Soap\Method("getAportanteToShow")
     * @Soap\Param("apoId", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getAportanteToShowAction($apoId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $aportante = $em->getRepository("SiafcaIntranetBundle:Aportante")->find($apoId);

        if (!$aportante) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No existe el aportante'));
        }

        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportante, 'json', array('groups' => array(
            "groupAportante",
            "groupPersona1",
            "groupParametrizacion",
            "groupNacionalidad",
            "groupEstadoCivil",
            "groupRevista",
            "groupOrganismo",
            "groupCargo",
            "groupTipoDocumento",
            "groupSexo"
        )));

        return $jsonContent;
    }
    
           
     /**
     * @Soap\Method("getCargoToShow")
     * @Soap\Param("carId", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getCargoToShowAction($carId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $cargo = $em->getRepository("SiafcaIntranetBundle:Cargo")->find($carId);

        if (!$cargo) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No existe el cargo'));
        }

        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($cargo, 'json', array('groups' => array(
            'groupCargo','groupEstado',
        )));

        return $jsonContent;
    }
    
    
    /**
    * @Soap\Method("getLiquidacionesConDatos")
    * @Soap\Param("orgId", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getLiquidacionesConDatosAction($orgId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();

        $query = $em->createQuery(
            'SELECT '.
                'partial l.{id, mes, anio}, '.
                'partial e.{id, estado, nombre}, '.
                'partial tl.{id, nombre}, '.
                'partial fl.{id, nombre, codigo}, '.
                'partial i.{id}, '.
                'partial ci.{id, importe }, '.
                'partial co.{id, nombre }, '.
                'partial a.{id, nroLiq}, '.
                'partial p.{id, cuil, nombre, apellidoMat, apellidoPat}, '.
                'partial c.{id, nombre} '.
            'FROM SiafcaIntranetBundle:Liquidacion l '.
                'JOIN l.estado e '.
                'JOIN l.tipoLiq tl '.
                'JOIN l.fuenteLiq fl '.
                'JOIN l.items i '.
                'JOIN i.aportante a '.
                'JOIN i.conceptosItem ci '.
                'JOIN ci.concepto co '.
                'JOIN a.persona p '.
                'JOIN a.cargo c '.
            'WHERE l.organismo = ?1 '.
                "AND e.estado != '00' ".
            'ORDER BY l.anio DESC, l.mes DESC'
        )->setParameter(1, $orgId);

        $entidades = $query->getResult(Query::HYDRATE_ARRAY);

        if (!$entidades) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay liquidaciones'));
        }

        $serializer = $this->container->get('serializer');
        $groups =  array('groups' => array(
            "groupLiquidacion", "groupParametrizacion", "groupEstado", "groupLiquidacion2", "groupItem",
            "groupConceptoItem", "groupConjuntoControl", "groupControl", "groupAportante", "groupConcepto",
            "groupPersona2", "groupCargo"
        ));
        $jsonContent = $serializer->serialize($entidades, 'json', $groups);
        return $jsonContent;
    }

    /**
    * @Soap\Method("getLiquidacion")
    * @Soap\Param("liqId", phpType = "int")
    * @Soap\Param("orgId", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getLiquidacionAction($liqId, $orgId)
    {
        $criteriaArray = array(
            'id' => $liqId,
            'organismo' => $orgId
        );
        $criteria = json_encode($criteriaArray);
        return $this->getLiquidacionByAction($criteria);
    }

    /**
    * @Soap\Method("getLiquidacion910")
    * @Soap\Param("liqId", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getLiquidacion910($liqId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $entidades = $em->getRepository("SiafcaIntranetBundle:Liquidacion")->getLiquidacion910($liqId);

        if (!$entidades) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay liquidaciones'));
        }

        $jsonContent = json_encode($entidades);

        return $jsonContent;
    }

    /**
     * @Soap\Method("getLiquidacionToShow")
     * @Soap\Param("liqId", phpType = "int")
     * @Soap\Param("orgId", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getLiquidacionToShowAction($liqId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $entidades = $em->getRepository("SiafcaIntranetBundle:Liquidacion")->showData($liqId);
        
        if (!$entidades) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay liquidaciones'));
        }

        $jsonContent = json_encode($entidades);

        return $jsonContent;
    }

   
    /**
    * @Soap\Method("getLiquidacionBy")
    * @Soap\Param("criteria", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getLiquidacionByAction($criteria)
    {
        $criteriaArray = json_decode($criteria, true);
        $groups = array('groups' => array(
            "groupLiquidacion", "groupParametrizacion", "groupEstado", "groupLiquidacion2", "groupItem",
            "groupConceptoItem", "groupConjuntoControl", "groupControl", "groupAportante", "groupConcepto",
            "groupPersona2", "groupCargo"
        ));

        return $this->getOneByJSON('Liquidacion', $groups, $criteriaArray);
    }

    /**
    * @Soap\Method("existeOtraLiqOriginal")
    * @Soap\Param("orgId", phpType = "int")
    * @Soap\Param("mes", phpType = "string")
    * @Soap\Param("anio", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function existeOtraLiqOriginalAction($orgId, $mes, $anio)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $repository = $em->getRepository('SiafcaIntranetBundle:Liquidacion');
        $liquidacion = $repository->findBy(array(
            'organismo' => $orgId,
            'mes' => $mes,
            'anio' => $anio,
            'tipoLiq' => array(221, 223), // Original normal o Original S.A.C
        ), array(), 1, 0);

        return (bool) $liquidacion;
    }

    /**
    * @Soap\Method("saveLiquidacion")
    * @Soap\Param("liquidacionJSON", phpType = "string")
    * @Soap\Result(phpType = "int")
    */
   public function saveLiquidacionAction($liquidacionJSON)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $repository = $em->getRepository('SiafcaIntranetBundle:Liquidacion');
        $liqArray = json_decode($liquidacionJSON,true);
        $liquidacion = $repository->crearLiquidacion($liqArray);
        try {
            $em->persist($liquidacion);
            $em->flush();

            // Inicializo el campo CLOB de la tabla (SQL Naivo)
            $db = $em->getConnection();
            $query = "update liquidacion set importa_dat = EMPTY_CLOB() where id = " . $liquidacion->getId();
            $stmt = $db->prepare($query);
            $params = array();
            $stmt->execute($params);
        } catch (Exception $ex) {
            return 0;
        }

        return $liquidacion->getId();
    }

    /**
    * @Soap\Method("getConceptos")
    * @Soap\Param("obligatorio", phpType = "boolean")
    * @Soap\Result(phpType = "string")
    */
    public function getConceptosAction($obligatorio)
    {
        $groups = array('groups' => array("groupConcepto", "groupExpteAmpItemConcepto"));
        $criteria = array('obligatorio' => $obligatorio);
        return $this->getByJSON('Concepto', $groups, $criteria);
    }
     
    /**
     * @Soap\Method("getPersonaByCuil")
     * @Soap\Param("cuil", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function getPersonaByCuilAction($cuil)
    {
        $criteria = array(
            'cuil' => $cuil
        );
        $groups =  array('groups' => array(
            "groupPersona1", "groupSexo", "groupTipoDocumento", "groupNacionalidad"
        ));
        return $this->getOneByJSON('Persona', $groups, $criteria);
    }

        /**
     * @Soap\Method("getPersonaId")
     * @Soap\Param("id", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function getPersonaIdAction($id)
    {
        $criteria = array('id' => $id);
        $groups =  array('groups' => array(
            "groupPersona1", "groupPersona2", "groupSexo", "groupTipoDocumento", "groupNacionalidad"
        ));
        return $this->getOneByJSON('Persona', $groups, $criteria);
    }
    
   
    /**
    * @Soap\Method("getAportantesFaltantesEnLiquidacion")
    * @Soap\Param("orgID", phpType = "string")
    * @Soap\Param("aportantesIDsJSON", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getAportantesFaltantesEnLiquidacionAction($orgID,$aportantesIDsJSON)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find($orgID);
        $ids = json_decode($aportantesIDsJSON);
        $aportantes = $organismo->getAportantesFaltantes($ids);
        if (!$aportantes)
        {
            return '';
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($aportantes, 'json', array('groups' => array(
            "groupAportante","groupPersona2","groupRevista","groupCargo"
        )));
        return $jsonContent;

    }

    /**
    * @Soap\Method("saveItems")
    * @Soap\Param("itemsJSON", phpType = "string")
    * @Soap\Param("liqID", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function saveItemsAction($itemsJSON, $liqID)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $itemsArray = json_decode($itemsJSON,true);
        try{
            $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($liqID);
            foreach ($itemsArray as $itemArray) {
                $item = $em->getRepository('SiafcaIntranetBundle:Item')->updateOrCreateItem($itemArray);
                $liquidacion->addItem($item);
            }
            $liquidacion->datosCargados();
            $em->persist($liquidacion);
            $em->flush();

            $control = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->controlarLiquidacion($liquidacion->getId());
            if ($control == 0) {
                return true;
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    /**
    * @Soap\Method("deleteFirmante")
    * @Soap\Param("id", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function deleteFirmanteAction($id)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        try {
            $firmante = $em->getRepository('SiafcaIntranetBundle:Firmante')->find($id);
            $em->remove($firmante);
            $em->flush();
            
             } catch (Exception $ex) {
            return false;
        }
        return true;
    }
    
    /**
    * @Soap\Method("borrarCargo")
    * @Soap\Param("carID", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function borrarCargoAction($carID)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        try {
            $cargo = $em->getRepository('SiafcaIntranetBundle:Cargo')->find($carID);
            $em->remove($cargo);
            $em->flush();
            
             } catch (Exception $ex) {
            return false;
        }
        return true;
    }
    
    
/**
    * @Soap\Method("getCargoId")
    * @Soap\Param("idCargo", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getCargoIdAction($idCargo)
    { 
        $criteria = array(
            'id' => $idCargo
        );
        $groups =  array('groups' => array("groupCargo","groupSectorPasivo", "groupParametrizacion", "groupTipoOrganismo"));
        return $this->getOneByJSON('Cargo', $groups, $criteria);
       }    
    
    /**
    * @Soap\Method("borrarDatosLiquidacion")
    * @Soap\Param("liqID", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function borrarDatosLiquidacionAction($liqID)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        try {
            $succes = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->borrarDatos($liqID);
            if ($succes == 0) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
    * @Soap\Method("updateItems")
    * @Soap\Param("itemsJSON", phpType = "string")
    * @Soap\Param("liqID", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function updateItemsAction($itemsJSON, $liqID)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $itemsArray = json_decode($itemsJSON,true);
        try{
            $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($liqID);
            $items = new ArrayCollection();
            foreach ($itemsArray as $itemArray) {
                $item = $em->getRepository('SiafcaIntranetBundle:Item')->updateOrCreateItem($itemArray);
                $items->add($item);
            }
            $liquidacion->updateItems($items);
            $em->getRepository('SiafcaIntranetBundle:Liquidacion')->removeExtraItems($liquidacion);
            $em->persist($liquidacion);
            $em->flush();

            $control = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->controlarLiquidacion($liquidacion->getId());
            if ($control == 0) {
                return true;
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     *
     * @param string $entity
     * @param array $groups
     * @param array $criteria
     * @return string
     * @throws SoapFault
     */
    private function getOneByJSON($entity, $groups, $criteria)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $entidades = $em->getRepository('SiafcaIntranetBundle:'.$entity)->findOneBy($criteria);
        if (!$entidades)
        {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay '.$entity));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($entidades, 'json', $groups);
        return $jsonContent;
    }

    /**
     *
     * @param string $entity
     * @param array $groups
     * @param array $criteria
     * @return string
     * @throws SoapFault
     */
    private function getByJSON($entity, $groups, $criteria, $order = null)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $entidades = $em->getRepository('SiafcaIntranetBundle:'.$entity)->findBy($criteria, $order);

        if (!$entidades)
        {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay '.$entity));
        }
        $serializer = $this->container->get('serializer');
        $jsonContent = $serializer->serialize($entidades, 'json', $groups);
        return $jsonContent;
    }

    /**
    * @Soap\Method("getInforme")
    * @Soap\Param("liqID", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getInformeAction($liqID)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $successInforme = $em->getRepository('SiafcaIntranetBundle:Informe')->generarInforme($liqID);
        if ($successInforme == 0) {
            throw new SoapFault('FAIL SF_WS_INFORME', sprintf('Falló generación de informe'));
        }
        $groups = array('groups' => array(
            "groupInforme", "groupInformeItem", "groupConcepto"
        ));
        $criteria = array('liquidacion' => $liqID);
        return $this->getOneByJSON('Informe', $groups, $criteria);
    }

    /**
    * @Soap\Method("copyLiquidacion")
    * @Soap\Param("idLiqSinDatos", phpType = "string")
    * @Soap\Param("idLiqConDatos", phpType = "string")
    * @Soap\Result(phpType = "boolean")
    */
    public function copyLiquidacionAction($idLiqSinDatos, $idLiqConDatos)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        try{
            $liquidacionVacia = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($idLiqSinDatos);
            $liquidacionDatos = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($idLiqConDatos);
            $liquidacionVacia->copyData($liquidacionDatos);
            $liquidacionVacia->datosCargados();
            $em->persist($liquidacionVacia);
            $em->flush();

            $control = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->controlarLiquidacion($liquidacionVacia->getId());
            if ($control == 0) {
                return true;
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
    * @Soap\Method("getConjuntoControl")
    * @Soap\Param("liqID", phpType = "string")
    * @Soap\Result(phpType = "string")
    */
    public function getConjuntoControlAction($liqID)
    {
        $groups = array('groups' => array(
            "groupConjuntoControl", "groupParametrizacion", "groupControl", "groupConceptoItem", "groupConcepto",
            "groupConceptoItem1", "groupItem1", "groupAportante", "groupPersona1", "groupCargo"
        ));
        $criteria = array('liquidacion' => $liqID);
        return $this->getOneByJSON('ConjuntoControl', $groups, $criteria);
    }

    /**
     * @Soap\Method("presentarLiquidacion")
     * @Soap\Param("idLiq", phpType = "string")
     * @Soap\Param("idUsr", phpType = "string")
     * @Soap\Result(phpType = "boolean")
     */
    public function presentarLiquidacionAction($idLiq, $idUsr)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find($idLiq);
        $usuario = $em->getRepository('SiafcaIntranetBundle:Usuario')->find($idUsr);
        $succes = $liquidacion->presentar($usuario);
        if ($succes) {
            $em->persist($liquidacion);
            $em->flush();
        }

        return $succes;
    }

    /**
    * @Soap\Method("getAmparo")
    * @Soap\Param("aportanteId", phpType = "int")
    * @Soap\Param("conceptoId", phpType = "int")
    * @Soap\Result(phpType = "string")
    */
    public function getAmparoAction($aportanteId, $conceptoId)
    {

        $response = array("error" => true, "porcentaje" => 0);
        $em = $this->container->get('Doctrine')->getEntityManager();

        $dql = 'SELECT c.porcentaje
                FROM SiafcaIntranetBundle:Aportante a
                JOIN a.amparos e
                JOIN e.itemConceptos c
                WHERE a.id = ?1
                AND c.concepto = ?2';

        try {

            $result = $em->CreateQuery($dql)
                         ->setParameters(array(
                             1 => $aportanteId,
                             2 => $conceptoId))
                         ->getSingleResult();
            $response['error'] = false;
            $response['porcentaje'] = $result['porcentaje'];
        } catch (NonUniqueResultException $e) {
            $response['error'] = true;
            $response['msg'] = "No se puede encontrar un único porcentaje";
        } catch (\Doctrine\Orm\NoResultException $e) {
            $response['error'] = false;
            $response['porcentaje'] = 0;
        } catch (Exception $e) {
            $response['error'] = true;
            $response['msg'] = $e->getMessage();
        }

//        $result = $em->CreateQuery($dql)
//                     ->setParameters(array(
//                         1 => $aportanteId,
//                         2 => $conceptoId))
//                     ->getArrayResult();
//        if (is_array($result) && count($result) == 1) {
//            $response['error'] = false;
//            $response['porcentaje'] = $result['porcentaje'];
//        } elseif (is_array($result) && count($result) > 1) {
//            $response['error'] = true;
//            $response['msg'] = "No se puede encontrar un único porcentaje (".$aportanteId.", ".$conceptoId.")";
//        } elseif (is_array($result) || count($result) == 0) {
//            $response['error'] = true;
//            $response['msg'] = "No hay entradas para los datos insertados (".$aportanteId.", ".$conceptoId.")";
//        } elseif (!is_array($result)) {
//            $response['error'] = true;
//            $response['msg'] = "Error al consultar porcentaje (".$aportanteId.", ".$conceptoId.")";
//        }

        return json_encode($response);
    }

    /**
     * @Soap\Method("getAmparos")
     * @Soap\Param("orgId", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getAmparosAction($orgId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();

        $entidades = $em->CreateQuery(
            'SELECT '
                . 'a.id aportante_id, '
                . 'ic.porcentaje amparo_porcentaje, '
                . 'c.id amparo_conceptoid '
            . 'FROM SiafcaIntranetBundle:ExpteAmpItem eai '
                . 'JOIN eai.aportante a '
                . 'JOIN a.organismo o '
                . 'JOIN eai.itemConceptos ic '
                . 'JOIN ic.concepto c '
            . 'WHERE o.id = ?1 '
                . 'AND ic.fechaFin >= :today '
                . 'AND ic.fechaInicio <= :today '
        )->setParameters(array(1 => $orgId, 'today' => (new \DateTime())->format('d/M/y')))
                ->getResult(Query::HYDRATE_ARRAY);

        if (!$entidades) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay amparos'));
        }

        $o = array();
        foreach ($entidades as $entidad) {
            $o[$entidad['aportante_id']][$entidad['amparo_conceptoid']] = $entidad['amparo_porcentaje'];
        }

        return json_encode($o);
    }

    /**
     * @Soap\Method("getOrganismoMails")
     * @Soap\Param("organismoId", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getOrganismoMails($organismoId)
    {
        $response = array("error" => true, "mails" => array());
        $em = $this->container->get('Doctrine')->getEntityManager();
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->findOneById($organismoId);


        $response['error'] = false;
        $response['mails'] = array(
            'organismo' => $organismo->getCorreos(),
            'responsable' => $organismo->getUsuarioResponsableMail(),
        );

        return json_encode($response);
    }

    /**
     * Cambia el estado de una liquidacion y guarda la modificacion
     * @Soap\Method("aplicarTransicion")
     * @Soap\Param("idLiq", phpType = "string")
     * @Soap\Param("transition", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function aplicarTransicionAction($idLiq, $transition)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->findOneBy(array('id' => $idLiq));

        if ($liquidacion) {
            $response['error'] = !$liquidacion->aplicarTransicion($transition);
            $em->flush();
        } else {
            $response['error'] = true;
        }

        return json_encode($response);
    }

    /**
     * @Soap\Method("getLiquidacionCargaManual")
     * @Soap\Param("liqId", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getLiquidacionCargaManual($liqId)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $entidades = $em->getRepository("SiafcaIntranetBundle:Liquidacion")->getLiquidacionCargaManual($liqId);

        if (!$entidades) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay liquidaciones'));
        }

        $jsonContent = json_encode($entidades);

        return $jsonContent;
    }

    /**
     * @Soap\Method("borrarItemLiquidacion")
     * @Soap\Param("idItem", phpType = "int")
     * @Soap\Result(phpType = "boolean")
     */
    public function borrarItemLiquidacion($idItem)
    {
        $success = true;
        $em = $this->container->get('Doctrine')->getEntityManager();
        $item = $em->getRepository("SiafcaIntranetBundle:Item")->find($idItem);

        if (!$item) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No se encontró el item'));
        }

        $em->remove($item);

        try {
            $em->flush();
        } catch (\Exception $ex) {
            $success = false;
        }

        return $success;
    }

    /**
     * @Soap\Method("getResumenPeriodos")
     * @Soap\Param("cuilAportante", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function getResumenPeriodos($cuilAportante)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $periodos = $em->getRepository("SiafcaIntranetBundle:Aportante")->getResumenPeriodos($cuilAportante);

        $jsonContent = json_encode($periodos);

        return $jsonContent;
    }
    
     /**
     * @Soap\Method("getLiquidacionAportante")
     * @Soap\Param("idLiquidacion", phpType = "int")
      *@Soap\Param("page", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getLiquidacionAportanteAction($idLiquidacion, $page)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        $offset = ($page * 20) - 20;
        $limite = 20;
        $liquidacionWS = $em->getRepository("SiafcaIntranetBundle:Liquidacion")->showDatosAportantes($idLiquidacion, $offset, $limite);
        if (!$liquidacionWS) {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf('No hay aportantes para esta liquidacion'));
        }
        $jsonContent = json_encode($liquidacionWS);

        return $jsonContent;
    }
    
      /**
     * @Soap\Method("getLiqAportanteConceptos")
     * @Soap\Param("idLiq", phpType = "int")
      *@Soap\Param("idItem", phpType = "int")
     * @Soap\Result(phpType = "string")
     */
    public function getLiqAportanteConceptosAction($idLiq, $idItem)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
       
        $conceptosWS = $em->getRepository("SiafcaIntranetBundle:Liquidacion")->conceptoItem($idLiq, $idItem);
        
        $jsonContent = json_encode($conceptosWS);

        return $jsonContent;
    }
    
    
    
     /**
     * @Soap\Method("getAnioLiquidacion")
     * @Soap\Param("idOrganismo", phpType = "int")
      *@Soap\Param("anio", phpType = "string")
     * @Soap\Result(phpType = "string")
     */
    public function  getAnioLiquidacion($idOrganismo,$anio)
    {
        $em = $this->container->get('Doctrine')->getEntityManager();
        
        $liquidacionesWs = $em->getRepository("SiafcaIntranetBundle:Liquidacion")->liquidacionAnio($idOrganismo, $anio);//funciona
        
        if (count($liquidacionesWs) == 0) 
        {
            throw new SoapFault('ENTIDAD_NOT_FOUND', sprintf(count($liquidacionesWs)));
        }

        $serializer = $this->container->get('serializer');
        $groups =  
        array('groups' => array(
            "groupLiquidacion", "groupParametrizacion", "groupEstado", "groupLiquidacion2", "groupItem",
            "groupConceptoItem", "groupConjuntoControl", "groupControl", "groupAportante", "groupConcepto",
            "groupPersona2", "groupCargo"
        ));
        $jsonContent = $serializer->serialize($liquidacionesWs, 'json', $groups);
        return $jsonContent;
    }
}
