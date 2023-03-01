<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Rector\Core\Exception\NotImplementedYetException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Text as TextInterface;
use function bcdiv;
use function func_get_args;
use function preg_quote;
use function preg_replace;
use function strtolower;
use function strtoupper;

class Text extends AbstractMethodHandler implements TextInterface
{
    
    public function char(int $asciiCode): string {
        return chr($asciiCode);
    }
    
    public function clean(string $string): string {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }
    
    public function code(string $string): int {
        return ord($string);
    }
    
    public function concatenate(): string {
        return implode(func_get_args());
    }
    
    public function fixed(float|int $number, int $decimals = 0, bool $commas = false): string {
        // number_format on its own will round numbers
        $number = bcdiv($number, 1, $decimals);
        return number_format($number, $decimals, '.', '');
    }
    
    public function left(string $string, int $chars): string {
        return substr($string, 0, $chars);
    }
    
    public function len(string $string): int {
        return strlen($string);
    }
    
    public function lower(string $string): string {
        return strtolower($string);
    }
    
    public function proper(string $string): string {
        return ucwords(strtolower($string));
    }
    
    public function rept(string $string, int $times): string {
        return str_repeat($string, $times);
    }
    
    public function right(string $string, int $chars): string {
        return substr($string, -($chars));
    }
    
    public function substitute(string $string, string $old, string $new, ?int $replaceAtIndex = null): string {
        if ($replaceAtIndex === null) {
            return str_replace($old, $new, $string);
        } else {
            $needle = preg_quote($old, null);
            // Replace the $replaceAtIndex'th occurence
            /** @noinspection RegExpUnnecessaryNonCapturingGroup */
            return preg_replace("/^((?:(?:.*?$needle){" . --$replaceAtIndex . "}.*?))$needle/", "$1$new", $string);
        }
    }
    
    /**
     * @todo php 7.2 could use mb_char
     */
    public function unichar(int $unicode): string {
        throw new NotImplementedYetException();
    }
    
    /**
     * @todo php 7.2 could use mb_ord
     */
    public function unicode(string $string): int {
        throw new NotImplementedYetException();
    }
    
    public function upper(string $string): string {
        return strtoupper($string);
    }
    
    public function contains(string $needle, string $haystack): bool {
        return (str_contains($haystack, $needle));
    }
}
