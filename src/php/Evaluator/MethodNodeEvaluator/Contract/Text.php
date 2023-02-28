<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Text extends EvaluatesMethods
{
    /**
     * Returns the character specified by a number
     *
     * @param int $asciiCode
     * @return string
     */
    public function char($asciiCode): string;
    
    /**
     * Removes all non-printable characters from a text string
     *
     * @param string $string
     * @return string
     */
    public function clean($string): string;
    
    /**
     * Returns a numeric code for the first character in a text string
     *
     * @param string $string
     * @return int
     */
    public function code($string): int;
    
    /**
     * Joins text strings into one text string
     *
     * @param string[] ...$args
     * @return string
     */
    public function concatenate(): string;
    
    /**
     * Formats the given number in decimal format using a period and commas
     *
     * @param int $number
     * @param int $decimals
     * @param bool $commas
     * @return string
     */
    public function fixed($number, $decimals = 0, $commas = false): string;
    
    /**
     * Returns the first characters in a text string
     *
     * @param string $string
     * @param int $chars
     * @return string
     */
    public function left($string, $chars): string;
    
    /**
     * Returns the number of characters in a text string
     *
     * @param string $string
     * @return int
     */
    public function len($string): int;
    
    /**
     * Converts a text string to lowercase
     *
     * @param string $string
     * @return string
     */
    public function lower($string): string;
    
    /**
     * Capitalizes the first letter of every word in a text string
     *
     * @param string $string
     * @return string
     */
    public function proper($string): string;
    
    /**
     * Repeats text a given number of times
     *
     * @param string $string
     * @param int $times
     * @return string
     */
    public function rept($string, $times): string;
    
    /**
     * Returns the last characters in a text string
     *
     * @param string $string
     * @param int $chars
     * @return string
     */
    public function right($string, $chars): string;
    
    /**
     * Substitutes new_text for old_text in a text string. If instance_num
     * is given, then only that instance will be substituted
     *
     * @param string $string
     * @param string $old
     * @param string $new
     * @param int $instances
     * @return string
     */
    public function substitute($string, $old, $new, $instances = null): string;
    
    /**
     * Returns the unicode character specified by a number
     *
     * @param int $unicode
     * @return string
     */
    public function unichar($unicode): string;
    
    /**
     * Returns a numeric code for the first character in a text string
     *
     * @param string $string
     * @return int
     */
    public function unicode($string): int;
    
    /**
     * Converts a text string to uppercase
     *
     * @param string $string
     * @return string
     */
    public function upper($string): string;

    /**
     * Determines whether one string may be found within another string
     *
     * @param string $needle
     * @param string $haystack
     *
     * @return boolean
     */
    public function contains($needle, $haystack): bool;
}
