<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Caja\SiafcaIntranetBundle\Repository\ParametrizacionRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class FamiliarType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('personaSearch', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Buscar persona',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Ingrese C.U.I.L.',
                    'maxlength' => 11,
                    'class' => 'only-numbers'
                ),
                'mapped' => false,
                'help' => 'Ingrese el cuil para verificar si la persona ya esta cargada en el sistema. La búsqueda se realizará al completar los 11 digitos.'
            ))
            // Para edicion del familiar:
            ->add('id','Symfony\Component\Form\Extension\Core\Type\HiddenType',array('mapped' => false))
            ->add('personaId', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false
            ))
            ->add('familiarId', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false
            ))
            ->add('familiar','Caja\SiafcaIntranetBundle\Form\PersonaType',array(
                'label' => 'Persona'
            ))
            ->add('fechaValidacion','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de ultima Validación',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('fechaVencimiento','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de Vencimiento',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('Parentezco','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Parametrizacion',
                'query_builder' => function (ParametrizacionRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.clase = :clase')
                        ->orderBy('p.codigo', 'ASC')
                        ->setParameter('clase', 'PARENT');
                },
                'placeholder' => '-- Seleccione Parentezco --',
                'attr' => array('class' => 'form-control'),
                'required' => true,
                'label' => 'Parentezco',
            ))      
            ->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

        $builder->get('fechaValidacion')->addModelTransformer(new StringToDateTimeTransformer());
        $builder->get('fechaVencimiento')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Familiar',
        ));
    }

    /**
     * Cambia los campos requeridos en caso de que la persona o el firmante ya exista (edicion).
     * Evita que la verificacion del formulario detecte cuil ya existentes como no validos.
     */
    public function onPreSubmit(FormEvent $event) {
        $submittedData = $event->getData();
        $form = $event->getForm();
        if (isset($submittedData['personaId']) || $submittedData['familiarId'] ) {
            unset($submittedData['personaSearch'], $submittedData['familiar']);
            $event->setData($submittedData);
            $form->remove('personaSearch');
            $form->remove('familiar');
        }
    }
}
