<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Text extends EvaluatesMethods
{
    /**
     * Returns the character specified by a number
     *
     * @param int $asciiCode
     */
    public function char(int $asciiCode): string;
    
    /**
     * Removes all non-printable characters from a text string
     *
     * @param string $string
     */
    public function clean(string $string): string;
    
    /**
     * Returns a numeric code for the first character in a text string
     *
     * @param string $string
     */
    public function code(string $string): int;
    
    /**
     * Joins text strings into one text string
     */
    public function concatenate(): string;
    
    /**
     * Formats the given number in decimal format using a period and commas
     */
    public function fixed(float|int $number, int $decimals = 0, bool $commas = false): string;
    
    /**
     * Returns the first characters in a text string
     */
    public function left(string $string, int $chars): string;
    
    /**
     * Returns the number of characters in a text string
     */
    public function len(string $string): int;
    
    /**
     * Converts a text string to lowercase
     */
    public function lower(string $string): string;
    
    /**
     * Capitalizes the first letter of every word in a text string
     */
    public function proper(string $string): string;
    
    /**
     * Repeats text a given number of times
     */
    public function rept(string $string, int $times): string;
    
    /**
     * Returns the last characters in a text string
     */
    public function right(string $string, int $chars): string;
    
    /**
     * Substitutes $new for $old in $string. If $replaceAtIndex
     * is given, then only the instance at $replaceAtIndex (starting at index 1) will be substituted.
     */
    public function substitute(string $string, string $old, string $new, ?int $replaceAtIndex = null): string;
    
    /**
     * Returns the unicode character specified by a number
     */
    public function unichar(int $unicode): string;
    
    /**
     * Returns a numeric code for the first character in a text string
     */
    public function unicode(string $string): int;
    
    /**
     * Converts a text string to uppercase
     */
    public function upper(string $string): string;

    /**
     * Determines whether one string may be found within another string
     */
    public function contains(string $needle, string $haystack): bool;
}
