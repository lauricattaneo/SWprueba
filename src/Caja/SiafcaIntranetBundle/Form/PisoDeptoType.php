<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PisoDeptoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('piso','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Piso',
                'required' => false,
                'attr' => array(
                    'maxlength' => '100'
                ),
            ))
            ->add('depto','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Departamento',
                'required' => false,
                'attr' => array(
                    'maxlength' => '100'
                ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'inherit_data' => true,
            'attr' => array('class' => 'form-inline'),
            'label' => false
        ));
    }
}