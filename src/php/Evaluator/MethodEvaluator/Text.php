<?php

namespace Floip\Evaluator\MethodEvaluator;

use Floip\Evaluator\MethodEvaluator\Contract\Text as TextInterface;

class Text extends AbstractMethodHandler implements TextInterface
{
    public function char($asciiCode)
    {
        return chr($asciiCode);
    }
    public function clean($string)
    {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }
    public function code($string)
    {
        return ord($string);
    }
    public function concatenate()
    {
        return implode(\func_get_args());
    }
    public function fixed($number, $decimals = 0, $commas = false)
    {
        // number_format on its own will round numbers
        $number = \bcdiv($number, 1, $decimals);
        return number_format($number, $decimals, '.', '');
    }
    public function left($string, $chars)
    {
        return substr($string, 0, $chars);
    }
    public function len($string)
    {
        return strlen($string);
    }
    public function lower($string)
    {
        return \strtolower($string);
    }
    public function proper($string)
    {
        return ucwords(strtolower($string));
    }
    public function rept($string, $times)
    {
        return str_repeat($string, $times);
    }
    public function right($string, $chars)
    {
        return substr($string, -($chars));
    }
    public function substitute($string, $old, $new, $instances = null)
    {
        return str_replace($old, $new, $string, $instances);
    }
    /**
     * @todo php 7.2 could use mb_char
     */
    public function unichar($unicode)
    {
        // 
    }
    /**
     * @todo php 7.2 could use mb_ord
     */
    public function unicode($string)
    {
        // 
    }
    public function upper($string)
    {
        return \strtoupper($string);
    }
}
