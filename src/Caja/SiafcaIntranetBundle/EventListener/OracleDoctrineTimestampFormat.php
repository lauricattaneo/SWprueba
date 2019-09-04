<?php

namespace Caja\SiafcaIntranetBundle\EventListener;

use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\Common\EventSubscriber;

/**
 * Cambio de formato al leer columnas tipo TIMESTAMP de Oracle.
 */
class OracleDoctrineTimestampFormat implements EventSubscriber
{
    /**
     * @var array
     */
    protected $_defaultSessionVars = [
        //'NLS_TIME_FORMAT' => "HH24:MI:SS",
        //'NLS_DATE_FORMAT' => "YYYY-MM-DD HH24:MI:SS",
        'NLS_TIMESTAMP_FORMAT' => "YYYY-MM-DD HH24.MI.SS",
        //'NLS_TIMESTAMP_TZ_FORMAT' => "YYYY-MM-DD HH24:MI:SS TZH:TZM",
        //'NLS_NUMERIC_CHARACTERS' => ".,",
    ];

    /**
     * @param array $oracleSessionVars
     */
    public function __construct(array $oracleSessionVars = [])
    {
        $this->_defaultSessionVars = array_merge($this->_defaultSessionVars, $oracleSessionVars);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(Events::postConnect);
    }

    /**
     * Luego de la conexion realiza una consulta para modificar algunos de los parametros de la sesión
     * definidos en _defaultSessionVars.
     * 
     * @param ConnectionEventArgs $args
     * @return void
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        if (count($this->_defaultSessionVars)) {
            array_change_key_case($this->_defaultSessionVars, \CASE_UPPER);
            $vars = [];
            foreach ($this->_defaultSessionVars as $option => $value) {
                if ($option === 'CURRENT_SCHEMA') {
                    $vars[] = $option . " = " . $value;
                } else {
                    $vars[] = $option . " = '" . $value . "'";
                }
            }
            $sql = "ALTER SESSION SET ".implode(" ", $vars);
            $args->getConnection()->executeUpdate($sql);
        }
    }
}
