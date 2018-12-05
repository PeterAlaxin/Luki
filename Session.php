<?php
/**
 * Session class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Session
 * @filesource
 */

namespace Luki;

use Luki\Storage;

class Session
{
    public static $limiters = array('public', 'private_no_expire', 'private', 'nocache');

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function Start($chacheType = 'nocache')
    {
        if (!in_array($chacheType, self::$limiters)) {
            $chacheType = 'nocache';
        }

        session_cache_limiter($chacheType);
        session_set_cookie_params(0, "/", null, false, true);

        session_start();
        $sessionId = session_id();

        if (Storage::isProfiler()) {
            Storage::Profiler()->Add('Session', $sessionId);
        }

        return $sessionId;
    }

    public static function Restart()
    {
        if (!isset($_SESSION)) {
            self::Start();
        }

        $sessionIds = array('old' => session_id());

        session_regenerate_id(true);
        $sessionIds['new'] = session_id();

        if (Storage::isProfiler()) {
            Storage::Profiler()->Add('Session', $sessionIds['new']);
        }

        return $sessionIds;
    }

    public static function Destroy()
    {
        $isDestroyed   = false;
        $sessionName   = session_name();
        $sessionCookie = session_get_cookie_params();

        self::Restart();
        if (session_destroy()) {
            setcookie(
                $sessionName, false, $sessionCookie['lifetime'], $sessionCookie['path'], $sessionCookie['domain'],
                $sessionCookie['secure']
            );
            $isDestroyed = true;

            if (Storage::isProfiler()) {
                Storage::Profiler()->Add('Session', session_id());
            }
        }

        return $isDestroyed;
    }
}