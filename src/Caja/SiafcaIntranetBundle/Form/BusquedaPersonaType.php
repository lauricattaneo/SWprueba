<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;



class BusquedaPersonaType extends AbstractType
{
    /**
     * @var int
     */
        
    public function __construct() 
    {
     
        
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('busqueda','Symfony\Component\Form\Extension\Core\Type\ChoiceType',array(
                'placeholder' => '-- Seleccione su busqueda --',
                'required' => true,
                'attr' => array ('class' => 'form-control'),
                'label' => ' ',
                'choices' => array(
                    'APELLIDO Y NOMBRE' => 'apellido',
                    'DNI' => 'documento',
                    'CUIL' => 'cuil',
                    ),
                'choices_as_values' => true,
                ))
                
               ->add('campoCuil', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'attr' => array('placeholder' => 'C.U.I.L. de la Persona'),
                    'label' => false,
                    'mapped' => false,
                    'constraints' => array(
                      //  new Assert\NotBlank(array('message' => "El CUIL no puede estar en blanco")),
                        new Assert\Length(array('min' => 11, 'max' => 11, 'exactMessage' => "El C.U.I.L. debe tener 11 caracteres.")),
                        new Assert\Regex(array('pattern' => "/^[0-9]*$/", 'message' => "El C.U.I.L. debe contener sólo números"))
                    ),
                ))
                ->add('campoDni', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'attr' => array('placeholder' => 'D.N.I. de la Persona'),
                    'label' => false,
                    'mapped' => false,
                    'constraints' => array(
                        //new Assert\NotBlank(array('message' => "El DNI no puede estar en blanco")),
                        new Assert\Length(array('min' => 8, 'max' => 8, 'exactMessage' => "El D.N.I. debe tener 8 caracteres.")),
                        new Assert\Regex(array('pattern' => "/^[0-9]*$/", 'message' => "El D.N.I. debe contener sólo números"))
                    ),
                ))
                ->add('campoApellido', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'attr' => array('placeholder' => 'Apellido de la Persona'),
                    'label' => false,
                    'mapped' => false,
                    'constraints' => array(
                        //new Assert\NotBlank(array('message' => "El Apellido no puede estar en blanco")),
                        new Assert\Regex(array('pattern' => "/^[A-Za-zƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèé êëìíîïðñòóôõöøùúûüýþÿ\s]*$/", 'message' => "El Apellido debe contener sólo letras"))
                    ),
                ))
                ->add('campoNombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'attr' => array('placeholder' => 'Nombre de la Persona'),
                    'label' => false,
                    'mapped' => false,
                    'constraints' => array(
                       // new Assert\NotBlank(array('message' => "El Nombre no puede estar en blanco")),
                        new Assert\Regex(array('pattern' => "/^[A-Za-zƒŠŒŽšœžŸÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèé êëìíîïðñòóôõöøùúûüýþÿ\s]*$/", 'message' => "El Nombre debe contener sólo letras"))
                    ),
                ));
               
}  
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }
    
    public function getName()
    {
        return 'form';
    }
   
}
