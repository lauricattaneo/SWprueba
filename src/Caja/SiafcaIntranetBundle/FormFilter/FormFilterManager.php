<?php

namespace Caja\SiafcaIntranetBundle\FormFilter;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactory;

/**
 * Servicio para crear formularios de filtros, y filtrar resultados
 *
 */
class FormFilterManager
{
    /**
     *
     * @var FormFactory
     */
    private $formFactory;
    
    public function __construct(FormFactory $formFactory) 
    {
        $this->formFactory = $formFactory;
    }
    
    /**
     * Formulario para filtrar colecciones
     * 
     * @param string $route
     * @param array $formNodes
     * @param string $formName || null
     * @return \Symfony\Component\Form\Form
     */
    public function createFilterForm($route, $formNodes, $formName = null) 
    {
        $formBuilder = $this->formFactory->createNamedBuilder(
                $formName,
                'Symfony\Component\Form\Extension\Core\Type\FormType',
                null,
                array('allow_extra_fields' => true)
            )
            ->setAction($route)
            ->setMethod('GET')
        ;

        foreach ($formNodes as $node ) {
            
            $this->processNode($formBuilder, $node);
        }
        
        
        $form = $formBuilder->getForm();
        
        return $form;
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\Form $form
     * @param boolean $isButtonForm
     * @param array $formAttrs
     * @param string $defaultSubtitulo
     * @param string $defaultDQL
     * @return array
     */
    public function getFilterResults($form, $isButtonForm, $formAttrs, $defaultSubtitulo = '', $defaultDQL = '')
    {
        $filter = array();
        // Default Values
        $filter['dql'] = $defaultDQL;
        $filter['subtitulo'] = $defaultSubtitulo;
        if (!$form) { return $filter; } // Si $form es null devolver los valores por defecto
        $clickedButton = $form->getClickedButton();

        if ($isButtonForm) {
            $name = $clickedButton->getName();
            $attr = $this->findAttr($formAttrs, $name);
            $filter['dql'] = $attr['dql'];
            $filter['subtitulo'] = $attr['subtitulo'];
        } else {
            /**
             * @var FormInterface $textChild
             */
            $textChild = $this->findTextTypeChild($form);
            $name = $textChild->getConfig()->getName();
            $data = $textChild->getData();
            $attr = $this->findAttr($formAttrs, $name);
            $filter['dql'] = $attr['dql']. $data . "'"; // $dql = WHERE attribute LIKE ' $data '
            $filter['subtitulo'] = $attr['subtitulo']. $data;
        }
        
        
        return $filter;
    }
    
    /**
     * 
     * @param Symfony\Component\Form\FormBuilder $formBuilder
     * @param array $node
     */
    private function processNode($formBuilder, $node)
    {
        $type = $this->getFullTypeName($node['type']);
        $attr = $this->getAttributes($node);
        
        $formBuilder->add($node['name'], $type, array(
            'attr' => $attr,
            'label' => $node['label']
        ));
        
    }
    
    /**
     * 
     * @param string $type
     * @return string
     */
    private function getFullTypeName($type)
    {
        $fullTypeName = null;
        switch ($type) {
            case 'SubmitType':
                $fullTypeName = 'Symfony\Component\Form\Extension\Core\Type\SubmitType';
                break;
            case 'TextType':
                $fullTypeName = 'Symfony\Component\Form\Extension\Core\Type\TextType';
                break;
        }
        
        return $fullTypeName;
    }
    
    /**
     * 
     * @param array $node
     * @return array
     */
    private function getAttributes($node)
    {
        $attr = array();
        switch ($node['type']) {
            case 'SubmitType':
                $attr['class'] = 'btn '.$node['class'];
                break;
            case 'TextType':
                $attr['placeholder'] = $node['placeholder'];
                break;
        }
        
        return $attr;
    }
    
    /**
     * 
     * @param array $formAttrs
     * @param string $name
     * @return array || null
     */
    private function findAttr($formAttrs, $name) 
    {
        foreach ($formAttrs as $attr) {
            if ($attr['name'] == $name) {
                return $attr;
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @param Form $form
     * @return FormInterface || null
     */
    private function findTextTypeChild(Form $form) 
    {
        foreach ($form->all() as $child) {
            if ($child->getConfig()->getType()->getName() == 'text') { // Si es nodo de tipo Texto

                return $child;
            }
        }
        
        return null;
    }
}
