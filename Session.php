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

    public static function Start($limiter = 'nocache', $lifetime = 0, $path = '/', $domain = null, $secure = false, $httponly = true)
    {
        if (!in_array($limiter, self::$limiters)) {
            $limiter = 'nocache';
        }

        session_cache_limiter($limiter);
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);

        session_start();
        setcookie(session_name(), session_id(), $lifetime);
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
        setcookie(session_name(), session_id(), 0);
        $sessionIds['new'] = session_id();

        if (Storage::isProfiler()) {
            Storage::Profiler()->Add('Session', $sessionIds['new']);
        }

        return $sessionIds;
    }

    public static function Destroy()
    {
        $params      = session_get_cookie_params();
        $isDestroyed = session_destroy();

        if ($isDestroyed) {
            setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
        }

        return $isDestroyed;
    }
}