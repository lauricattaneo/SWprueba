<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Repository\EstadoCivilRepository;
use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;
use Caja\SiafcaInternetBundle\Validator\Constraints as PersonaAssert;
use Symfony\Component\Validator\Constraints\Regex;
use Caja\SiafcaIntranetBundle\Validator\Constraints\ValidaFechaNac;

class PersonaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('apellidoPat','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Apellido',
                // agrego constraint para limitar el tamaño dle string a 100, sino explota cuando
                // intenta insertar en la base, pq el limite es 100
                'attr' => array(
                    'maxlength' => '100'
                ),
            ))
        /*->add('apellidoMat','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Apellido Materno',
                'required' => false
            )) */
            ->add('nombre','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Nombre',
                'attr' => array(
                    'maxlength' => '100'
                ),
            ))
            ->add('estadoCivil','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'placeholder' => '-- Seleccione Estado Civil --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\EstadoCivil',
                'required' => true,
                'attr' => array ('class' => 'form-control'),
                'label' => 'Estado Civil',
                'query_builder' => function (EstadoCivilRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.codigo', 'ASC');
                },
            ))
            ->add('cuil','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array(
                    'class' => 'only-numbers',
                    'maxlength' => '11'
                ),
                'label' => 'C.U.I.L.'
            ))
            /*->add('tipoDocumento','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'placeholder' => '-- Seleccione Tipo de Documento --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\TipoDocumento',
                'required' => true,
                'label' => 'Tipo de Documento'
            )) */
            ->add('documento','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'attr' => array( 'class' => 'only-numbers' ),
                'label' => 'Documento'
            ))
            ->add('sexo','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'placeholder' => '-- Seleccione Sexo --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Sexo',
                'required' => true ,
                'label' => 'Sexo'
            ))
            ->add('nacionalidad','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'placeholder' => '-- Seleccione Nacionalidad --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Nacionalidad',
                'required' => true,
                'label' => 'Nacionalidad'
            ))                    
            ->add('fechaNac', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario'
                    ),
//                'constraints' => array(
//                    new Regex(
//                            array(
//                                'pattern' => '/(\d\d)\/(\d\d)\/(\d{4})/',
//                                'message' => 'Fecha no válida, respete el formato: dd/mm/aaaa'
//                                )),
//                    ),
                'label' => 'Fecha de nacimiento',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa',
            ))
            ->add('domicilios', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
                'allow_add' => false,
                'allow_delete' => false,
                'entry_type' => 'Caja\SiafcaIntranetBundle\Form\DomicilioType',
                'by_reference' => false,
                'label' => false,
                'block_name' => 'domicilio',
                'data' => ((!isset($options['data']) || count($options['data']->getDomicilios()) == 0)? array(new Domicilio()) : $options['data']->getDomicilios()),
            ))
        ->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

        $builder->get('fechaNac')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Persona',
        ));
    }

    /**
     * En el caso que se este editando la persona, se eliminan los campos de domicilio
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();
        if ((isset($options['data']) && is_int($options['data']->getId())) || !isset($options['data'])) {
            $form->remove('domicilios');
        }
    }
}
