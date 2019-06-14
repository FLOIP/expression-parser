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
    public function concatenate(array $strings)
    {
        return implode($strings);
    }
    public function fixed($number, $decimals = 0, $commas = false)
    {
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
        return ucwords($string);
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
        return str_replace($string, $old, $new, $instances);
    }
    public function unichar($unicode)
    {
        // php 7.2 could use mb_char
    }
    public function unicode($string)
    {
        // php 7.2 could use mb_ord
    }
    public function upper($string)
    {
        return \strtoupper($string);
    }
}
