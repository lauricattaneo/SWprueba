<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntidadResponsableType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entidadRSearch', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Buscar entidad',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Ingrese C.U.I.L.',
                    'maxlength' => 11,
                    'class' => 'only-numbers'
                ),
                'mapped' => false,
                'help' => 'Ingrese el cuil para verificar si la entidad ya esta cargada en el sistema. La búsqueda se realizará al completar los 11 digitos.'
            ))
            // Para edicion de la entidad responsable:
            ->add('id', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false,
            ))
            ->add('nombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre de entidad',
                'required' => true,
                'attr' => array(
                    'maxlength' => 30,
                )
            ))
            ->add('tipoJuridico', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Tipo juridico',
                'required' => true,
                'attr' => array(
                    'maxlength' => 20,
                )
            ))
            ->add('cuit', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'CUIT',
                'required' => true,
                'attr' => array(
                    'class' => 'only-numbers',
                    'maxlength' => 11,
                ),
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\EntidadResponsable',
        ));
    }
}
