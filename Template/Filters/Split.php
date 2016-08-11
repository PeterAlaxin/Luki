<?php
/**
 * Split template filter adapter
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Template
 * @filesource
 */

namespace Luki\Template\Filters;

class Split
{

    public function Get($value, $separator = '', $limit = 1)
    {
        switch (gettype($value)) {
            case 'string':
                if (empty($separator)) {
                    $split = $this->splitWithoutSeparator($value, $limit);
                } else {
                    $split = $this->splitWithSeparator($value, $separator, $limit);
                }
                break;
            default:
                $split = $value;
        }

        return $split;
    }

    private function splitWithoutSeparator($value, $limit)
    {
        if ($limit > 0) {
            $split = array();
            $len = mb_strlen($value, "UTF-8");
            for ($i = 0; $i < $len; $i += $limit) {
                $split[] = mb_substr($value, $i, $limit, "UTF-8");
            }
        } else {
            $split = preg_split("//u", $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        return $split;
    }

    private function splitWithSeparator($value, $separator, $limit)
    {
        $values = explode($separator, $value);

        if ($limit > 1) {
            $split = array();
            $last = array();
            foreach ($values as $key => $value) {
                if ($key + 1 < $limit) {
                    $split[] = $value;
                } else {
                    $last[] = $value;
                }
            }
            $split[] = implode($separator, $last);
        } else {
            $split = $values;
        }

        return $split;
    }
}
