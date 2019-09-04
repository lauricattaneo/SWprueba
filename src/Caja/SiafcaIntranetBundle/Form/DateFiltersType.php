<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class DateFiltersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Agrega los campos comunes con Oficina
        parent::buildForm($builder, $options);
        // Continua agregando los campos especificos de Organismo
        $builder
            ->add('startDate','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format input-sm',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Inicio',
                'mapped' => false,
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('endDate','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format input-sm',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fin',
                'mapped' => false,
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ));

        $builder->get('startDate')->addModelTransformer(new StringToDateTimeTransformer());
        $builder->get('endDate')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'stdClass',
        ));
    }
}
