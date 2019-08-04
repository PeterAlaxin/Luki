<?php
/**
 * Math class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Math
 * @filesource
 */

namespace Luki;

class Math
{
    private $result;

    public function __construct()
    {
        if (!function_exists('bcadd')) {
            throw new \Exception('BC Math library not installed.');
        }

        $this->reset();
    }

    public function __destruct()
    {
        foreach ($this as &$value) {
            $value = null;
        }
    }

    public function reset()
    {
        bcscale(2);
        $this->result = bcadd(0, 0);

        return $this;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setDecimals(int $decimals = 2)
    {
        bcscale($decimals);

        return $this;
    }

    public function add($value)
    {
        $this->result = bcadd($this->result, $value);

        return $this;
    }

    public function sub($value)
    {
        $this->result = bcsub($this->result, $value);

        return $this;
    }

    public function subX($value)
    {
        $this->result = bcsub($value, $this->result);

        return $this;
    }

    public function mul($value)
    {
        $this->result = bcmul($this->result, $value);

        return $this;
    }

    public function div($value)
    {
        $this->result = bcdiv($this->result, $value);

        return $this;
    }

    public function divX($value)
    {
        $this->result = bcdiv($value, $this->result);

        return $this;
    }

    public function mod($value)
    {
        $this->result = bcmod($this->result, $value);

        return $this;
    }

    public function modX($value)
    {
        $this->result = bcmod($value, $this->result);

        return $this;
    }

    public function pow($value)
    {
        $this->result = bcpow($this->result, $value);

        return $this;
    }

    public function powX($value)
    {
        $this->result = bcpow($value, $this->result);

        return $this;
    }

    public function sqrt()
    {
        $this->result = bcsqrt($this->result);

        return $this;
    }
}