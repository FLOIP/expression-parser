<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;



use Traversable;
use Viamo\Floip\Evaluator\Node;

interface MatchTest extends EvaluatesMethods
{
    const PUNCTUATION=',:;!?.-';

    /**
     * Tests whether all the words are contained in text.
     * The words can be in any order and may appear more than once.
     */
    public function has_all_words(string $text, string $words): TestResult;

    /**
     * Tests whether any of the words are contained in the text
     * Only one of the words needs to match and it may appear more than once.
     */
    public function has_any_word(string $text, string $words): TestResult;

    /**
     * Tests whether text starts with beginning.
     * Both text values are trimmed of surrounding whitespace,
     * but otherwise matching is strict without any tokenization.
     */
    public function has_beginning(string $text, string $beginning): TestResult;

    /**
     * Tests whether the category of a result on of the passed in categories
     */
    public function has_category(Node|iterable $result, array $categories): TestResult;

    /**
     * Tests whether text contains a date formatted according to our environment.
     */
    public function has_date(string $text): TestResult;

    /**
     * Tests whether text a date equal to date.
     */
    public function has_date_eq(string $text, string $date): TestResult;

    /**
     * Tests whether text a date after the date min.
     */
    public function has_date_gt(string $text, string $min): TestResult;

    /**
     * Tests whether text contains a date before the date max.
     */
    public function has_date_lt(string $text, string $max): TestResult;

    /**
     * Tests whether a district name is contained in the text.
     * If state is also provided then the returned district must be within that
     * state.
     */
    public function has_district(string $text, ?string $state = null): TestResult;

    /**
     * Tests whether an email is contained in text.
     */
    public function has_email(string $text): TestResult;

    /**
     * Returns whether value is an error.
     */
    public function has_error(mixed $value): TestResult;

    /**
     * Returns whether the contact is part of group with the passed in UUID.
     */
    public function has_group(Node|array $groups, string $group_uuid): TestResult;

    /**
     * Tests whether any intent in a classification result has name and minimum
     * confidence.
     */
    public function has_intent(Node|array|string $result, string $name, float $confidence): TestResult;

    /**
     * Tests whether text contains a number.
     */
    public function has_number(string $text): TestResult;

    /**
     * Tests whether text contains a number between min and max inclusive.
     */
    public function has_number_between(string $text, int $min, int $max): TestResult;

    /**
     * Tests whether text contains a number equal to the value.
     */
    public function has_number_eq(string $text, int $value): TestResult;

    /**
     * Tests whether text contains a number greater than min.
     */
    public function has_number_gt(string $text, int $min): TestResult;

    /**
     * Tests whether text contains a number greater than or equal to min.
     */
    public function has_number_gte(string $text, int $min): TestResult;

    /**
     * Tests whether text contains a number less than max.
     */
    public function has_number_lt(string $text, int $max): TestResult;

    /**
     * Tests whether text contains a number less than or equal to max.
     */
    public function has_number_lte(string $text, int $max): TestResult;

    /**
     * Tests whether the text contains only phrase.
     * The phrase must be the only text in the text to match
     */
    public function has_only_phrase(string $text, string $phrase): TestResult;

    /**
     * Returns whether two text values are equal (case sensitive).
     * In the case that they are, it will return the text as the match.
     */
    public function has_only_text(string $text1, string $text2): TestResult;

    /**
     * Tests whether text matches the regex pattern.
     * Both text values are trimmed of surrounding whitespace and matching is
     * case-insensitive.
     */
    public function has_pattern(string $text, string $pattern): TestResult;

    /**
     * Tests whether text contains a phone number. The optional country_code
     * argument specifies the country to use for parsing.
     */
    public function has_phone(string $text, ?string $country_code = null): TestResult;

    /**
     * Tests whether phrase is contained in text.
     * The words in the test phrase must appear in the same order with no other
     * words in between.
     */
    public function has_phrase(string $text, string $phrase): TestResult;

    /**
     * Tests whether a state name is contained in the text.
     */
    public function has_state(string $text): TestResult;

    /**
     * Tests whether there the text has any characters in it.
     */
    public function has_text(string $text): TestResult;

    /**
     * Tests whether text contains a time.
     */
    public function has_time(string $text): TestResult;

    /**
     * Tests whether the top intent in a classification result has name and
     * minimum confidence.
     */
    public function has_top_intent(string $result, string $name, int $confidence): TestResult;

    /**
     * Tests whether a ward name is contained in the text.
     */
    public function has_ward(string $text, string $district, string $state): TestResult;

}
