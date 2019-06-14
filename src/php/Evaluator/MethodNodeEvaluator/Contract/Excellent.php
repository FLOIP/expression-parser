<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface Excellent extends EvaluatesMethods
{
    const PUNCTUATION=',:;!?.-';
    /**
     * Returns the first word in the given text - equivalent to WORD(text, 1).
     * Note: n is 1 indexed.
     *
     * @param string $string
     * @return string
     */
    public function firstWord($string);

    /**
     * Formats a number as a percentage
     *
     * @param int|float $number
     * @return string
     */
    public function percent($number);

    /**
     * Formats digits in text for reading in TTS
     *
     * @param string $string
     * @return mixed
     */
    public function readDigits($string);

    /**
     * Removes the first word from the given text. The remaining text will
     * be unchanged
     *
     * @param string $string
     * @return string
     */
    public function removeFistWord($string);

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
     * @return string
     */
    public function word($string, $number, $bySpaces = null);

    /**
     * Returns the number of words in the given text string. If by_spaces is
     * specified and is TRUE then the functionsplits the text into words only
     * by spaces. Otherwise the text is split by punctuation characters as well
     *
     * @param string $string
     * @param bool|null $bySpaces
     * @return int
     */
    public function wordCount($string, $bySpaces = null);

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
     * @return string
     */
    public function wordSlice($string, $start, $stop = null, $bySpaces = null);
}
