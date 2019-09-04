<?php
namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class ExpteAmparoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaInicio','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de Inicio',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('fechaResolucion','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de Resolución',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('numero')

//            ->add('amparoItems','Symfony\Component\Form\Extension\Core\Type\CollectionType',array(
//                'allow_add' => true,
//                'allow_delete' => true,
//                'entry_type' => new ExpteAmpItemType(),
//                'by_reference' => false,
//                'label' => false,
//                'prototype' => true,
//                'prototype_name' => 'tag__name__',
//            ))

        ;

        $builder->get('fechaInicio')->addModelTransformer(new StringToDateTimeTransformer());
        $builder->get('fechaResolucion')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\ExpteAmparo'
        ));
    }
}
