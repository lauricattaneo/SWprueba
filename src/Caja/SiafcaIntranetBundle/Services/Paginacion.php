<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Caja\SiafcaIntranetBundle\Services;

/**
 * Metodo global para armar la paginacion, simil al de internet
 * @author desarrollo
 */
class Paginacion {
    // instancia del paginador
    private $paginator;
    
    // creo el paginacion, creo instancia del paginador 
    public function __contruct($paginator){
       $this->$paginator = $paginator;
    }
    
    // seteo el paginador, llamo al servicio knp_paginator
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
    }
    
    // metodo para hacer la paginacion
    // param: $request donde viene info de la pagina
    //        $datosws es la info que viene del webservice
    //        $cantidadPagina cantidad de filas a mostrar por fila
    //        $criterioOrden criterio de ordenamiento de las columnas, es opcional, sino se usa va null
    public function obtenerPaginacion($request,$datosWS,$cantidadPorPagina,$criteriorOrden)
    {
        // si viene null el criterio de orden, se le asigna un array vacio
        if(is_null($criteriorOrden)) {
            $criteriorOrden = array();
        }
        // armo la paginacion
        $paginacion = 
                    $this->paginator->paginate(
                    $datosWS,
                    $request->query->getInt('page', 1)/*page number*/,
                    $cantidadPorPagina, /*limit per page*/
                    $criteriorOrden        
        );
        // retorno    
        return $paginacion;
    }
}
