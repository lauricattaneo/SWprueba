<?php

namespace Caja\SiafcaIntranetWsBundle\Controller;

use Caja\SiafcaIntranetWsBundle\Classes\Person;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use DateTime;
use Caja\SiafcaIntranetBundle\Entity\Organismo;
use Doctrine\Common\Collections\ArrayCollection;
use Caja\SiafcaIntranetWsBundle\Services\WebService as ws;

class WebServiceController extends Controller
{                
    public function helloAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        // The client service name is `besimple.soap.client.demoapi`:
        // `besimple.soap.client.`: is the base name of your client
        // `demoapi`: is the name specified in your config file converted to lowercase
        $client = $this->container->get('besimple.soap.client.siafcaapi');
        $organismo = $em->getRepository('SiafcaIntranetBundle:Organismo')->find(1534);
        
        $serializer = $this->get('serializer');
        $itemsArray = json_decode('[{"id":616322,"aportante":{"id":65641},"liquidacion":{"id":221},"conceptosItem":[{"id":1220727,"concepto":{"id":2},"importe":5.8,"porcentaje":null},{"id":1220728,"concepto":{"id":3},"importe":6.88,"porcentaje":null},{"id":1220729,"concepto":{"id":61},"importe":40,"porcentaje":null},{"id":1220730,"concepto":{"id":62},"importe":0,"porcentaje":null},{"id":null,"concepto":{"id":43},"importe":90,"porcentaje":null}]},{"id":616323,"aportante":{"id":65642},"liquidacion":{"id":221},"conceptosItem":[{"id":1220731,"concepto":{"id":2},"importe":7.25,"porcentaje":null},{"id":1220732,"concepto":{"id":3},"importe":8.6,"porcentaje":null},{"id":1220733,"concepto":{"id":61},"importe":50,"porcentaje":null},{"id":1220734,"concepto":{"id":62},"importe":0,"porcentaje":null}]}]',true);
        $liquidacion = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->find('221');
        $items = new ArrayCollection();
        foreach ($itemsArray as $itemArray) {
            $item = $em->getRepository('SiafcaIntranetBundle:Item')->updateItem($itemArray);
            $items->add($item);
        }
        $liquidacion->updateItems($items);
        $em->getRepository('SiafcaIntranetBundle:Liquidacion')->removeExtraItems($liquidacion);
        $em->persist($liquidacion);
        $em->flush();

        $control = $em->getRepository('SiafcaIntranetBundle:Liquidacion')->controlarLiquidacion($liquidacion->getId());
        
        dump($liquidacion);
        dump($control);
        die();
    }
    
    public function goodbyeAction($name)
    {
        // The client service name is `besimple.soap.client.demoapi`:
        // `besimple.soap.client.`: is the base name of your client
        // `demoapi`: is the name specified in your config file converted to lowercase
        $client = $this->container->get('besimple.soap.client.siafcaapi');

        // call `hello` method on WebService with the string parameter `$name`
        $helloResult = $client->goodbye($name);

        return $this->render('::basebesimple.html.twig', array(
            'usuario' => $helloResult,
        ));
    }
    
    public function usuarioAction($name)
    {
        $serializer = $this->get('serializer');
        
        $client = $this->container->get('besimple.soap.client.siafcaapi');
        $em = $this->getDoctrine()->getManager();
        
        $json = '{"anio":"2016","mes":"03","titulo":"LIK PRUEBA","descripcion":"PRUEBA","claseLiq":{"id":121},"fuenteLiq":{"id":101},"tipoLiq":{"id":123},"origenLiq":null,"organismo":{"id":1533}}';
        $liqArray = json_decode($json,true);
        $repository = $em->getRepository('SiafcaIntranetBundle:Liquidacion');
        $liquidacion = $repository->crearLiquidacion($liqArray);
        dump($liquidacion);
        $em->persist($liquidacion);
        $em->flush();
        die();

        
        return $this->render('::basebesimple.html.twig');
    }
    
    public function organismosAction($usrId)
    {
         
        $client = $this->container->get('besimple.soap.client.siafcaapi');
        $organismos = $client->getOrganismos($usrId);
        $serializer = $this->get('serializer');

        $des = $serializer->deserialize($organismos, 'Caja\SiafcaIntranetBundle\Entity\Organismo[]', 'json');
        dump($des);die();
              
        return $this->render('::basebesimple.html.twig', array(
            'organismos'=> $des,
        ));
        
    }
    
    public function liquidacionesAction($orgId)
    {
        $WebSer = new ws();
        $liquidaciones = $WebSer->getLiquidacionesAction($orgId);
        var_dump($liquidaciones); die;
        
    }
}
