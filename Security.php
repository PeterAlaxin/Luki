<?php
/**
 * Security class
 *
 * Methods textEncrypt and textDecrypt are inspired by Jaro Varga (http://jarovarga.name).
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Security
 * @filesource
 */

namespace Luki;

class Security
{
    private static $chars     = array(
        1 => '1234567890',
        2 => 'abcdefghijklmnopqrstuvwxyz',
        3 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        4 => '@#$%^&*');
    private static $salt      = '';
    private static $algorithm = 'sha256';
    private static $key       = '';

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public static function generatePassword($lenght = 8, $level = 4)
    {
        $password    = '';
        $actualLevel = 1;
        $usedLevels  = array(1 => false, 2 => false, 3 => false, 4 => false);

        if ($lenght < 4) {
            $lenght = 4;
        } elseif ($lenght > 64) {
            $lenght = 64;
        }
        if (!in_array($level, array(1, 2, 3, 4))) {
            $level = 4;
        }

        while (strlen($password) < $lenght) {
            while (true) {
                $actualLevel = rand(1, $level);
                if (strlen($password) < $level) {
                    if (!$usedLevels[$actualLevel]) {
                        break;
                    }
                } else {
                    break;
                }
            }

            $usedLevels[$actualLevel] = true;
            $chars                    = self::$chars[$actualLevel];
            $charsLength              = (strlen($chars) - 1);
            $char                     = $chars{rand(0, $charsLength)};

            if (0 == strlen($password) or $char != $password{strlen($password) - 1}) {
                $password .= $char;
            }
        }

        return $password;
    }

    public static function setSalt($newSalt = '')
    {
        if (empty($newSalt)) {
            $newSalt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
        }

        self::$salt = (string) $newSalt;
    }

    public static function getSalt()
    {
        if (empty(self::$salt)) {
            self::setSalt();
        }

        return self::$salt;
    }

    public static function setAlgorithm($algorithm = 'sha256')
    {
        self::$algorithm = (string) $algorithm;

        unset($algorithm);
    }

    public static function getAlgorithm()
    {
        return self::$algorithm;
    }

    static function generateHash($string = '')
    {
        $hashedString = '';

        if (!empty($string)) {
            if (function_exists('hash') and in_array(self::$algorithm, hash_algos())) {
                $hashedString = hash_hmac(self::$algorithm, $string, self::$salt);
            } else {
                $hashedString = sha1(self::$salt.$string);
            }
        }

        return $hashedString;
    }

    static function passwordHash($password, $cost = 10)
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => max(1, (int) $cost)));

        return $hash;
    }

    static function passwordVerify($password, $hash)
    {
        $isValid = password_verify($password, $hash);

        return $isValid;
    }

    static function setKey($key = '')
    {
        self::$key = md5($key);
    }

    static function getKey()
    {
        if (empty(self::$key)) {
            self::setKey();
        }

        return self::$key;
    }

    static function textEncrypt($text, $key1 = '', $key2 = '')
    {
        $key1Final = empty($key1) ? self::getKey() : md5($key1);
        $key2Final = empty($key2) ? md5(strrev($key1Final)) : md5($key2);

        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key1Final, $text, MCRYPT_MODE_CBC, $key2Final));

        return $encrypted;
    }

    static function textDecrypt($text, $key1 = '', $key2 = '')
    {
        $key1Final = empty($key1) ? self::getKey() : md5($key1);
        $key2Final = empty($key2) ? md5(strrev($key1Final)) : md5($key2);

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key1Final, base64_decode($text), MCRYPT_MODE_CBC, $key2Final);

        return $decrypted;
    }

    static function passwordStrength($password)
    {
        $score = 0;
        if (empty($password)) {
            return $score;
        }

        $letters = array();
        for ($i = 0; $i < strlen($password); $i++) {
            $letter = substr($password, $i, 1);
            if (!array_key_exists($letter, $letters)) {
                $letters[$letter] = 1;
            } else {
                $letters[$letter] += 1;
            }
            $score += 5 / $letters[$letter];
        }

        $variations     = array('/\d/', '/[a-z]/', '/[A-Z]/', '/\W/');
        $variationCount = 0;
        foreach ($variations as $check) {
            preg_match($check, $password, $results);
            if (!empty($results)) {
                $variationCount++;
            }
        }

        $score += ($variationCount - 1) * 10;
        $score = intval($score);

        return $score;
    }
}