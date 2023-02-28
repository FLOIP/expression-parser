<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Excellent extends EvaluatesMethods
{
    const PUNCTUATION=',:;!?.-';
    /**
     * Returns the first word in the given text - equivalent to WORD(text, 1).
     * Note: n is 1 indexed.
     */
    public function firstWord(string $string): string;

    /**
     * Formats a number as a percentage
     */
    public function percent(int|float $number): string;

    /**
     * Formats digits in text for reading in TTS
     */
    public function readDigits(string $string): mixed;

    /**
     * Removes the first word from the given text. The remaining text will
     * be unchanged
     */
    public function removeFirstWord(string $string): string;

    /**
     * Extracts the nth word from the given text string. If stop is a negative
     * number, then it is treated as count backwards from the end of the text.
     * If by_spaces is specified and is TRUE then the function splits the text
     * into words only by spaces. Otherwise the text is split by punctuation
     * characters as well.
     * Note: n is 1 indexed.
     *
     * @param string $string
     * @param int $number
     * @param bool|null $bySpaces
     */
    public function word(string $string, int $number, bool $bySpaces = null): string;

    /**
     * Returns the number of words in the given text string. If by_spaces is
     * specified and is TRUE then the functionsplits the text into words only
     * by spaces. Otherwise the text is split by punctuation characters as well
     *
     * @param string $string
     * @param bool|null $bySpaces
     */
    public function wordCount(string $string, bool $bySpaces = null): int;

    /**
     * Extracts a substring of the words beginning at start, and up to but
     * not-including stop. If stop is omitted then the substring will be all
     * words from start until the end of the text. If stop is a negative
     * number, then it is treated as count backwards from the end of the text.
     * If by_spaces is specified and is TRUE then the function splits the text
     * into words only by spaces. Otherwise the text is split by punctuation
     * characters as well
     *
     * @param string $string
     * @param int $start
     * @param int|null $stop
     * @param int|null $bySpaces
     */
    public function wordSlice(string $string, int $start, int $stop = null, int $bySpaces = null): string;

    /**
     * Return trues if a value is a number, false otherwise.
     */
    public function isNumber(mixed $value): bool;

    /**
     * Return trues if a value is a string, false otherwise.
     */
    public function isString(mixed $value): bool;

    /**
     * Return trues if a value is a bool, false otherwise.
     */
    public function isBool(mixed $value): bool;
    
    /**
     * Returns a single random number between [0.0-1.0).
     */
    public function rand(): float;
    
    /**
     * Returns a single random integer in the given inclusive range.
     *
     * @param int $min
     * @param int $max
     */
    public function randBetween(int $min, int $max): int;
}
