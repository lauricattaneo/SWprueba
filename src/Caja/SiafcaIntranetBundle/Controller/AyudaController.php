<?php

namespace Caja\SiafcaIntranetBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Caja\SiafcaIntranetBundle\Entity\Ayuda;

/**
 * Ayuda controller.
 * AYUDA:
 * IntraAyuIndex - indexAction
 * IntraAyuShow  - showAction
 * IntraAyuEdit  - editAction
 * IntraAyuNew  -  newAction
 * 
 */

/**
 * Admin controller.
 *
 */
class AyudaController extends Controller
{
    
    /**
     * 
     * @return array
     */
    private function getAyudasSearchFormFilterAttrs()
    {
        $formAttrs = array(
            array(
                'type' => 'TextType',
                'name' => 'codigo',
                'placeholder' => 'Código de Ayuda',
                'label' => false,
                'dql' => " WHERE a.codigo LIKE '",
                'subtitulo' => 'Ayudas con el Código '
            ),
            array(
                'type' => 'SubmitType',
                'name' => 'buscar',
                'class' => 'btn-primary',
                'label' => 'Buscar',
                'dql' => "",
                'subtitulo' => ''
            )
        );
        
        
        return $formAttrs;
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\Form $searchForm
     * @return array
     */
    private function getAyudasFilterResults(Form $searchForm, $subtitulo)
    {
        $formFilter = $this->get('form.filter');
        $defaultSubtitulo = $subtitulo;
        $form = null;
        $isButtonForm = true;
        $formAttrs = null;
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $form = $searchForm;
            $isButtonForm = false;
            $formAttrs = $this->getAyudasSearchFormFilterAttrs();
        }
        
        $filters = $formFilter->getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo);
        
        return $filters;
    }

    public function indexAction(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'No tiene los permisos necesarios');

        $em = $this->getDoctrine()->getManager();   

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraAyuIndex'));
        
        $formFilter = $this->get('form.filter');
        //var_dump($formFilter); die;
        
        $route = $this->generateUrl('ayudas');
        $dql   = "SELECT a FROM SiafcaIntranetBundle:Ayuda a";
        $searchForm = $formFilter->createFilterForm($route ,$this->getAyudasSearchFormFilterAttrs(), 'searchForm') ;
        $searchForm->handleRequest($request);
        $filters = $this->getAyudasFilterResults( $searchForm, 'Ayudas encontradas');
        //var_dump($filters); die;
        $subtitulo = $filters['subtitulo'];
        $dql .= $filters['dql'];
        $query = $em->createQuery($dql);
        //var_dump($query); die;
        $paginator  = $this->get('knp_paginator');
        $ayudas = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/,
            array('defaultSortFieldName' => 'a.codigo', 'defaultSortDirection' => 'asc')
        );

        return $this->render('ayuda/indexAyudas.html.twig', array(
            'ayudas' => $ayudas,
            'titulo' => 'Ayudas',
            'subtitulo' => $subtitulo,
            'searchForm' => $searchForm->createView(),
            'ayuda' => $ayuda,
            
        ));        
        
    }
    
    public function newAction(Request $request) {
        $ayudaNew = new Ayuda();
        $form = $this->createForm('Caja\SiafcaIntranetBundle\Form\AyudaType', $ayudaNew);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraAyuNew'));


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ayudaNew);
            $em->flush();

            return $this->redirectToRoute('ayuda_show', array('id' => $ayudaNew->getId()));
        }

        return $this->render('ayuda/new.html.twig', array(
                    'ayudaNew' => $ayudaNew,
                    'form' => $form->createView(),
                    'ayuda' => $ayuda
        ));
    }    
    
    public function showAction(Ayuda $ayudaNew)
    {
        // The ParamConverter automatically queries for an object whose $id property matches the {id} value. 
        // It will also show a 404 page if no Post can be found.

        $em = $this->getDoctrine()->getManager();
        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraAyuShow'));
        
        return $this->render('ayuda/show.html.twig', array(
            'ayudaNew' => $ayudaNew,
            'ayuda' => $ayuda,
        ));
        
    }
    
    private function createDeleteForm(Ayuda $ayudaNew)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ayuda_delete', array('id' => $ayudaNew->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    public function deleteAction(Request $request, Ayuda $ayudaNew)
    {
        $form = $this->createDeleteForm($ayudaNew);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ayudaNew);
            $em->flush();
        }

        return $this->redirectToRoute('ayuda_index');
    }
    
    public function editAction(Request $request, Ayuda $ayudaNew)
    {
        $deleteForm = $this->createDeleteForm($ayudaNew);
        $editForm = $this->createForm('Caja\SiafcaIntranetBundle\Form\AyudaType', $ayudaNew);
        $editForm->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        $ayuda = $em->getRepository('SiafcaIntranetBundle:Ayuda')->findOneBy(array('codigo' => 'IntraAyuEdit'));

        
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ayudaNew);
            $em->flush();

            return $this->redirectToRoute('ayuda_show', array('id' => $ayudaNew->getId()));
        }

        return $this->render('ayuda/edit.html.twig', array(
            'ayudaNew' => $ayudaNew,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ayuda' => $ayuda,
        ));
    }
}
