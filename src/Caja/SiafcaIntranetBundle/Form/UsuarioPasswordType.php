<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('password_act', 'password', array( 'label' => 'Contraseña Actual:' ) )
            
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Las contraseñas no coinciden.',
                'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options'  => array('label' => 'Nueva Contraseña:'),
                    'second_options' => array('label' => 'Repetir Nueva Contraseña:   '),
                ))
            ->getForm();
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'caja_siafcaintranetbundle_usuario_passwordtype';
    }
}
