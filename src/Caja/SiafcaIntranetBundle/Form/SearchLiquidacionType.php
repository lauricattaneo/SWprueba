<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Caja\SiafcaIntranetBundle\Form\OficinaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository;
use Caja\SiafcaIntranetBundle\Repository\EstadoRepository;
use Symfony\Component\Form\AbstractType;
use Caja\SiafcaIntranetBundle\Repository\OrganismoRepository;

class SearchLiquidacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('anio','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Año',
                'required' => false,
                'attr' => array(
                    'maxlength' => 4,
                    'class' => 'only-numbers input-sm',
                    'placeholder' => 'AAAA'
                ),
            ))
            ->add('mes','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Mes',
                'required' => false,
                'attr' => array(
                    'maxlength' => 2,
                    'class' => 'only-numbers input-sm',
                    'placeholder' => 'MM'
                ),
            ))
            ->add('titulo','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Titulo',
                'required' => false,
                'attr' => array('class' => 'form-control input-sm'),
            ))
            ->add('estado','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Estado',
                'query_builder' => function (EstadoRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.clase = :clase')
                        ->setParameter('clase', 'LIQ');
                },
                'attr' => array('class' => 'form-control input-sm'),
                'required' => false,
                'label' => 'Estado'
            ))
            ->add('orgName','Caja\SiafcaIntranetBundle\Form\Type\AutocompleteType',array(
                'required' => false,
                'label' => 'Organismo',
                'attr' => array('class' => 'form-control input-sm'),
                'url' => 'organismo.ajax_autocomplete',
                'mapped' => false
            ))
            ->add('fuenteLiq','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->setParameter('clase', 'FUENTE');
                },
                'required' => false,
                'label' => 'Fuente',
                'attr' => array('class' => 'form-control input-sm'),
            ))
            ->add('tipoLiq','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->setParameter('clase', 'TIPO_LIQ');
                },
                'required' => false,
                'label' => 'Tipo',
                'attr' => array('class' => 'form-control input-sm'),
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Liquidacion',
            'validation_groups' => false,
        ));
    }
}
