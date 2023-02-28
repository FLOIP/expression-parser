<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Rector\Core\Exception\NotImplementedYetException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Text as TextInterface;

class Text extends AbstractMethodHandler implements TextInterface
{
    
    public function char($asciiCode): string {
        return chr($asciiCode);
    }
    
    public function clean($string): string {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }
    
    public function code($string): int {
        return ord($string);
    }
    
    public function concatenate(): string {
        return implode(\func_get_args());
    }
    
    public function fixed($number, $decimals = 0, $commas = false): string {
        // number_format on its own will round numbers
        $number = \bcdiv($number, 1, $decimals);
        return number_format($number, $decimals, '.', '');
    }
    
    public function left($string, $chars): string {
        return substr($string, 0, $chars);
    }
    
    public function len($string): int {
        return strlen($string);
    }
    
    public function lower($string): string {
        return \strtolower($string);
    }
    
    public function proper($string): string {
        return ucwords(strtolower($string));
    }
    
    public function rept($string, $times): string {
        return str_repeat($string, $times);
    }
    
    public function right($string, $chars): string {
        return substr($string, -($chars));
    }
    
    public function substitute($string, $old, $new, $instances = null): string {
        return str_replace($old, $new, $string, $instances);
    }
    
    /**
     * @todo php 7.2 could use mb_char
     */
    public function unichar($unicode): string {
        throw new NotImplementedYetException();
    }
    
    /**
     * @todo php 7.2 could use mb_ord
     */
    public function unicode($string): int {
        throw new NotImplementedYetException();
    }
    
    public function upper($string): string {
        return \strtoupper($string);
    }
    
    public function contains($needle, $haystack): bool {
        return (strpos($haystack, $needle) !== false);
    }
}
