<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class FirmanteType extends AbstractType
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
            // Para edicion del firmante y alta de firmantes con personas existentes:
            ->add('personaId', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false
            ))
            // Para edicion del firmante:
            ->add('firmanteId', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false
            ))
            ->add('persona','Caja\SiafcaIntranetBundle\Form\PersonaType',array(
                'label' => 'Persona',
            ))
            ->add('fechaDesde','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de inicio en el cargo',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            
            ->add('comentario','Symfony\Component\Form\Extension\Core\Type\TextareaType',array(
                'label' => 'Comentario',
                'required' => false,
            ))
            ->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

        $builder->get('fechaDesde')->addModelTransformer(new StringToDateTimeTransformer());
        //$builder->get('fechaHasta')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Firmante',
        ));
    }

    /**
     * Cambia los campos requeridos en caso de que la persona o el firmante ya exista (edicion).
     * Evita que la verificacion del formulario detecte cuil ya existentes como no validos.
     */
    public function onPreSubmit(FormEvent $event) {
        $submittedData = $event->getData();
        $form = $event->getForm();

        unset($submittedData['personaSearch']);

        if ((isset($submittedData['personaId']) && trim($submittedData['personaId']) !== '') || (isset($submittedData['firmanteId']) && trim($submittedData['firmanteId']) !== '')) {
            unset($submittedData['persona']);
            $form->remove('personaSearch');
            $form->remove('persona');
        }
        
        $event->setData($submittedData);
    }
}
