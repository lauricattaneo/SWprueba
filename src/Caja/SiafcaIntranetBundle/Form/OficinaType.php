<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository;

class OficinaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre'
            ))
            ->add('zona','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->setParameter('clase', 'ZONA');
                },
                'placeholder' => '-- Seleccione la Zona del Organismo --',
                'required' => true,
                'label' => 'Zona'
            ))
            ->add('telefono','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array(
                    'maxlength' => 10,
                    'pattern' => '^\d{10}$',
                    'class' => 'only-numbers'),
                'label' => 'Teléfono',
                'required' => true
            ))
            ->add('correos','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Correo',
                'attr' => array('placeholder' => 'Correo oficial del Organismo, ej: organismo@gmail.com'),
                'required' => true
            ))
            ->add('cuit','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array(
                    'maxlength' => 11,
                    'pattern' => '^\d{11}$',
                    'class' => 'only-numbers'),
                'label' => 'C.U.I.T.',
                'required' => true
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Oficina'
        ));
    }
}
