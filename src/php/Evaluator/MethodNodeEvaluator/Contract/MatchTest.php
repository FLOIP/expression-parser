<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\EvaluatesMethods;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\TestResult;

interface MatchTest extends EvaluatesMethods
{
    const PUNCTUATION=',:;!?.-';

    /**
     * Tests whether all the words are contained in text.
     * The words can be in any order and may appear more than once.
     *
     * @param string $text
     * @param string $words
     * @return TestResult
     */
    public function has_all_words($text, $words);

    /**
     * Tests whether any of the words are contained in the text
     * Only one of the words needs to match and it may appear more than once.
     *
     * @param string $text
     * @param string $words
     * @return TestResult
     */
    public function has_any_word($text, $words);

    /**
     * Tests whether text starts with beginning.
     * Both text values are trimmed of surrounding whitespace,
     * but otherwise matching is strict without any tokenization.
     *
     * @param string $text
     * @param string $beginning
     * @return TestResult
     */
    public function has_beginning($text, $beginning);

    /**
     * Tests whether the category of a result on of the passed in categories
     *
     * @param array|Traversable|Node $result
     * @param string ...$categories
     * @return TestResult
     */
    public function has_category($result, $categories);

    /**
     * Tests whether text contains a date formatted according to our environment.
     *
     * @param string $text
     * @return TestResult
     */
    public function has_date($text);

    /**
     * Tests whether text a date equal to date.
     *
     * @param string $text
     * @param string $date
     * @return TestResult
     */
    public function has_date_eq($text, $date);

    /**
     * Tests whether text a date after the date min.
     *
     * @param string $text
     * @param string $min
     * @return TestResult
     */
    public function has_date_gt($text, $min);

    /**
     * Tests whether text contains a date before the date max.
     *
     * @param string $text
     * @param string $max
     * @return TestResult
     */
    public function has_date_lt($text, $max);

    /**
     * Tests whether a district name is contained in the text.
     * If state is also provided then the returned district must be within that
     * state.
     *
     * @param string $text
     * @param string $state
     * @return TestResult
     */
    public function has_district($text, $state = null);

    /**
     * Tests whether an email is contained in text.
     *
     * @param string $text
     * @return TestResult
     */
    public function has_email($text);

    /**
     * Returns whether value is an error.
     *
     * @param mixed $value
     * @return TestResult
     */
    public function has_error($value);

    /**
     * Returns whether the contact is part of group with the passed in UUID.
     *
     * @param array $groups
     * @param string $group_uuid
     * @return TestResult
     */
    public function has_group($groups, $group_uuid);

    /**
     * Tests whether any intent in a classification result has name and minimum
     * confidence.
     *
     * @param string $result
     * @param string $name
     * @param float $confidence
     * @return TestResult
     */
    public function has_intent($result, $name, $confidence);

    /**
     * Tests whether text contains a number.
     *
     * @param string $text
     * @return TestResult
     */
    public function has_number($text);

    /**
     * Tests whether text contains a number between min and max inclusive.
     *
     * @param string $text
     * @param int $min
     * @param int $max
     * @return TestResult
     */
    public function has_number_between($text, $min, $max);

    /**
     * Tests whether text contains a number equal to the value.
     *
     * @param string $text
     * @param int $value
     * @return TestResult
     */
    public function has_number_eq($text, $value);

    /**
     * Tests whether text contains a number greater than min.
     *
     * @param string $text
     * @param int $min
     * @return TestResult
     */
    public function has_number_gt($text, $min);

    /**
     * Tests whether text contains a number greater than or equal to min.
     *
     * @param string $text
     * @param int $min
     * @return TestResult
     */
    public function has_number_gte($text, $min);

    /**
     * Tests whether text contains a number less than max.
     *
     * @param string $text
     * @param int $max
     * @return TestResult
     */
    public function has_number_lt($text, $max);

    /**
     * Tests whether text contains a number less than or equal to max.
     *
     * @param string $text
     * @param int $max
     * @return TestResult
     */
    public function has_number_lte($text, $max);

    /**
     * Tests whether the text contains only phrase.
     * The phrase must be the only text in the text to match
     *
     * @param string $text
     * @param string $phrase
     * @return TestResult
     */
    public function has_only_phrase($text, $phrase);

    /**
     * Returns whether two text values are equal (case sensitive).
     * In the case that they are, it will return the text as the match.
     *
     * @param string $text1
     * @param string $text2
     * @return TestResult
     */
    public function has_only_text($text1, $text2);

    /**
     * Tests whether text matches the regex pattern.
     * Both text values are trimmed of surrounding whitespace and matching is
     * case-insensitive.
     *
     * @param string $text
     * @param string $pattern
     * @return TestResult
     */
    public function has_pattern($text, $pattern);

    /**
     * Tests whether text contains a phone number. The optional country_code
     * argument specifies the country to use for parsing.
     *
     * @param string $text
     * @param string|null $country_code
     * @return TestResult
     */
    public function has_phone($text, $country_code = null);

    /**
     * Tests whether phrase is contained in text.
     * The words in the test phrase must appear in the same order with no other
     * words in between.
     *
     * @param string $text
     * @param string $phrase
     * @return TestResult
     */
    public function has_phrase($text, $phrase);

    /**
     * Tests whether a state name is contained in the text.
     *
     * @param string $text
     * @return TestResult
     */
    public function has_state($text);

    /**
     * Tests whether there the text has any characters in it.
     *
     * @param string $text
     * @return TestResult
     */
    public function has_text($text);

    /**
     * Tests whether text contains a time.
     *
     * @param string $text
     * @return TestResult
     */
    public function has_time($text);

    /**
     * Tests whether the top intent in a classification result has name and
     * minimum confidence.
     *
     * @param string $result
     * @param string $name
     * @param int $confidence
     * @return TestResult
     */
    public function has_top_intent($result, $name, $confidence);

    /**
     * Tests whether a ward name is contained in the text.
     *
     * @param string $text
     * @param string $district
     * @param string $state
     * @return TestResult
     */
    public function has_ward($text, $district, $state);

}
