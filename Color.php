<?php
/**
 * Color class
 *
 * Luki framework
 *
 * @author Peter Alaxin, <peter@lavien.sk>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 *
 * @package Luki
 * @subpackage Color
 * @filesource
 */

namespace Luki;

class Color
{

	const STEP = 15;

	private $hex;
	private $rgb;
	private $hsv;
	private $hsl;
	private $inverse;
	private $complementary;
	private $darker;
	private $lighter;

	public function __construct($hex)
	{
		$this->hex = self::clear($hex);
		$this->rgb = self::hex2rgb($this->hex);
		$this->hsv = self::rgb2hsv($this->rgb['r'], $this->rgb['g'], $this->rgb['b']);
		$this->hsl = self::rgb2hsl($this->rgb['r'], $this->rgb['g'], $this->rgb['b']);
		$this->darker = self::darken($this->hex);
		$this->lighter = self::lighten($this->hex);
		$this->inverse = self::inverse($this->hex);
		$this->complementary = self::complementary($this->hex);
	}

	public function getHEX()
	{
		return '#'.$this->hex;
	}

	public function getRGB()
	{
		return $this->rgb;
	}

	public function getHSV()
	{
		return $this->hsv;
	}

	public function getHSL()
	{
		return $this->hsl;
	}

	public function getInverse()
	{
		return $this->inverse;
	}

	public function getComplementary()
	{
		return $this->complementary;
	}

	public function getDarker()
	{
		return $this->darker;
	}

	public function getLighter()
	{
		return $this->lighter;
	}

	public function getGradient($amount = self::STEP)
	{
		$gradient = array(
			'lighter' => self::lighten($this->hex, $amount),
			'middle' => $this->getHEX(),
			'darker' => self::darken($this->hex, $amount),
		);

		return $gradient;
	}

	public function getColorDifference($hex)
	{
		$rgb1 = self::hex2rgb($hex);
		$rgb2 = $this->getRGB();

		$red = array($rgb1['r'], $rgb2['r']);
		$green = array($rgb1['g'], $rgb2['g']);
		$blue = array($rgb1['b'], $rgb2['b']);

		$difference = (max($red) - min($red)) + (max($green) - min($green)) + (max($blue) - min($blue));

		return $difference;
	}

	public function getContrastRatio($hex)
	{
		$ratio = 0;
		$l1 = self::getLuminance($this->getHEX());
		$b1 = self::getBrightness($this->getHEX());
		$l2 = self::getLuminance($hex);
		$b2 = self::getBrightness($hex);

		if ($b1 < $b2) {
			$ratio = round(($l2 + 0.05) / ($l1 + 0.05), 1);
		} else {
			$ratio = round(($l1 + 0.05) / ($l2 + 0.05), 1);
		}

		return $ratio;
	}

	public function isVisible($hex)
	{
		$b1 = self::getBrightness($this->getHEX());
		$b2 = self::getBrightness($hex);
		$isBrightnessDifference = (abs($b1 - $b2) > 125);

		$isContrastRatio = ($this->getContrastRatio($hex) > 4.5);
		$isColorDifference = ($this->getColorDifference($hex) > 500);

		$isVisible = ($isBrightnessDifference and $isContrastRatio and $isColorDifference);

		return $isVisible;
	}

	public static function clear($hex)
	{
		$color = str_replace('#', '', $hex);
		if (3 == strlen($color)) {
			$color = substr($color, 0, 1).substr($color, 0, 1).
				substr($color, 1, 1).substr($color, 1, 1).
				substr($color, 2, 1).substr($color, 2, 1);
		}

		return $color;
	}

	public static function hex2rgb($hex)
	{
		$color = self::clear($hex);
		$rgb = array(
			'r' => hexdec(substr($color, 0, 2)),
			'g' => hexdec(substr($color, 2, 2)),
			'b' => hexdec(substr($color, 4, 2))
		);

		return $rgb;
	}

	public static function hex2hsl($hex)
	{
		$rgb = self::hex2rgb($hex);
		$hsl = self::rgb2hsl($rgb['r'], $rgb['g'], $rgb['b']);

		return $hsl;
	}

	public static function hex2hsv($hex)
	{
		$rgb = self::hex2rgb($hex);
		$hsv = self::rgb2hsv($rgb['r'], $rgb['g'], $rgb['b']);

		return $hsv;
	}

	public static function rgb2hex($r, $g, $b)
	{
		$rHex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$gHex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$bHex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
		$hex = "#".$rHex.$gHex.$bHex;

		return $hex;
	}

