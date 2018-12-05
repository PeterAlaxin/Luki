<?php
/**
 * Alerts class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Alerts
 * @filesource
 */

namespace Luki;

use Luki\Storage;

class Alerts
{
    private static $alerts  = array();
    private static $userKey = null;

    public static function addDanger($messagge, $label = '')
    {
        self::init();
        self::$alerts[] = array('type' => 'danger', 'messagge' => $messagge, 'label' => $label);
        self::save();
    }

    public static function addWarning($messagge, $label = '')
    {
        self::init();
        self::$alerts[] = array('type' => 'warning', 'messagge' => $messagge, 'label' => $label);
        self::save();
    }

    public static function addInfo($messagge, $label = '')
    {
        self::init();
        self::$alerts[] = array('type' => 'info', 'messagge' => $messagge, 'label' => $label);
        self::save();
    }

    public static function addSuccess($messagge, $label = '')
    {
        self::init();
        self::$alerts[] = array('type' => 'success', 'messagge' => $messagge, 'label' => $label);
        self::save();
    }

    public static function init()
    {
        if (empty(self::$userKey)) {
            self::$userKey = 'alerts_'.session_id();
        }

        if (Storage::isCache() and Storage::Cache()->Has(self::$userKey)) {
            self::$alerts = Storage::Cache()->Get(self::$userKey);
        }
    }

    public static function isAlerts()
    {
        self::init();
        $count = !empty(self::$alerts);

        return (bool) $count;
    }

    public static function getAlerts()
    {
        self::init();

        return self::$alerts;
    }

    public static function deleteAlerts()
    {
        self::$alerts = array();
        if (Storage::isCache()) {
            Storage::Cache()->Set(self::$userKey, self::$alerts);
        }
    }

    public static function save()
    {
        if (Storage::isCache()) {
            Storage::Cache()->Set(self::$userKey, self::$alerts);
        }
    }
}