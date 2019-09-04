<?php
namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\ExpteAmpItemToNumberTransformer;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class ExpteAmpItemConceptoType extends AbstractType
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaInicio', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de Inicio',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('fechaFin', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de Resolución',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            ->add('descripcion', 'Symfony\Component\Form\Extension\Core\Type\TextareaType', array(
                'label' => 'Descripción',
                'invalid_message' => 'Error en descripción',
            ))
            ->add('porcentaje', 'Symfony\Component\Form\Extension\Core\Type\NumberType', array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Ej: 11.5',
                    'maxlength' => '6', // Posibles valores decimales (12.444)
                    'title' => 'Ingrese sólo caracteres numéricos y separador decimal',
                ),
                'label' => 'Porcentaje',
                'invalid_message' => 'Sólo acepta caracteres numéricos con separador de decimales'
            ))
            ->add('amparoItem', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'invalid_message' => 'El item de amparo no es válido',
            ))
            ->add('concepto', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'placeholder' => '-- Seleccione un concepto --',
                'class' => 'SiafcaIntranetBundle:Concepto',
                'choice_label' => 'nombre',
                'label' => 'Concepto',
                'invalid_message' => 'El concepto ingresado no es válido',
            ));

        $builder->get('amparoItem')->addModelTransformer(new ExpteAmpItemToNumberTransformer($this->manager));
        $builder->get('fechaInicio')->addModelTransformer(new StringToDateTimeTransformer());
        $builder->get('fechaFin')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\ExpteAmpItemConcepto'
        ));
    }
}
