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

    public static function addDanger($messagge)
    {
        self::init();
        $alerts = Storage::Get('alerts');
        $alerts['danger'][] = $messagge;
        Storage::Set('alerts', $alerts);
    }

    public static function addWarning($messagge)
    {
        self::init();
        $alerts = Storage::Get('alerts');
        $alerts['warning'][] = $messagge;
        Storage::Set('alerts', $alerts);
    }

    public static function addInfo($messagge)
    {
        self::init();
        $alerts = Storage::Get('alerts');
        $alerts['info'][] = $messagge;
        Storage::Set('alerts', $alerts);
    }

    public static function addSuccess($messagge)
    {
        self::init();
        $alerts = Storage::Get('alerts');
        $alerts['success'][] = $messagge;
        Storage::Set('alerts', $alerts);
    }

    public static function init()
    {
        if (!Storage::isSaved('alerts')) {
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
        $alerts = Storage::Get('alerts');
        $count = count($alerts['danger']) + count($alerts['warning']) + count($alerts['info']) + count($alerts['success']);

        return (bool) $count;
    }
}
