<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Caja\SiafcaIntranetBundle\Entity\Rol;
use Caja\SiafcaIntranetBundle\Repository\RolRepository;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array( 'placeholder' => 'Nombre de Usuario'),
                'label' => 'Nombre de Usuario',
                'required' => true
            ))
//            ->add('password','Symfony\Component\Form\Extension\Core\Type\RepeatedType',array(
//                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
//                'invalid_message' => 'Las contraseñas deben ser iguales.',
//                'required' => true,
//                'first_options'  => array(
//                    'attr' => array('placeholder' => 'Contraseña'),
//                    'label' => false
//                ),
//                'second_options' => array(
//                    'attr' => array('placeholder' => 'Repita la contraseña'),
//                    'label' => false
//                ),
//            ))
            ->add('rol', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Rol',
                'mapped' => false,
                'placeholder' => '-- Seleccione un Rol --',
                'label' => 'Rol'
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Usuario'
        ));
    }
}
