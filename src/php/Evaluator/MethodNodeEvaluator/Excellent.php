<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Excellent as ExcellentInterface;
use Viamo\Floip\Evaluator\Node;
use function array_reverse;
use function lcg_value;
use function mt_rand;
use function str_replace;

class Excellent extends AbstractMethodHandler implements ExcellentInterface
{
    public function firstWord(string $string): string {
        return $this->word($string, 1);
    }

    public function first_word($string): string {
        return $this->firstWord($string);
    }

    public function percent($number): string {
        return $number * 100 . "%";
    }

    /**
     * @todo implement
     * @inheritDoc
     */
    public function readDigits(string $string): mixed {
        return $string;
    }

    public function read_digits($string) {
        return $this->readDigits($string);
    }

    public function removeFirstWord(string $string): string {
        $word = $this->firstWord($string);
        return substr($string, 1, strlen($word));
    }

    public function remove_first_word($string): string {
        return $this->removeFirstWord($string);
    }

    public function word(string $string, int $number, bool $bySpaces = null): string {
        if ($bySpaces) {
            $split = explode(' ', $string);
        } else {
            $split = $this->splitByPunc($string);
        }

        if ($number < 0) {
            return array_reverse($split)[abs(++$number)];
        }
        // decrement the 1-indexed number
        return $split[--$number];
    }

    public function wordCount(string $string, bool $bySpaces = null): int {
        if ($bySpaces) {
            return count(explode(' ', $string));
        }
        return count($this->splitByPunc($string));
    }

    public function word_count($string, $bySpaces = null): ?int {
        return $this->wordCount($string, $bySpaces);
    }

    public function wordSlice(string $string, int $start, int $stop = null, int $bySpaces = null): string {
        if ($bySpaces) {
            $split = explode(' ', $string);
        } else {
            $split = $this->splitByPunc($string);
        }

        if ($stop > 0) {
            $stop = count($split) - ($stop - 2);
        }

        if ($start < 0) {
            $split = array_reverse($split);
            ++$start;
            $stop = count($split) - $stop + 1;
        } else {
            --$start;
        }

        return implode(' ', array_slice($split, $start, $stop));
    }

    public function word_slice($string, $start, $stop = null, $bySpaces = null): string {
        return $this->wordSlice($string, $start, $stop, $bySpaces);
    }

    /**
     * Splits a string by punctuation or spaces
     *
     */
    private function splitByPunc(string $string): array {
        $punc = static::PUNCTUATION;
        $result = preg_split("/\\s*[{$punc}]\\s*|\\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($result === false) {
            throw new MethodNodeException('Error splitting string by punctuation');
        }
        return $result;
    }

    private function stripPunc($string): array|string {
        return str_replace(str_split(static::PUNCTUATION), '', (string) $string);
    }

    public function isNumber($value): bool {
        if ($value instanceof Node) {
            $value = $value->getValue();
        }
        return is_numeric($value);
    }

    public function is_number($value): bool {
        return $this->isNumber($value);
    }

    public function isString($value): bool {
        if ($value instanceof Node) {
            $value = $value->getValue();
        }
        return is_string($value) && !(is_numeric($value) || $this->isBool($value));
    }

    public function is_string($value): bool {
        return $this->isString($value);
    }

    public function isBool($value): bool {
        if ($value instanceof Node) {
            $value = $value->getValue();
        } else if (!is_string($value)) {
            return false;
        }
        return $value === 'TRUE' || $value === 'FALSE';
    }

    public function is_bool($value): bool {
        return $this->isBool($value);
    }

    public function rand(): float {
        return lcg_value();
    }

    public function randBetween(int $min, int $max): int {
        return mt_rand($min, $max);
    }

    public function rand_between($min, $max): int {
        return $this->randBetween($min, $max);
    }
}
