<?php

namespace Caja\SiafcaIntranetBundle\Util;

class Util
{

    function varDump($obj)
    {
        ob_start();
        var_export($obj);
        $output = ob_get_clean();

        $outputFile = "/home/desarrollo/public_html/SiafcaIntranetLog.html";
        $fileHandle = fopen($outputFile, 'a') or die("File creation error.");
        fwrite($fileHandle, $output);
        fclose($fileHandle);
        return true;
    }

    /**
     * Obtiene un array con todos los errores de un formulario indexados por el nombre del campo
     * @param Form $form Un objeto Form de symfony
     * @return array Listado de errores
     */
    function getErrorMessages($form)
    {
        $errors = array();
        if ($form->count() > 0) {
            foreach ($form->all() as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        } else {
            foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
            }
        }
        return $errors;
    }
    
    /**
    * Escribe lo que le pasen a un archivo de logs
    * @param string $cadena texto a escribir en el log
    * @param string $tipo texto que indica el tipo de mensaje. Los valores normales son Info, Error,  
    *                                       Warn Debug, Critical
    */
    function write_log($cadena,$tipo)
    {
	$arch = fopen('/home/desarrollo/public_html/'."/logs/milog_".date("Y-m-d").".txt", "a+"); 

	fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".$_SERVER['REMOTE_ADDR']." ".
                   $_SERVER['HTTP_X_FORWARDED_FOR']." - $tipo ] ".$cadena."\n");
	fclose($arch);
    }
}