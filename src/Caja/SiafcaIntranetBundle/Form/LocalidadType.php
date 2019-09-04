<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Entity\Localidad;
use Caja\SiafcaIntranetBundle\Entity\Provincia;
use Caja\SiafcaIntranetBundle\Entity\Departamento;
use Caja\SiafcaIntranetBundle\Type;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LocalidadType extends AbstractType
{
    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add listeners
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }
    
    protected function addElements(FormInterface $form, Provincia $provincia = null, 
            Departamento $departamento = null)
    {
        
        // Add the province element
        $form->add('Provincia', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
            'data' => $provincia,
            'placeholder' => '-- Seleccione una Provincia --',
            'class' => 'Caja\SiafcaIntranetBundle\Entity\Provincia',
            'mapped' => false,
            'attr' => array ('class' => 'form-control'))
        );
        
        $departamentos = $localidades = array();
        
        if ($provincia)
        {
            // Fetch the cities from specified province
            $repo = $this->em->getRepository('SiafcaIntranetBundle:Departamento');
            $departamentos = $repo->findByProvincia($provincia);
        }
        if ($departamento)
        {
            $form->add('Departamento', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'choices' => $departamentos,
                'placeholder' => '-- Seleccione un Departamento --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Departamento',
                'mapped' => false,
                'attr' => array ('class' => 'form-control'),
                'data' => $departamento)
            );
            $repoLocalidad = $this->em->getRepository('SiafcaIntranetBundle:Localidad');
            $localidades = $repoLocalidad->findByDepartamento($departamento);
        }
        else
        {
            $form->add('Departamento', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'choices' => $departamentos,
                'placeholder' => '-- Seleccione un Departamento --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Departamento',
                'mapped' => false,
                'attr' => array ('class' => 'form-control'))
            );
        }
        $form->add('Localidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
            'choices' => $localidades,
            'placeholder' => '-- Seleccione una Localidad --',
            'class' => 'Caja\SiafcaIntranetBundle\Entity\Localidad',
            'mapped' => false,
            'attr' => array ('class' => 'form-control'))
        );
    }
    
    function onPreSetData(FormEvent $event)
    {
        $localidad = $event->getData();
        $form = $event->getForm();

        // We might have an empty account (when we insert a new account, for instance)
        $provincia = $departamento = null;
        if ($localidad)
        {
            $departamento = $localidad->getDepartamento();
            $provincia = $departamento->getProvincia();
        }
        $this->addElements($form, $provincia, $departamento);
    }
    
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Note that the data is not yet hydrated into the entity.
        $provincia = $this->em->getRepository('SiafcaIntranetBundle:Provincia')->find($data['Provincia']);
        $departamento = $this->em->getRepository('SiafcaIntranetBundle:Departamento')->find($data['Departamento']);
        $this->addElements($form, $provincia,$departamento);
        $localidad = $this->em->getRepository('SiafcaIntranetBundle:Localidad')->find($data['Localidad']);
        $form->setData($localidad);
    }
//    
//    function onSubmit(FormEvent $event)
//    {
//        $data = $event->getData();
//        dump($data);
//        return $data;
//    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Localidad',
            'allow_extra_fields' => true,
        ));
    }
}

