<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotNull;

class ContactoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('contactFirstName', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 20,
                    'pattern' => '^.{1,20}$'),
                'label' => 'Su Nombre',
                'constraints' => array(
                    new Length(array('max' => 20, 'minMessage' => "El nombre debe tener entre 1 y 20 caracteres", 'maxMessage' => "El nombre debe tener entre 1 y 20 caracteres")),
                    new Type(array('type' => "string", 'message' => "Introducir texto")),
                ),
                'trim' => true
            ))
            ->add('contactLastName', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 20,
                    'pattern' => '^.{1,20}$'),
                'label' => 'Su Apellido',
                'constraints' => array(
                    new Length(array('max' => 20, 'minMessage' => "El apellido debe tener entre 1 y 20 caracteres", 'maxMessage' => "El apellido debe tener entre 1 y 20 caracteres")),
                    new Type(array('type' => "string", 'message' => "Introducir texto")),
                ),
                'trim' => true
            ))
            ->add('contactPhone', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'attr' => array(
                    'maxlength' => 20,
                    'pattern' => '^\d{0,20}$'),
                'label' => 'Su Teléfono',
                'required' => false,
                'constraints' => array(
                    new Length(array('min' => 0, 'max' => 20, 'minMessage' => "El número telefónico ingresado debe tener entre 1 y 20 caracteres", 'maxMessage' => "El número telefónico ingresado debe tener entre 1 y 20 caracteres")),
                ),
                'trim' => true
            ))
            ->add('contactMail', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'attr' => array(
                    'maxlength' => 500,
                    'pattern' => '^.{1,500}$'),
                'label' => 'Su Email',
                'required' => false,
                'constraints' => array(
                    new Length(array('min' => 0, 'max' => 500, 'minMessage' => "El texto ingresado debe tener entre 1 y 500 caracteres", 'maxMessage' => "El texto ingresado debe tener entre 1 y 500 caracteres")),
                ),
                'trim' => true
            ))
            ->add('destinationEmails', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'mail1@example.com; mail2@example.com;',
                    'maxlength' => 500,
                    'pattern' => '^.{1,500}$'),
                'label' => 'Emails de destino',
                'data' => implode('; ', array_map('trim', array_filter($entity->defaultEmails))),
                'constraints' => array(
                    new Type(array('type' => "string", 'message' => "Introducir texto")),
                    new Length(array('min' => 1, 'max' => 500, 'minMessage' => "El texto ingresado debe tener entre 1 y 500 caracteres", 'maxMessage' => "El texto ingresado debe tener entre 1 y 500 caracteres")),
                    new NotNull(array('message' => "Debe ingresar los mails de los destinatarios"))
                ),
                'trim' => true
            ))
            ->add('subject', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'attr' => array(
                    'maxlength' => 30,
                    'pattern' => '^.{1,30}$'),
                'label' => 'Asunto',
                'required' => true,
                'constraints' => array(
                    new Type(array('type' => "string", 'message' => "Introducir texto")),
                    new Length(array('min' => 1, 'max' => 30, 'minMessage' => "El asunto debe tener entre 1 y 30 caracteres", 'maxMessage' => "El asunto debe tener entre 1 y 30 caracteres")),
                    new NotNull(array('message' => "Debe ingresar el asunto de la comunicación"))
                ),
                'trim' => true
            ))
            ->add('message', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'attr' => array(
                    'maxlength' => 1000),
                'required' => true,
                'label' => 'Mensaje',
                'constraints' => array(
                    new Type(array('type' => "string", 'message' => "Introducir texto")),
                    new Length(array('min' => 1, 'max' => 1000, 'minMessage' => "El asunto debe tener entre 1 y 1000 caracteres", 'maxMessage' => "El asunto debe tener entre 1 y 1000 caracteres")),
                    new NotNull(array('message' => "Debe ingresar un mensaje a entregar"))
                ),
                'trim' => true
            ));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            // Clase anonima para poder llamar al formulario desde un controlador:
            // Para crear un formulario usar como parametro: new \stdClass()
            'data_class' => '\stdClass',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'contact_page',
        ));
    }
}
