<?php

namespace Caja\SiafcaIntranetBundle\Services;

class MenuHandler {
    public $menuStructure;
    
    public function __construct()
    {
        $sidebar = array();
        $sidebar['Liquidaciones'] = array(
            'path' => 'liquidacion_index',
            'icon' => 'assignment',
            'submenu' => array(
                array(
                    'path' => 'liquidacion_search',
                    'icon' => 'search',
                    'name' => 'Buscar Liquidación',
                    'd' => ''
                ),
            ),
        );
        $sidebar['Oficinas'] = array(
            'path' => 'oficina_index',
            'icon' => 'home',
            'submenu' => array(
                array(
                    'path' => 'oficina_new',
                    'icon' => 'add_circle',
                    'name' => 'Agregar Oficina',
                    'd' => ''
                ),
                array(
                    'path' => 'oficina_search',
                    'icon' => 'search',
                    'name' => 'Buscar Oficina',
                    'd' => ''
                ),
            ),
        );
        $sidebar['Organismos'] = array(
            'path' => 'organismo_index',
            'icon' => 'home',
            'submenu' => array(
                array(
                    'path' => 'organismo_new',
                    'icon' => 'add_circle',
                    'name' => 'Agregar Organismo',
                    'd' => ''
                ),
                array(
                    'path' => 'organismo_search',
                    'icon' => 'search',
                    'name' => 'Buscar Organismo',
                    'd' => ''
                ),
                array(
                    'path' => 'firmante_pendientes',
                    'icon' => '',
                    'name' => 'Firmantes Pendientes',
                    'd' => '!'
                )
            ),
        );
        $sidebar['Personas'] = array(
            'path' => 'persona_index',
            'icon' => 'people',
            'submenu' => array(
                array(
                    'name' => 'Agregar Persona',
                    'path' => 'persona_new',
                    'icon' => 'person_add',
                    'd' => ''
                ),
            ),
        );
        $sidebar['Administrador'] = array(
            'path' => 'excepciones_index',
            'icon' => 'build',
            'submenu' => array(
                array(
                    'name' => 'Excepciones',
                    'path' => 'excepciones_index',
                    'icon' => 'bug_report',
                    'd' => ''
                ),
                array(
                    'name' => 'Importar Archivo JOS',
                    'path' => 'importar_jos',
                    'icon' => 'bug_report',
                    'd' => ''
                ),
                array(
                    'name' => 'Gestión de Ayudas',
                    'path' => 'ayuda_index',
                    'icon' => 'bug_report',
                    'd' => ''
                ),
                array(
                    'name' => 'Validar IdCiudadana',
                    'path' => 'id_ciudadana',
                    'icon' => 'bug_report',
                    'd' => ''
                ),
            ),
        );
        $this->menuStructure = $sidebar;
    }
    
    public function getMenuStructure()
    {
        return $this->menuStructure;
    }
}
