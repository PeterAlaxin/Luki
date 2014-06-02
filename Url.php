<?php

/**
 * URL class for SEO
 *
 * Luki framework
 * Date 7.12.2012
 *
 * @version 3.0.0
 *
 * @author Peter Alaxin, <alaxin@almex.sk>
 * @copyright (c) 2009, Almex spol. s r.o.
 * * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Class
 * @filesource
 */

namespace Luki;

/**
 * URL class for SEO
 *
 * @package Luki
 */
class Url
{

    public static function makeLink($value)
    {
        $link = '';

        if ( is_string($value) ) {
            $link = mb_strtolower($value, 'UTF-8');
            $link = html_entity_decode($link, ENT_QUOTES, 'UTF-8');
            $link = preg_replace("/[^a-z0-9-._~:?#[\]@!$&'()*+:=ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïñòóôõöøùúûüýÿĀāĂăĄąĆćĈĉĊċČčĎďĐđĒēĔĕĖėĘęĚěĜĝĞğĠġĢģĤĥĦħĨĩĪīĬĭĮįİıĲĳĴĵĶķĹĺĻļĽľĿŀŁłŃńŅņŇňŉŌōŎŏŐőŒœŔŕŖŗŘřŚśŜŝŞşŠšŢţŤťŦŧŨũŪūŬŭŮůŰűŲųŴŵŶŷŸŹźŻżŽžſƒƠơƯưǍǎǏǐǑǒǓǔǕǖǗǘǙǚǛǜǺǻǼǽǾǿ]/u", '-', $link);
            $link = str_replace('&', '-and-', $link);
            $link = urlencode($link);
            $link = str_replace('+', '-', $link);
            $link = preg_replace('/--+/u', '-', $link);
            $link = trim($link, '.-_');
            $link = urldecode($link);
        } elseif ( is_array($value) ) {

            foreach ( $value as $linkPart ) {
                $link .= self::makeLink((string) $linkPart) . '/';
            }
        } else {
            $link = self::makeLink((string) $value);
        }

        unset($value, $linkPart);
        return $link;
    }

    static public function Reload($link = '', $response = 302)
    {
        if ( !empty($link) ) {
            header('Location: ' . $link, TRUE, $response);
            exit;
        }
    }

}

# End of file