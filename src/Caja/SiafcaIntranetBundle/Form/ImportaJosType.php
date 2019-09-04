<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Caja\SiafcaIntranetBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Description of ImportaJos
 *
 * @author desarrollo
 */
class ImportaJosType extends AbstractType {
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fecha', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d{4}',
                ),
                'label' => 'Fecha del .JOS a importar ',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('cancelar', SubmitType::class, array('label' => 'Cancelar', 'attr'  => array('class' => 'btn btn-default pull-right active')))
            ->add('importarJos', SubmitType::class, array('label' => 'Importar', 'attr'  => array('class' => 'btn btn-primary pull-right active')))
            ->add('reporteJos', SubmitType::class, array('label' => 'Ver Reporte', 'attr'  => array('class' => 'btn btn-success pull-right active')))
        ;
    }
}
