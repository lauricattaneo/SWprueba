<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ManzanaLoteMonoblockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('manzana','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Manzana',
                'required' => false,
                'attr' => array(
                    'maxlength' => '100'
                ),
            ))
            ->add('lote','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Lote',
                'required' => false,
                'attr' => array(
                    'maxlength' => '100'
                ),
            ))
            ->add('monoblock','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Monoblock',
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