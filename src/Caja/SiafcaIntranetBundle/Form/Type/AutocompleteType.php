<?php

namespace Caja\SiafcaIntranetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class AutocompleteType extends AbstractType
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'url' => '', // Url to request data by filter value
        ));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['autocomplete'] = 'off';
        $view->vars['attr']['class'] = isset($options['attr']['class'])? $options['attr']['class'].' autocomplete-field' : 'autocomplete-field';
        $view->vars['attr']['data-url'] = $this->router->generate($options['url']);
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }
}
