<?php

/**
 * Alerts class
 *
 * Luki framework
 * Date 24.9.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Storage;

/**
 * Alerts class
 *
 * Alerts management
 *
 * @package Luki
 */
class Alerts
{

    public static function addDanger($messagge)
    {
        self::init();
        $alerts = Storage::Alerts();
        $alerts['danger'][] = $messagge;
        Storage::Set('alerts', $alerts);
        
        unset($messagge, $alerts);
    }
    
    public static function addWarning($messagge)
    {
        self::init();
        $alerts = Storage::Alerts();
        $alerts['warning'][] = $messagge;
        Storage::Set('alerts', $alerts);
        
        unset($messagge, $alerts);
    }
    
    public static function addInfo($messagge)
    {
        self::init();
        $alerts = Storage::Alerts();
        $alerts['info'][] = $messagge;
        Storage::Set('alerts', $alerts);
        
        unset($messagge, $alerts);
    }
    
    public static function addSuccess($messagge)
    {
        self::init();
        $alerts = Storage::Alerts();
        $alerts['success'][] = $messagge;
        Storage::Set('alerts', $alerts);
        
        unset($messagge, $alerts);
    }
    
    public static function init()
    {
        if(!Storage::isSaved('alerts')) {
            Storage::Set('alerts', array(
              'danger' => array(),
              'warning' => array(),
              'info' => array(),
              'success' => array(),
            ));
        }
    }
    
    public static function isAlerts()
    {
        self::init();
        $alerts = Storage::Alerts();
        $count = count($alerts['danger']) + count($alerts['warning']) + count($alerts['info']) + count($alerts['success']);
        
        unset($alerts);
        return (bool)$count;
    }
}