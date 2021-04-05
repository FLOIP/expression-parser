<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Excellent as ExcellentInterface;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\Node;

class Excellent extends AbstractMethodHandler implements ExcellentInterface
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
            return \array_reverse($split)[abs(++$number)];
        }
        // decrement the 1-indexed number
        return $split[--$number];
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

        if ($stop > 0) {
            $stop = count($split) - ($stop - 2);
        }

        if ($start < 0) {
            $split = \array_reverse($split);
            ++$start;
            $stop = count($split) - $stop + 1;
        } else {
            --$start;
        }


        return implode(' ', array_slice($split, $start, $stop));
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
        $result = preg_split("/\\s*[{$punc}]\\s*|\\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($result === false) {
            throw new MethodNodeException('Error splitting string by punctuation');
        }
        return $result;
    }

    private function stripPunc($string)
    {
        return \str_replace(str_split(static::PUNCTUATION), '', $string);
    }

    public function isNumber($value)
    {
        if ($value instanceof Node) {
            $value = $value->getValue();
        }
        return is_numeric($value);
    }

    public function isString($value)
    {
        if ($value instanceof Node) {
            $value = $value->getValue();
        }
        return is_string($value) && !(is_numeric($value) || $this->isBool($value));
    }

    public function isBool($value)
    {
        if ($value instanceof Node) {
            $value = $value->getValue();
        }
        else if (!is_string($value)) {
            return false;
        }
        return $value === 'TRUE' || $value === 'FALSE';
    }

    public function regexMatch($text, $pattern, $group = null) {
        $matches = [];

        // we'll use / as our delimiter, escaping any already present
        $pattern = \str_replace('/', '\/', $pattern);
        $pattern = "/$pattern/";

        $result = @\preg_match($pattern, "$text", $matches);
        if ($result === false) {
            if (preg_last_error() !== \PREG_NO_ERROR) {
                throw new MethodNodeException(\error_get_last()['message']);
            }
            throw new MethodNodeException("No match found for regex '$pattern' on '$text'");
        }
        if ($group > count($matches)) {
            throw new MethodNodeException("No match group index $group found for regex '$pattern' on '$text'");
        }
        if ($group > 0) {
            return $matches[$group];
        }
        return $matches[0];
    }
}
