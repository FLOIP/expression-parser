<?php

namespace Floip\Evaluator;

use Floip\Evaluator\Contract\Excellent as ExcellentInterface;

class Excellent implements ExcellentInterface
{
    public function firstWord($string)
    {
        return $this->word($string, 1);
    }

    public function percent($number)
    {
        return $number * 100 . "%";
    }

    /**
     * @todo implement
     * @inheritDoc
     */
    public function readDigits($string)
    {
        return $string;
    }

    public function removeFistWord($string)
    {
        $word = $this->firstWord($string);
        return substr($string, 1, strlen($word));
    }

    public function word($string, $number, $bySpaces = null)
    {
        if ($bySpaces) {
            $split = explode(' ', $string);
        } else {
            $split = $this->splitByPunc($string);
        }

        if ($number < 0) {
            return $this->stripPunc(\array_reverse($split)[abs(++$number)]);
        }
        // decrement the 1-indexed number
        return $this->stripPunc($split[--$number]);
    }

    public function wordCount($string, $bySpaces = null)
    {
        if ($bySpaces) {
            return count(explode(' ', $string));
        }
        return count($this->splitByPunc($string));
    }

    public function wordSlice($string, $start, $stop = null, $bySpaces = null)
    {
        if ($bySpaces) {
            $split = explode(' ', $string);
        } else {
            $split = $this->splitByPunc($string);
        }

        return array_slice($split, $start, $stop);
    }

    /**
     * Splits a string by punctuation or spaces
     *
     * @param string $string
     * @return array
     */
    private function splitByPunc($string)
    {
        $punc = static::PUNCTUATION;
        return preg_split("/\\s*[{$punc}]\\s*|\\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    private function stripPunc($string)
    {
        return \str_replace(str_split(static::PUNCTUATION), '', $string);
    }
}
