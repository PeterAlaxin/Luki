<?php
/**
 * One time password class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Geo
 * @filesource
 */

namespace Luki;

class Otp
{
    private $decodedSecretKey = '';
    private $hash;
    private $invertedMap = ['A' => '0', 'B' => '1', 'C' => '2', 'D' => '3', 'E' => '4',
        'F' => '5', 'G' => '6', 'H' => '7', 'I' => '8', 'J' => '9', 'K' => '10',
        'L' => '11', 'M' => '12', 'N' => '13', 'O' => '14', 'P' => '15', 'Q' => '16',
        'R' => '17', 'S' => '18', 'T' => '19', 'U' => '20', 'V' => '21', 'W' => '22',
        'X' => '23', 'Y' => '24', 'Z' => '25', '2' => '26', '3' => '27', '4' => '28',
        '5' => '29', '6' => '30', '7' => '31'];
    private $map = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
        'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '2', '3',
        '4', '5', '6', '7', '='];
    private $rangeInTime = 1;
    private $secretKey = '';
    private $size = 200;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;

        try {
            $this->decodeSecretKey();
        } catch (\Exception $exception) {
            exit($exception->getMessage());
        }
    }

    public function verify($code)
    {
        $result = in_array((int) $code, $this->getTokens());

        return $result;
    }

    public function getTokens()
    {
        $tokens = [];
        $unixtimestamp = time() / 30;

        for ($i = -($this->rangeInTime); $i <= $this->rangeInTime; $i++) {
            $this->oathHotp((int) ($unixtimestamp + $i));
            $tokens[] = $this->oathTruncate();
        }

        return $tokens;
    }

    public function getQRCode($appName, $userName = '')
    {
        $url = 'http://chart.apis.google.com/chart';
        $url = $url.'?chs='.$this->size.'x'.$this->size.'&chld=M|0&cht=qr&chl=otpauth://totp/';
        $url = $url.rawurlencode($userName).'%3Fsecret%3D'.$this->secretKey.'%26issuer%3D'.rawurlencode($appName);

        return $url;
    }

    public function setQRCodeSize($size)
    {
        $this->size = min(600, max(100, (int) $size));

        return $this;
    }

    public static function generateSecretKey($length = 16)
    {
        $b32 = '1234567890QWERTYUIOPASDFGHJKLZXCVBNM';
        $key = '';

        for ($i = 0; $i < min(128, max(8, (int) $length)); $i++) {
            $key .= $b32[rand(0, 35)];
        }

        return $key;
    }

    private function decodeSecretKey()
    {
        if (!$this->checkSecretKey()) {
            throw new \Exception('Wrong secret key! Generate new with generateSecretKey() method.');
        }

        $secretKey = str_split(str_replace('=', '', $this->secretKey));

        for ($i = 0; $i < count($secretKey); $i = $i + 8) {
            $x = '';

            if (!in_array($secretKey[$i], $this->map)) {
                throw new \Exception('Wrong secret key! Generate new with generateSecretKey() method.');
            }

            for ($j = 0; $j < 8; $j++) {
                $x .= str_pad(base_convert($this->invertedMap[$secretKey[$i + $j]],
                        10, 2), 5, '0', STR_PAD_LEFT);
            }

            $eightBits = str_split($x, 8);

            for ($z = 0; $z < count($eightBits); $z++) {
                $this->decodedSecretKey .= (($y = chr(base_convert($eightBits[$z],
                        2, 10))) or ord($y) == 48 ) ? $y : "";
            }
        }
    }

    private function checkSecretKey()
    {
        $result = true;
        $allowedValues = [6, 4, 3, 1, 0];

        $paddingCharCount = substr_count($this->secretKey, $this->map[32]);

        if (!in_array($paddingCharCount, $allowedValues)) {
            $result = false;
        } else {
            for ($i = 0; $i < 4; $i++) {
                if ($paddingCharCount == $allowedValues[$i] and substr($this->secretKey,
                        -(allowedValues[$i])) != str_repeat($this->map[32],
                        $allowedValues[$i])) {
                    $result = false;
                }
            }
        }

        return $result;
    }

    private function oathHotp($counter)
    {
        $cur_counter = [0, 0, 0, 0, 0, 0, 0, 0];

        for ($i = 7; $i >= 0; $i--) {
            $cur_counter[$i] = pack('C*', $counter);
            $counter = $counter >> 8;
        }

        $binary = implode($cur_counter);
        str_pad($binary, 8, chr(0), STR_PAD_LEFT);

        $this->hash = hash_hmac('sha1', $binary, $this->decodedSecretKey);
    }

    private function oathTruncate()
    {
        $hashcharacters = str_split($this->hash, 2);

        for ($j = 0; $j < count($hashcharacters); $j++) {
            $hmac_result[] = hexdec($hashcharacters[$j]);
        }

        $offset = $hmac_result[19] & 0xf;

        $result = (
            (($hmac_result[$offset + 0] & 0x7f) << 24 ) |
            (($hmac_result[$offset + 1] & 0xff) << 16 ) |
            (($hmac_result[$offset + 2] & 0xff) << 8 ) |
            ($hmac_result[$offset + 3] & 0xff)
            ) % pow(10, 6);

        return $result;
    }
}