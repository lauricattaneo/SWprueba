<?php
namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpteAmpItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        set_time_limit(0);
        $builder
           ->add('cuil','Symfony\Component\Form\Extension\Core\Type\TextType',array(
                'label' => 'C.U.I.L. del Aportante',
                'mapped' => false,
                'constraints' => array(
                    new Assert\NotBlank(array('message' => "El C.U.I.L. no puede estar en blanco")),
                    new Assert\Length(array('min' => 11, 'max' => 11, 'exactMessage' => "El C.U.I.L. debe tener 11 caracteres.")),
                    new Assert\Regex(array('pattern' => "/^[0-9]*$/", 'message' => "El C.U.I.L. debe contener sólo números"))
                ),
            ))
            ->getForm();
                
          
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\ExpteAmpItem'
        ));
    }
}
