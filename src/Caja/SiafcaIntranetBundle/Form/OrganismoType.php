<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Caja\SiafcaIntranetBundle\Form\OficinaType;
use Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Entity\Domicilio;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class OrganismoType extends OficinaType
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
            ->add('tipoOrganismo','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\TipoOrganismo',
                'placeholder' => '-- Seleccione Tipo de Organismo --',
                'required' => true,
                'label' => 'Tipo de Organismo'
            ))
            ->add('fechaAprobacion','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de Aprobación',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('expediente','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Expediente',
                'required' => true
            ))
            ->add('resolucion','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'Resolución',
                'required' => true
            ));
                
        /* ATRIBUTOS T0 */
                
        $builder
            ->add('numero','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => true,
                'label' => 'Número de Escuela',
            ))
            ->add('circuns','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->orderBy('p.codigo', 'ASC')
                        ->setParameter('clase', 'CIRCUNS');
                },
                'placeholder' => '-- Seleccione Circunscripción --',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'label' => 'Circunscripción',
            ))
            ->add('ta00','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->orderBy('p.codigo', 'ASC')
                        ->setParameter('clase', 'TA00');
                },
                'placeholder' => '-- Seleccione Tipo de Establecimiento --',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'label' => 'Establecimiento',
            ));
        
        /* ATRIBUTOS T1, T4 */
        $builder
            ->add('juri','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->orderBy('p.codigo', 'ASC')
                        ->setParameter('clase', 'JURI');
                },
                'placeholder' => '-- Seleccione Jurisdicción --',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'label' => 'Jurisdicción'
            ));
//            ->add('uorg','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
//                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
//                'query_builder' => function (ParametrizacionRepository $er) {
//                    return $er->createQueryBuilder('p')
//                        ->where('p.clase = :clase')
//                        ->setParameter('clase', 'UORG');
//                },
//                'placeholder' => '-- Seleccione Unidad de Organización --',
//                'attr' => array('class' => 'form-control'),
//                'required' => true,
//                'label' => 'Unidad de Organización'
//            ));
                
        /* ATRIBUTOS T2 */
        $builder
            ->add('ta02','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->orderBy('p.codigo', 'ASC')
                        ->setParameter('clase', 'TA02');
                },
                'placeholder' => '-- Seleccione Tipo de Dependencia --',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'label' => 'Tipo de Dependencia'
            ));
                
        /* ATRIBUTOS T5 */
        $builder
            ->add('ta05','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->orderBy('p.codigo', 'ASC')
                        ->setParameter('clase', 'TA05');
                },
                'placeholder' => '-- Seleccione Tipo de Establecimiento --',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'label' => 'Establecimiento'
            ))
        ;

        $builder->get('fechaAprobacion')->addModelTransformer(new StringToDateTimeTransformer());

        $builder->add('domicilios', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', array(
            'allow_add' => false,
            'allow_delete' => false,
            'entry_type' => 'Caja\SiafcaIntranetBundle\Form\DomicilioType',
            'by_reference' => false,
            'label' => false,
            'block_name' => 'domicilio',
            'data' => (!isset($options['data']) || count($options['data']->getDomicilios()) == 0)? array(new Domicilio()) : $options['data']->getDomicilios(),
        ))->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Organismo',
            'validation_groups' => array(
               'Caja\SiafcaIntranetBundle\Entity\Organismo',
               'determineValidationGroups',
           ),    
        
        ));
    }

    /**
     * En el caso que se este editando el organismo, se eliminan los campos de domicilio
     */
    public function onPreSubmit(FormEvent $event) {
        $form = $event->getForm();
        if (is_int($form->getConfig()->getOptions()['data']->getId())) {
            $form->remove('domicilios');
        }
    }
}
