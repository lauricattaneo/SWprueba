<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Caja\SiafcaIntranetBundle\Form\OficinaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository;
use Caja\SiafcaIntranetBundle\Repository\EstadoRepository;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class SearchOrganismoType extends OficinaType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', 'Caja\SiafcaIntranetBundle\Form\Type\AutocompleteType', array(
                'label' => 'Nombre',
                'required' => false,
                'attr' => array(
                    'class' => 'input-sm',
                ),
                'url' => 'organismo.ajax_autocomplete',
            ))
            ->add('zona','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->setParameter('clase', 'ZONA');
                },
                'required' => false,
                'label' => 'Zona',
                'attr' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add('cuit','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array(
                    'maxlength' => 11,
                    'pattern' => '^\d{0,11}$',
                    'class' => 'only-numbers input-sm'),
                'label' => 'C.U.I.T.',
                'required' => false
            ))
            ->add('tipoOrganismo','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\TipoOrganismo',
                'required' => false,
                'label' => 'Tipo',
                'attr' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add('fechaAprobacion','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d{4}'
                ),
                'label' => 'Aprobación',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('fechaInicio','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d{4}'
                ),
                'label' => 'Desde',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('fechaFinal','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d{4}'
                ),
                'label' => 'Hasta',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('expediente','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Expediente',
                'required' => false,
                'attr' => array(
                    'class' => 'input-sm',
                ),
            ))
            ->add('codigo','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Código',
                'required' => false,
                'attr' => array(
                    'maxlength' => 10,
                    'pattern' => '^\d{0,11}$',
                    'class' => 'only-numbers input-sm'),
            ))
            ->add('estado','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Estado',
                'query_builder' => function (EstadoRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.clase = :clase')
                        ->setParameter('clase', 'ORG');
                },
                'attr' => array('class' => 'input-sm'),
                'required' => false,
                'label' => 'Estado'
            ))
            ->add('resolucion','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Resolución',
                'required' => false,
                'attr' => array(
                    'class' => 'input-sm',
                ),
            ));

        $builder->get('fechaInicio')->addModelTransformer(new StringToDateTimeTransformer());
        $builder->get('fechaAprobacion')->addModelTransformer(new StringToDateTimeTransformer());
        $builder->get('fechaFinal')->addModelTransformer(new StringToDateTimeTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }
}
