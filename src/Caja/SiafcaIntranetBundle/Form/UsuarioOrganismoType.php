<?php

namespace Caja\SiafcaIntranetBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Caja\SiafcaIntranetBundle\Repository\RolRepository;
use Caja\SiafcaIntranetBundle\Form\DataTransformer\StringToDateTimeTransformer;

class UsuarioOrganismoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idCiudadanaBuscar', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'CUIL del Usuario',
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Ingrese C.U.I.L.',
                    'maxlength' => 11,
                    'class' => 'only-numbers'
                ),
                'mapped' => false,
                'help' => 'Ingrese el cuil para verificar si tiene ID Ciudadana.'
            )) 
            ->add('idNombre', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Nombre', 
                'required' => false,
                'mapped' => false,
            )) 
                
            ->add('idMail', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'E-mail', 
                'required' => false,
                'mapped' => false,
            )) 
            
            ->add('idDn', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'Dn', 
                'required' => false,
                'mapped' => false,
            ))  
                
            ->add('username' ,'Caja\SiafcaIntranetBundle\Form\Type\AutocompleteType', array(
                'label' => 'ID Ciudadana',
                'required' => true,
                'mapped' => false,
                'url' => 'usuario.ajax_autocomplete',
                
                'read_only' => false
            ))
             ->add('rol', 'Symfony\Bridge\Doctrine\Form\Type\EntityType', array(
                'class' => 'Caja\SiafcaIntranetBundle\Entity\Rol',
                   'query_builder' => function (RolRepository $er) use ($options) {
                        
                $roles = $options['roles'];
                        if($options['type'] === 'oficina'){
                            if (in_array('ROLE_CONTRALOR_ADMIN', $roles)) {
                                if($options['discr'] === 'oficina'){
                                    $query = $er->createQueryBuilder('r')->where('r.id IN (4)');
                                }
                               elseif($options['discr'] === 'organismo'){
                                    $query = $er->createQueryBuilder('r')->where('r.id IN (3)');
                                }
                        
                            }
                        }
//                            $query = $er->createQueryBuilder('r')->where('r.id IN (3)');
//                          if (in_array('ROLE_ADMIN', $roles)) {
//                            $query = $er->createQueryBuilder('r')->where('r.id IN (3)');
//                         
                         
//                        }elseif($options['type'] === 'organismo' && (!empty ($roles))){
//                          $query = $er->createQueryBuilder('r')->where('r.id IN (3)');
//                          
                        
                        else {
                            $query = $er->createQueryBuilder('r')
                                       ->orderBy('r.nombre', 'ASC');
                       }

                    return $query;
              },
                'placeholder' => '-- Seleccione un Rol --',
                'label' => 'Rol'
            ))
            ->add('fechaDesde', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => true,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha de inicio',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
                        /*
            ->add('fechaHasta', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'attr' => array(
                    'class' => 'date-format',
                    'title' => 'Click en el calendario',
                    'pattern' => '\d\d\/\d\d\/\d\d\d\d',
                ),
                'label' => 'Fecha fin',
                'invalid_message' => 'El formato ingresado es incorrecto. Ingrese la fecha de la forma: dd/mm/aaaa'
            ))
            */
            ->add('correo', 'Symfony\Component\Form\Extension\Core\Type\EmailType', array(
                'required' => true,
                
                ))
            ->add('uid', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'mapped' => false,
            ));

        $builder->get('fechaDesde')->addModelTransformer(new StringToDateTimeTransformer());
        //$builder->get('fechaHasta')->addModelTransformer(new StringToDateTimeTransformer());
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caja\SiafcaIntranetBundle\Entity\UsuarioOrganismo',
            'type' => '',
            'discr' => '',
            'roles'=> ''
        ));
    }
}
