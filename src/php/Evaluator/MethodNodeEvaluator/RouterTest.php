<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\RouterTest as RouterTestInterface;

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
        $diff = array_diff($this->splitByPunc($words), $this->splitByPunc($text));
        
        return count($diff) == 0;
    }

    public function has_any_word($text, $words) { }

    public function has_beginning($text, $beginning) { }

    public function has_category($result, $categories) { }

    public function has_date($text) { }

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
