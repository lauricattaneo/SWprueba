<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository;

class SearchOficinaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','Caja\SiafcaIntranetBundle\Form\Type\AutocompleteType',array(
                'label' => 'Nombre',
                'required' => false,
                'url' => 'oficina.ajax_autocomplete',
                'attr' => array('class' => 'form-control input-sm'),
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
                'attr' => array('class' => 'form-control input-sm'),
            ))
            ->add('cuit','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array(
                    'maxlength' => 11,
                    'pattern' => '^\d{0,11}$',
                    'class' => 'only-numbers form-control input-sm'),
                'label' => 'C.U.I.T.',
                'required' => false
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Oficina',
            'validation_groups' => false,
        ));
    }
}