	public static function rgb2hsl($r, $g, $b)
	{
		$hsl = array();
		$red = ($r / 255);
		$green = ($g / 255);
		$blue = ($b / 255);

		$min = min($red, $green, $blue);
		$max = max($red, $green, $blue);
		$delta = $max - $min;

		$hsl['l'] = ($max + $min) / 2;
		if ($delta == 0) {
			$hsl['h'] = 0;
			$hsl['s'] = 0;
		} else {
			if ($hsl['l'] < 0.5) {
				$hsl['s'] = $delta / ($max + $min);
			} else {
				$hsl['s'] = $delta / (2 - $max - $min);
			}

			$rDelta = ((($max - $red) / 6) + ($delta / 2)) / $delta;
			$gDelta = ((($max - $green) / 6 ) + ($delta / 2)) / $delta;
			$bDelta = ((($max - $blue) / 6 ) + ($delta / 2)) / $delta;

			if ($red == $max) {
				$hsl['h'] = $bDelta - $gDelta;
			} else if ($green == $max) {
				$hsl['h'] = (1 / 3) + $rDelta - $bDelta;
			} else if ($blue == $max) {
				$hsl['h'] = (2 / 3) + $gDelta - $rDelta;
			}

			if ($hsl['h'] < 0) {
				$hsl['h'] ++;
			}
			if ($hsl['h'] > 1) {
				$hsl['h'] --;
			}
		}

		$hsl['h'] = round($hsl['h'] * 360, 1);
		$hsl['s'] = round($hsl['s'] * 100, 1);
		$hsl['l'] = round($hsl['l'] * 100, 1);

		return $hsl;
	}

	public static function rgb2hsv($r, $g, $b)
	{
		$r = ($r / 255);
		$g = ($g / 255);
		$b = ($b / 255);

		$max = max($r, $g, $b);
		$min = min($r, $g, $b);
		$chroma = $max - $min;
		$hsv = array('v' => round(100 * $max, 1));

		if ($chroma == 0) {
			$hsv['h'] = 0;
			$hsv['s'] = 0;
		} else {
			$hsv['s'] = round(100 * ($chroma / $max), 1);

			if ($r == $min) {
				$hsv['h'] = round(60 * (3 - (($g - $b) / $chroma)), 1);
			} elseif ($b == $min) {
				$hsv['h'] = round(60 * (1 - (($r - $g) / $chroma)), 1);
			} else {
				$hsv['h'] = round(60 * (5 - (($b - $r) / $chroma)), 1);
			}
		}

		return $hsv;
	}

	public static function hsl2rgb($h, $s, $l)
	{
		$h /= 360;
		$s /=100;
		$l /=100;

		$rgb = array('r' => $l, 'g' => $l, 'b' => $l);
		$rgb['r'] = $l;
		$rgb['g'] = $l;
		$rgb['b'] = $l;
		$v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
		if ($v > 0) {
			$m = $l + $l - $v;
			$sv = ($v - $m ) / $v;
			$h *= 6.0;
			$sextant = floor($h);
			$fract = $h - $sextant;
			$vsf = $v * $sv * $fract;
			$mid1 = $m + $vsf;
			$mid2 = $v - $vsf;

			switch ($sextant) {
				case 0:
					$rgb['r'] = $v;
					$rgb['g'] = $mid1;
					$rgb['b'] = $m;
					break;
				case 1:
					$rgb['r'] = $mid2;
					$rgb['g'] = $v;
					$rgb['b'] = $m;
					break;
				case 2:
					$rgb['r'] = $m;
					$rgb['g'] = $v;
					$rgb['b'] = $mid1;
					break;
				case 3:
					$rgb['r'] = $m;
					$rgb['g'] = $mid2;
					$rgb['b'] = $v;
					break;
				case 4:
					$rgb['r'] = $mid1;
					$rgb['g'] = $m;
					$rgb['b'] = $v;
					break;
				case 5:
					$rgb['r'] = $v;
					$rgb['g'] = $m;
					$rgb['b'] = $mid2;
					break;
			}
		}

		$rgb['r'] = round($rgb['r'] * 255);
		$rgb['g'] = round($rgb['g'] * 255);
		$rgb['b'] = round($rgb['b'] * 255);

		return $rgb;
	}

	public static function hsl2hex($h, $s, $l)
	{
		$rgb = self::hsl2rgb($h, $s, $l);
		$hex = self::rgb2hex($rgb['r'], $rgb['g'], $rgb['b']);

		return $hex;
	}

	public static function hsl2hsv($h, $s, $l)
	{
		$rgb = self::hsl2rgb($h, $s, $l);
		$hsv = self::rgb2hsl($rgb['r'], $rgb['g'], $rgb['b']);

		return $hsv;
	}

