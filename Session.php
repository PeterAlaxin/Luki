<?php

/**
 * Session class
 *
 * Luki framework
 * Date 30.11.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

use Luki\Storage;

/**
 * Session class
 *
 * Session management
 *
 * @package Luki
 */
class Session
{

    public static $limiters = array( 'public',
      'private_no_expire',
      'private',
      'nocache' );

    public static function Start($chacheType = 'nocache')
    {
        if ( !in_array($chacheType, self::$limiters) ) {
            $chacheType = 'nocache';
        }

        session_cache_limiter($chacheType);
        session_set_cookie_params(0, "/", NULL, FALSE, TRUE);

        session_start();
        $sessionId = session_id();

        if ( Storage::isProfiler() ) {
            Storage::Profiler()->Add('Session', $sessionId);
        }

        unset($chacheType);
        return $sessionId;
    }

    public static function Restart()
    {
        if ( !isset($_SESSION) ) {
            self::Start();
        }

        $sessionIds = array( 'old' => session_id() );

        session_regenerate_id(TRUE);
        $sessionIds['new'] = session_id();

        if ( Storage::isProfiler() ) {
            Storage::Profiler()->Add('Session', $sessionIds['new']);
        }

        return $sessionIds;
    }

    public static function Destroy()
    {
        $isDestroyed = FALSE;
        $sessionName = session_name();
        $sessionCookie = session_get_cookie_params();

        self::Restart();
        if ( session_destroy() ) {
            setcookie(
                    $sessionName, false, $sessionCookie['lifetime'], $sessionCookie['path'], $sessionCookie['domain'], $sessionCookie['secure']
            );
            $isDestroyed = TRUE;

            if ( Storage::isProfiler() ) {
                Storage::Profiler()->Add('Session', session_id());
            }
        }

        unset($sessionCookie, $sessionName);
        return $isDestroyed;
    }

}

# End of file