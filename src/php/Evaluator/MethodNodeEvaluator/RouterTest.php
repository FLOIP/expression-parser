<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use ArrayAccess;
use Carbon\Carbon;
use Exception;
use NajiDev\Permutation\PermutationIterator;
use Traversable;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\RouterTest as RouterTestInterface;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\TestResult;
use Viamo\Floip\Evaluator\Node;

class RouterTest implements RouterTestInterface
{
    /**
     * Splits a string by punctuation or spaces
     *
     * @param string $string
     * @return string[]
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

    public function has_all_words($text, $words) {
        // get a list of words
        $text = $this->splitByPunc($text);
        $words = $this->splitByPunc($words);

        // get all values in $words that are not present in $text
        $diff = array_udiff($words, $text, 'strcasecmp');

        // if all words are present, diff should be empty
        $result = count($diff) === 0;

        if ($result) {
            // get the matching words
            $match = array_uintersect($text, $words, 'strcasecmp');
            return new TestResult(true, implode(' ', $match));
        } else {
            return new TestResult;
        }
    }

    public function has_any_word($text, $words) {
        // get a list of words
        $text = $this->splitByPunc($text);
        $words = $this->splitByPunc($words);

        $intersection = array_uintersect($text, $words, 'strcasecmp');

        if (count($intersection) > 0) {
            return new TestResult(true, implode(' ', $intersection));
        } else {
            return new TestResult;
        }
    }

    public function has_beginning($text, $beginning) {
        $text = trim($text);
        $beginning = trim($beginning);

        $result = stripos($text, $beginning) === 0;

        if ($result) {
            return new TestResult(true, \substr($text, 0, \strlen($beginning)));
        } else {
            return new TestResult;
        }
    }

    // todo what exactly is the point of this...?
    public function has_category($result) {
        if ($result instanceof Node) {
            $result = $result->getValue();
        }
        if (!(is_array($result) || $result instanceof ArrayAccess)) {
            $type = \gettype($result);
            throw new MethodNodeException("Can only perform has_category on an array or ArrayAccess, got $type");
        }
        if (!isset($result['category'])) {
            throw new MethodNodeException("Can only perform has_category on a valid result structure, got: " . \json_encode($result));
        }
        $categories = array_slice(\func_get_args(), 1);
        foreach ($categories as $category) {
            if ($result['category'] === $category) {
                return new TestResult(true, $category);
            }
        }
        return new TestResult;
    }

    /**
     * Try very hard to parse a date from a sentence that may contain one.
     *
     * @param string $string
     * @return int|false
     */
    private function parseDateTimeFromString($string) {
        // remove non numeric or separator chars
        $string = trim(\preg_replace('%[^\d\-/\\\]%i', ' ', $string));
        // collapse whitespace
        $string = trim(\preg_replace('/\s+/', ' ', $string));
        // try splitting the string into parts
        $parts = preg_split('%[/\-\\\]%', $string);

        if ($parts === false) {
            return false;
        }

        if (count($parts) !== 3) {
            return false;
        }

        // try permutations of what we have
        foreach (new PermutationIterator(array_slice($parts, 0, 3)) as $permutation) {
            try {
                if ($result = \strtotime(implode('/', $permutation))) {
                    return $result;
                }
            } catch (Exception $e) {
                continue;
            }
        }
        return false;
    }

    public function has_date($text) {
        $result = $this->parseDateTimeFromString($text);
        if ($result === false) {
            return new TestResult;
        } else {
            return new TestResult(true, Carbon::createFromTimestamp($result));
        }
    }

    public function has_date_eq($text, $date) { }

    public function has_date_gt($text, $min) { }

    public function has_date_lt($text, $max) { }

    public function has_district($text, $state = null) { }

    public function has_email($text) { }

    public function has_error($value) { }

    public function has_group($contact, $group_uuid) { }

    public function has_intent($result, $name, $confidence) { }

    public function has_number($text) { }

    public function has_number_between($text, $min, $max) { }

    public function has_number_eq($text, $value) { }

    public function has_number_gt($text, $min) { }

    public function has_number_gte($text, $min) { }

    public function has_number_lt($text, $max) { }

    public function has_number_lte($text, $max) { }

    public function has_only_phrase($text, $phrase) { }

    public function has_only_text($text1, $text2) { }

    public function has_pattern($text, $pattern) { }

    public function has_phone($text, $country_code) { }

    public function has_phrase($text, $phrase) { }

    public function has_state($text) { }

    public function has_text($text) { }

    public function has_time($text) { }

    public function has_top_intent($result, $name, $confidence) { }

    public function has_ward($text, $district, $state) { }

    public function handles() { }
}