	public static function hsv2rgb($h, $s, $v)
	{
		$saturation = $s / 100;
		$lightness = $v / 100;
		$chroma = $lightness * $saturation;
		$prime = $h / 60;
		$temperature = $prime;

		while ($temperature >= 2) {
			$temperature -= 2;
		}

		$m = $chroma * (1 - abs($temperature - 1));

		switch (floor($prime)) {
			case 0:
				$rgb = array('r' => $chroma, 'g' => $m, 'b' => 0);
				break;
			case 1:
				$rgb = array('r' => $m, 'g' => $chroma, 'b' => 0);
				break;
			case 2:
				$rgb = array('r' => 0, 'g' => $chroma, 'b' => $m);
				break;
			case 3:
				$rgb = array('r' => 0, 'g' => $m, 'b' => $chroma);
				break;
			case 4:
				$rgb = array('r' => $m, 'g' => 0, 'b' => $chroma);
				break;
			case 5:
				$rgb = array('r' => $chroma, 'g' => 0, 'b' => $m);
				break;
			default:
				$rgb = array('r' => 0, 'g' => 0, 'b' => 0);
				break;
		}

		$modulo = $lightness - $chroma;
		$rgb['r'] = round(($rgb['r'] + $modulo) * 255);
		$rgb['g'] = round(($rgb['g'] + $modulo) * 255);
		$rgb['b'] = round(($rgb['b'] + $modulo) * 255);

		return $rgb;
	}

	public static function hsv2hex($h, $s, $v)
	{
		$rgb = self::hsv2rgb($h, $s, $v);
		$hex = self::rgb2hex($rgb['r'], $rgb['g'], $rgb['b']);

		return $hex;
	}

	public static function hsv2hsl($h, $s, $v)
	{
		$rgb = self::hsv2rgb($h, $s, $v);
		$hsl = self::rgb2hsl($rgb['r'], $rgb['g'], $rgb['b']);

		return $hsl;
	}

	public static function complementary($hex)
	{
		$hsl = self::hex2hsl($hex);
		$hsl['h'] += ($hsl['h'] > 180) ? -180 : 180;
		$complementary = self::hsl2hex($hsl['h'], $hsl['s'], $hsl['l']);

		return $complementary;
	}

	public static function inverse($hex)
	{
		$rgb = self::hex2rgb($hex);
		$rgb['r'] = 255 - $rgb['r'];
		$rgb['g'] = 255 - $rgb['g'];
		$rgb['b'] = 255 - $rgb['b'];
		$inverse = self::rgb2hex($rgb['r'], $rgb['g'], $rgb['b']);

		return $inverse;
	}

	public static function darken($hex, $amount = self::STEP)
	{
		$hsl = self::hex2hsl($hex);

		if (!empty($amount)) {
			$hsl['l'] = $hsl['l'] - $amount;
			$hsl['l'] = ($hsl['l'] < 0) ? 0 : $hsl['l'];
		} else {
			$hsl['l'] = round($hsl['l'] / 2, 1);
		}

		$darken = self::hsl2hex($hsl['h'], $hsl['s'], $hsl['l']);

		return $darken;
	}

	public static function lighten($hex, $amount = self::STEP)
	{
		$hsl = self::hex2hsl($hex);

		if (!empty($amount)) {
			$hsl['l'] = $hsl['l'] + $amount;
			$hsl['l'] = ($hsl['l'] > 100) ? 100 : $hsl['l'];
		} else {
			$hsl['l'] += round($hsl['l'] / 2);
		}

		$lighten = self::hsl2hex($hsl['h'], $hsl['s'], $hsl['l']);

		return $lighten;
	}

	public static function getLuminance($hex)
	{
		$rgb = self::hex2rgb($hex);
		$components = array(
			'r' => $rgb['r'] / 255,
			'g' => $rgb['g'] / 255,
			'b' => $rgb['b'] / 255
		);
		foreach ($components as $c => $v) {
			if ($v <= 0.03928) {
				$components[$c] = $v / 12.92;
			} else {
				$components[$c] = pow((($v + 0.055) / 1.055), 2.4);
			}
		}
		$luminance = ($components['r'] * 0.2126) + ($components['g'] * 0.7152) + ($components['b'] * 0.0722);

		return $luminance;
	}

	public static function getBrightness($hex)
	{
		$rgb = self::hex2rgb($hex);
		$brightnes = (($rgb['r'] * 299) + ($rgb['g'] * 587) + ($rgb['b'] * 114)) / 1000;

		return $brightnes;
	}
}
