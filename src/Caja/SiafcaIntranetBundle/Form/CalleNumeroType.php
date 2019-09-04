<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalleNumeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('calle','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Calle',
                'required' => true,
                 'attr' => array(
                    'maxlength' => '200'
                ),
            ))
            ->add('numero','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Número',
                'required' => true,
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