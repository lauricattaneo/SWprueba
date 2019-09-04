<?php

namespace Caja\SiafcaIntranetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
* Comando que realiza la importación de los archivo ".JOS".
* Se realiza todos los días a las 6 a.m. mediante un cron.
* Los parámetros 'name', 'ape' y la opción 'yell' solamente están para mostrar cómo se implementan.
* No se utilizan en el script.
* @param String $name Nombre. Si tiene más de una palabra, ponerlas a todas entre commillas. 
* @param String $ape Apellido. Si tiene más de una palabra, ponerlas a todas entre commillas. 
* @param String $yell Muestra todo en mayúsculas.
*/
class ImportarJosCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('caja:siafca:importaJos')
            ->setDescription('Procesa los archivo .JOS')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nombre')
            ->addArgument('ape', InputArgument::OPTIONAL, 'Apellido')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $fecha = date('Y-m-d');
        $nuevafecha = strtotime ( '-1 day' , strtotime ( $fecha ) ) ;
        $nuevafecha = date ( 'Y-m-d' , $nuevafecha );

        $dia = substr($nuevafecha, 8, 2);
        $mes = substr($nuevafecha, 5, 2);
        $anio = substr($nuevafecha, 0, 4);

//        $output->writeln($fecha);

        
        
        // Envío el correo de notificación de comienzo de la importación
        $texto_mail = 'Comienzo de la importación de archivos .JOS';
        
        $mensaje = \Swift_Message::newInstance()
            ->setSubject("Comienzo de Importación JOS del ". $dia. '-' . $mes . '-' . $anio)
            ->setFrom("cnass@santafe.gov.ar")
            ->setTo("cnass@santafe.gov.ar")
            ->setBody($texto_mail, 'text/html');
        
        $this->getContainer()->get('mailer')->send($mensaje);

        // Proceso de importación
        $con = $this->getContainer()->get('doctrine')->getConnection();
        
        $con->executeQuery(""
                        . "BEGIN "
                        . "SP_PROCESAR_JOS(:dia, :mes, :anio, :sobreescribir); "
                        . "END;"
                        , array('dia' => $dia, 'mes' => $mes, 'anio' => $anio, 'sobreescribir' => 'S')
                ); 
        
        // Envío el correo de notificación de finalización de la importación
        $texto_mail = 'Finalización de la importación de archivos .JOS';
        $mensaje = \Swift_Message::newInstance()
            ->setSubject("Final de la Importación JOS del ". $dia. '-' . $mes . '-' . $anio)
            ->setFrom("cnass@santafe.gov.ar")
            ->setTo("cnass@santafe.gov.ar")
            ->setBody($texto_mail, 'text/html');
        
        $this->getContainer()->get('mailer')->send($mensaje);

// Ejemplo: cómo utilizar parámetros (name y ape) y opciones (yell)
//         
//        $name = $input->getArgument('name');
//        if ($name) {
//            $text = 'Hello '.$name;
//        } else {
//            $text = 'Hello';
//        }
//
//        $ape = $input->getArgument('ape');
//        if ($ape) {
//            $text = $text. ', '.$ape;
//        }
//        
//        if ($input->getOption('yell')) {
//            $text = strtoupper($text);
//        }
//
//        $output->writeln($text);
        
    }

}
