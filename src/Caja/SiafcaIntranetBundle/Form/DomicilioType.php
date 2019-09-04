<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotNull;
use Caja\SiafcaIntranetBundle\Repository\DepartamentoRepository;
use Caja\SiafcaIntranetBundle\Repository\LocalidadRepository;
use Symfony\Component\Form\FormEvent;

class DomicilioType extends AbstractType
{
    
    private $disableTipoDomicilio;
    private $em;

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
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));

        $localidad = $departamento = $provincia = null;
        if ($entity = $builder->getData()) {
            $localidad = $entity->getLocalidad();
            if ($localidad) { $departamento = $localidad->getDepartamento(); }
            if ($departamento) { $provincia = $departamento->getProvincia(); }
        }

        //$this->disableTipoDomicilio = !$options['isOrganismo'];
        $builder
            //id para edicion
            ->add('id','Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false
            ))
            ->add('calleNumero','Caja\SiafcaIntranetBundle\Form\CalleNumeroType')
            ->add('pisoDepto','Caja\SiafcaIntranetBundle\Form\PisoDeptoType')
            ->add('manzanaLoteMono','Caja\SiafcaIntranetBundle\Form\ManzanaLoteMonoblockType')
            ->add('tipoDomicilio','Symfony\Bridge\Doctrine\Form\Type\EntityType',array(
                'placeholder' => '--Seleccione un Tipo de Domicilio--',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\TipoDomicilio',
                'required' => true,
                'label' => 'Tipo de Domicilio',
                //'disabled' => $this->disableTipoDomicilio
            ))
            ->add('Localidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                 'query_builder' => function (LocalidadRepository $er) use ($departamento) {
                    return $er->createQueryBuilder('l')
                        ->where('l.departamento = ?1')
                        ->orderBy('l.nombre', 'ASC')
                        ->setParameter(1, ($departamento)? $departamento->getId() : $departamento);
                },
                'placeholder' => '-- Seleccione una Localidad --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Localidad',
                'label' => 'Localidad',
                'data' => $localidad,
                'constraints' => array(
                    new NotNull(array('message' => "Debe ingresar una localidad"))
                )))
            ->add('Departamento', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'query_builder' => function (DepartamentoRepository $er) use ($provincia) {
                    return $er->createQueryBuilder('d')
                        ->where('d.provincia = ?1')
                        ->orderBy('d.nombre', 'ASC')
                        ->setParameter(1, ($provincia)? $provincia->getId() : $provincia);
                },
                'placeholder' => '-- Seleccione un Departamento --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Departamento',
                'data' => $departamento,
                'label' => 'Departamento',
                'mapped' => false,
                'constraints' => array(
                    new NotNull(array('message' => "Debe ingresar un departamento"))
                )))
            ->add('Provincia', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'data' => $provincia,
                'placeholder' => '-- Seleccione una Provincia --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Provincia',
                'mapped' => false,
                'label' => 'Provincia',
                'constraints' => array(
                    new NotNull(array('message' => "Debe ingresar una provincia"))
                )));
    }
    
    function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // Note that the data is not yet hydrated into the entity.
        if (isset($data['Provincia'])) {
            $provincia = $this->em->getRepository('SiafcaIntranetBundle:Provincia')->find($data['Provincia']);
        }
        if (isset($data['Departamento'])) {
            $departamento = $this->em->getRepository('SiafcaIntranetBundle:Departamento')->find($data['Departamento']);
        }

        if (isset($data['Provincia']) && $provincia) {
            // Fetch the cities from specified province so symfony gets selected value as valid option
            // Replaces field with a copy with new options
            $form->add('Departamento', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'query_builder' => function (DepartamentoRepository $er) use ($provincia) {
                    return $er->createQueryBuilder('d')
                        ->where('d.provincia = ?1')
                        ->orderBy('d.nombre', 'ASC')
                        ->setParameter(1, ($provincia)? $provincia->getId() : $provincia);
                },
                'placeholder' => '-- Seleccione un Departamento --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Departamento',
                'data' => $departamento,
                'label' => 'Departamento',
                'mapped' => false,
                'constraints' => array(
                    new NotNull(array('message' => "Debe ingresar un departamento"))
                )));
        }

        $localidad = null;
        if (isset($data['Localidad'])) {
            $localidad = $this->em->getRepository('SiafcaIntranetBundle:Localidad')->find($data['Localidad']);
        }

        if (isset($data['Departamento']) && $departamento) {
            $form->add('Localidad', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'query_builder' => function (LocalidadRepository $er) use ($departamento) {
                    return $er->createQueryBuilder('l')
                        ->where('l.departamento = ?1')
                        ->orderBy('l.nombre', 'ASC')
                        ->setParameter(1, ($departamento)? $departamento->getId() : $departamento);
                },
                'placeholder' => '-- Seleccione una Localidad --',
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Localidad',
                'label' => 'Localidad',
                'data' => $localidad,
                'constraints' => array(
                    new NotNull(array('message' => "Debe ingresar una localidad"))
                )));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\Domicilio',
            'allow_extra_fields' => true,
            //'isOrganismo' => false,
        ));
    }
}
