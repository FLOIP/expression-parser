<?php

namespace Viamo\Floip\Tests\Evaluator\MethodHandler;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\MatchTest as MatchTestContract;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\MatchTest;
use function json_decode;

class MatchTestHandlerTest extends TestCase
{
    /** @var MatchTestContract */
    private MatchTestContract|MatchTest $handler;

    public function setUp(): void
    {
        $this->handler = new MatchTest;
        parent::setUp();
    }

    /**
     * @dataProvider hasAllWordsProvider
     */
    public function testHasAllWords(array $args, $expected) {
        $result = $this->handler
            ->has_all_words($args[0], $args[1])
            ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasAllWordsMatchProvider
     */
    public function testHasAllWordsMatch(array $args, $expected) {
        $result = $this->handler
            ->has_all_words($args[0], $args[1])
            ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasAllWordsProvider(): array {
        return [
            [["the quick brown FOX", "the fox"], "TRUE"],
            [["the quick brown FOX", "red fox"], "FALSE"]
        ];
    }

    public function hasAllWordsMatchProvider(): array {
        return [
            [["the quick brown FOX", "the fox"], "the FOX"],
            [["the quick brown FOX", "foo bar"], ""]
        ];
    }

    /**
     * @dataProvider hasAnyWordProvider
     */
    public function testHasAnyWord(array $args, $expected) {
        $result = $this->handler
        ->has_any_word($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasAnyWordMatchProvider
     */
    public function testHasAnyWordMatch(array $args, $expected) {
        $result = $this->handler
        ->has_any_word($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasAnyWordProvider(): array {
        return [
            [["The Quick Brown Fox", "fox quick"], "TRUE"],
            [["The Quick Brown Fox", "foo bar"], "FALSE"],
        ];
    }

    public function hasAnyWordMatchProvider(): array {
        return [
            [["The Quick Brown Fox", "fox quick"], "Quick Fox"],
            [["The Quick Brown Fox", "red fox"], "Fox"],
        ];
    }

    /**
     * @dataProvider hasBeginningProvider
     */
    public function testHasBeginning(array $args, $expected) {
        $result = $this->handler
        ->has_beginning($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasBeginningMatchProvider
     */
    public function testHasBeginningMatch(array $args, $expected) {
        $result = $this->handler
        ->has_beginning($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasBeginningProvider(): array {
        return [
            [["The Quick Brown Fox", "the quick"], "TRUE"],
            [["The Quick Brown Fox", "quick brown"], "FALSE"],
            [["The Quick Brown Fox", "the   quick"], "FALSE"],
        ];
    }

    public function hasBeginningMatchProvider(): array {
        return [
            [["The Quick Brown Fox", "the quick"], "The Quick"],
            [["The Quick Brown Fox", "red fox"], ""],
        ];
    }

    /**
     * @dataProvider hasCategoryProvider
     */
    public function testHasCategory(array $args, $expected) {
        $result = $this->handler
        ->has_category($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasCategoryMatchProvider
     */
    public function testHasCategoryMatch(array $args, $expected) {
        $result = $this->handler
        ->has_category($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasCategoryProvider(): array {
        return [
            [[$this->getResultsData()['webhook'], 'Success', 'Failure' ], "TRUE"],
            [[$this->getResultsData()['webhook'], 'Failure' ], "FALSE"],
        ];
    }

    public function hasCategoryMatchProvider(): array {
        return [
            [[$this->getResultsData()['webhook'], 'Success', 'Failure' ], 'Success'],
        ];
    }

    /**
     * @dataProvider hasDateProvider
     */
    public function testHasDate($text, $expected) {
        $result = $this->handler
        ->has_date($text)
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasDateMatchProvider
     */
    public function testHasDateMatch($text, $expected) {
        $result = $this->handler
        ->has_date($text)
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasDateProvider(): array {
        return [
            ["the date is 15/01/2017", "TRUE"],
            ["there is no date here, just a year 2017", "FALSE"],
        ];
    }

    public function hasDateMatchProvider(): array {
        return [
            ["the date is 15/01/2017", "2017-01-15 00:00:00"],
        ];
    }

    /**
     * @dataProvider hasDateEqProvider
     */
    public function testHasDateEq($args, $expected) {
        $result = $this->handler
        ->has_date_eq($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasDateEqMatchProvider
     */
    public function testHasDateEqMatch($args, $expected) {
        $result = $this->handler
        ->has_date_eq($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasDateEqProvider(): array {
        return [
            [["the date is 15/01/2017", "2017-01-15"], "TRUE"],
            [["there is no date here, just a year 2017", "2017-06-01"], "FALSE"],
        ];
    }

    public function hasDateEqMatchProvider(): array {
        return [
            [["the date is 15/01/2017", "2017-01-15"], "2017-01-15 00:00:00"],
            [["the date is 15/01/2017 15:00", "2017-01-15"], "2017-01-15 15:00:00"]
        ];
    }

    /**
     * @dataProvider hasDateGtProvider
     */
    public function testHasDateGt($args, $expected) {
        $result = $this->handler
        ->has_date_gt($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasDateGtMatchProvider
     */
    public function testHasDateGtMatch($args, $expected) {
        $result = $this->handler
        ->has_date_gt($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasDateGtProvider(): array {
        return [
            [["the date is 15/01/2017", "2017-01-01"], "TRUE"],
            [["the date is 15/01/2017", "2017-03-15"], "FALSE"],
            [["there is no date here, just a year 2017", "2017-06-01"], "FALSE"]
        ];
    }

    public function hasDateGtMatchProvider(): array {
        return [
            [["the date is 15/01/2017", "2017-01-01"], "2017-01-15 00:00:00"],
            [["the date is 15/01/2017 15:00", "2017-01-15"], "2017-01-15 15:00:00"]
        ];
    }

    /**
     * @dataProvider hasDistrictProvider
     */
    public function testHasDistrict($args, $expected) {
        $this->markTestSkipped();
        $result = $this->handler
        ->has_district($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasDistrictMatchProvider
     */
    public function testHasDistrictMatch($args, $expected) {
        $this->markTestSkipped();
        $result = $this->handler
        ->has_district($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasDistrictProvider(): array {
        return [
            [["Gasabo", "Kigali"], "TRUE"],
            [["I live in Gasabo", "Kigali"], "TRUE"],
            [["Gasabo", "Boston"], "FALSE"],
        ];
    }

    public function hasDistrictMatchProvider(): array {
        return [
            [["Gasabo", "Kigali"], "Rwanda > Kigali City > Gasabo"],
            [["I live in Gasabo"], "Kigali", "Rwanda > Kigali City > Gasabo"],
            [["Gasabo", null], "Rwanda > Kigali City > Gasabo",]
        ];
    }
    
    /**
     * @dataProvider hasEmailProvider
     */
    public function testHasEmail($args, $expected) {
        $result = $this->handler
        ->has_email($args[0])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasEmailMatchProvider
     */
    public function testHasEmailMatch($args, $expected) {
        $result = $this->handler
        ->has_email($args[0])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasEmailProvider(): array {
        return [
            [["my email is foo1@bar.com, please respond"], "TRUE"],
            [["my email is <foo@bar2.com>"], "TRUE"],
            [["i'm not sharing my email"], "FALSE"]
        ];
    }

    public function hasEmailMatchProvider(): array {
        return [
            [["my email is foo1@bar.com, please respond"], "foo1@bar.com"],
            [["my email is <foo@bar2.com>"], "foo@bar2.com"]
        ];
    }

    /**
     * @dataProvider hasGroupProvider
     */
    public function testHasGroup($args, $expected) {
        $result = $this->handler
        ->has_group($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasGroupMatchProvider
     */
    public function testHasGroupMatch($args, $expected) {
        $result = $this->handler
        ->has_group($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasGroupProvider(): array {
        return [
            [[$this->getContactData()['groups'], "b7cf0d83-f1c9-411c-96fd-c511a4cfa86d"], "TRUE"],
            [[$this->getContactData()['groups'], "97fe7029-3a15-4005-b0c7-277b884fc1d5"], "FALSE"],
        ];
    }

    public function hasGroupMatchProvider(): array {
        return [
            [[$this->getContactData()['groups'], "b7cf0d83-f1c9-411c-96fd-c511a4cfa86d"], '{"name":"Testers","uuid":"b7cf0d83-f1c9-411c-96fd-c511a4cfa86d"}'],
        ];
    }

    /**
     * @dataProvider hasIntentProvider
     */
    public function testHasIntent($args, $expected) {
        $result = $this->handler
        ->has_intent($args[0], $args[1], $args[2])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasIntentMatchProvider
     */
    public function testHasIntentMatch($args, $expected) {
        $result = $this->handler
        ->has_intent($args[0], $args[1], $args[2])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasIntentProvider(): array {
        return [
            [[$this->getResultsData()['intent'], "book_flight", 0.5], "TRUE"],
            [[$this->getResultsData()['intent'], "book_hotel", 0.2], "TRUE"],
            [[$this->getResultsData()['intent'], "book_car", 0.9], "FALSE"],
        ];
    }

    public function hasIntentMatchProvider(): array {
        return [
        ];
    }

    /**
     * @dataProvider hasNumberProvider
     */
    public function testHasNumber($args, $expected) {
        $result = $this->handler
        ->has_number($args[0])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberMatchProvider
     */
    public function testHasNumberMatch($args, $expected) {
        $result = $this->handler
        ->has_number($args[0])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberProvider(): array {
        return [
            [["the number is 42"], "TRUE"],
            [["العدد ٤٢"], "TRUE"],
            [["the number is forty two"], "FALSE"]
        ];
    }

    public function hasNumberMatchProvider(): array {
        return [
            [["the number is 42"], 42],
            [["العدد ٤٢"], "٤٢"]
        ];
    }
    
    /**
     * @dataProvider hasNumberBetweenProvider
     */
    public function testHasNumberBetween($args, $expected) {
        $result = $this->handler
        ->has_number_between($args[0], $args[1], $args[2])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberBetweenMatchProvider
     */
    public function testHasNumberBetweenMatch($args, $expected) {
        $result = $this->handler
        ->has_number_between($args[0], $args[1], $args[2])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberBetweenProvider(): array {
        return [
            [["the number is 42", 40, 44], "TRUE"],
            [["the number is 42", 50, 60], "FALSE"],
            [["the number is not there", 50, 60], "FALSE"],
        ];
    }

    public function hasNumberBetweenMatchProvider(): array {
        return [
            [["the number is 42", 40, 44], 42],
        ];
    }

    /**
     * @dataProvider hasNumberEqProvider
     */
    public function testHasNumberEq($args, $expected) {
        $result = $this->handler
        ->has_number_eq($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberEqMatchProvider
     */
    public function testHasNumberEqMatch($args, $expected) {
        $result = $this->handler
        ->has_number_eq($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberEqProvider(): array {
        return [
            [["the number is 42", 42], "TRUE"],
            [["the number is not 42", 40], "FALSE"],
            [["the number is not there", 40], "FALSE"],
        ];
    }

    public function hasNumberEqMatchProvider(): array {
        return [
            [["the number is 42", 42], 42],
        ];
    }

    /**
     * @dataProvider hasNumberGtProvider
     */
    public function testHasNumberGt($args, $expected) {
        $result = $this->handler
        ->has_number_gt($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberGtMatchProvider
     */
    public function testHasNumberGtMatch($args, $expected) {
        $result = $this->handler
        ->has_number_gt($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberGtProvider(): array {
        return [
            [["the number is 42", 40], "TRUE"],
            [["the number is 42", 42], "FALSE"],
            [["the number is not there", 40], "FALSE"],
        ];
    }

    public function hasNumberGtMatchProvider(): array {
        return [
            [["the number is 42", 40], 42],
        ];
    }

    /**
     * @dataProvider hasNumberGteProvider
     */
    public function testHasNumberGte($args, $expected) {
        $result = $this->handler
        ->has_number_gte($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberGteMatchProvider
     */
    public function testHasNumberGteMatch($args, $expected) {
        $result = $this->handler
        ->has_number_gte($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberGteProvider(): array {
        return [
            [["the number is 42", 42], "TRUE"],
            [["the number is 42", 45], "FALSE"],
            [["the number is not there", 40], "FALSE"],
        ];
    }

    public function hasNumberGteMatchProvider(): array {
        return [
            [["the number is 42", 42], 42],
        ];
    }

    /**
     * @dataProvider hasNumberLtProvider
     */
    public function testHasNumberLt($args, $expected) {
        $result = $this->handler
        ->has_number_lt($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberLtMatchProvider
     */
    public function testHasNumberLtMatch($args, $expected) {
        $result = $this->handler
        ->has_number_lt($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberLtProvider(): array {
        return [
            [["the number is 42", 44], "TRUE"],
            [["the number is 42", 40], "FALSE"],
            [["the number is not there", 40], "FALSE"],
        ];
    }

    public function hasNumberLtMatchProvider(): array {
        return [
            [["the number is 42", 44], 42],
        ];
    }

    /**
     * @dataProvider hasNumberLteProvider
     */
    public function testHasNumberLte($args, $expected) {
        $result = $this->handler
        ->has_number_lte($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasNumberLteMatchProvider
     */
    public function testHasNumberLteMatch($args, $expected) {
        $result = $this->handler
        ->has_number_lte($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasNumberLteProvider(): array {
        return [
            [["the number is 42", 42], "TRUE"],
            [["the number is 42", 40], "FALSE"],
            [["the number is not there", 40], "FALSE"],
        ];
    }

    public function hasNumberLteMatchProvider(): array {
        return [
            [["the number is 42", 42], 42],
        ];
    }

    /**
     * @dataProvider hasOnlyPhraseProvider
     */
    public function testHasOnlyPhrase($args, $expected) {
        $result = $this->handler
        ->has_only_phrase($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasOnlyPhraseMatchProvider
     */
    public function testHasOnlyPhraseMatch($args, $expected) {
        $result = $this->handler
        ->has_only_phrase($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasOnlyPhraseProvider(): array {
        return [
            [["Quick Brown", "quick brown"], "TRUE"],
            [["The Quick Brown Fox", "quick brown"], "FALSE"],
            [["the Quick Brown fox", ""], "FALSE"],
            [["", "",], "TRUE"],
            [["The Quick Brown Fox", "red fox"], "FALSE"],
        ];
    }

    public function hasOnlyPhraseMatchProvider(): array {
        return [
            [["Quick Brown", "quick brown"], "quick brown"],
            [["", ""], ""]
        ];
    }

    /**
     * @dataProvider hasOnlyTextProvider
     */
    public function testHasOnlyText($args, $expected) {
        $result = $this->handler
        ->has_only_text($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasOnlyTextMatchProvider
     */
    public function testHasOnlyTextMatch($args, $expected) {
        $result = $this->handler
        ->has_only_text($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasOnlyTextProvider(): array {
        return [
            [["foo", "foo"], "TRUE"],
            [["foo", "FOO"], "FALSE"],
            [["foo", "bar"], "FALSE"],
            [["foo", " foo "], "FALSE"],
            [["The Quick Brown Fox", "red fox"], "FALSE"],
        ];
    }

    public function hasOnlyTextMatchProvider(): array {
        return [
            [["foo", "foo"], "foo"],
            [["FOO", "FOO"], "FOO"],
        ];
    }

    /**
     * @dataProvider hasPatternProvider
     */
    public function testHasPattern($args, $expected) {
        $result = $this->handler
        ->has_pattern($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasPatternMatchProvider
     */
    public function testHasPatternMatch($args, $expected) {
        $result = $this->handler
        ->has_pattern($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasPatternProvider(): array {
        return [
            [["Buy cheese please", "buy (\w+)"], "TRUE"],
            [["Sell cheese please", "buy (\w+)"], "FALSE"],
        ];
    }

    public function hasPatternMatchProvider(): array {
        return [
            [["Buy cheese please", "buy (\w+)"], "Buy cheese"],
        ];
    }

    /**
     * @dataProvider hasPhoneProvider
     */
    public function testHasPhone($args, $expected) {
        $result = $this->handler
        ->has_phone($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasPhoneMatchProvider
     */
    public function testHasPhoneMatch($args, $expected) {
        $result = $this->handler
        ->has_phone($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasPhoneProvider(): array {
        return [
            [["my number is +12067799294 thanks", null], "TRUE"],
            [["my number is 2067799294", "US"], "TRUE"],
            [["my number is none of your business", "US"], "FALSE"],
        ];
    }

    public function hasPhoneMatchProvider(): array {
        return [
            [["my number is +12067799294 thanks", null], "+12067799294"],
            [["my number is 2067799294", "US"], "+12067799294"],
            [["my number is 206 779 9294", "US"], "+12067799294"]
        ];
    }

    /**
     * @dataProvider hasPhraseProvider
     */
    public function testHasPhrase($args, $expected) {
        $result = $this->handler
        ->has_phrase($args[0], $args[1])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasPhraseMatchProvider
     */
    public function testHasPhraseMatch($args, $expected) {
        $result = $this->handler
        ->has_phrase($args[0], $args[1])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasPhraseProvider(): array {
        return [
            [["the quick brown fox", "brown fox"], "TRUE"],
            [["the Quick Brown fox", "quick fox"], "FALSE"],
        ];
    }

    public function hasPhraseMatchProvider(): array {
        return [
            [["the quick brown fox", "brown fox"], "brown fox"],
            [["the Quick Brown fox", ""], ""],
        ];
    }

    /**
     * @dataProvider hasTextProvider
     */
    public function testHasText($args, $expected) {
        $result = $this->handler
        ->has_text($args[0])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasTextMatchProvider
     */
    public function testHasTextMatch($args, $expected) {
        $result = $this->handler
        ->has_text($args[0])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasTextProvider(): array {
        return [
            [["quick brown"], "TRUE"],
            [[""], "FALSE"],
            [[" \n"], "FALSE"],
            [[123], "TRUE"]
        ];
    }

    public function hasTextMatchProvider(): array {
        return [
            [["quick brown"], "quick brown"],
            [[123], "123"]
        ];
    }

    /**
     * @dataProvider hasTimeProvider
     */
    public function testHasTime($args, $expected) {
        $result = $this->handler
        ->has_time($args[0])
        ->getValue();

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider hasTimeMatchProvider
     */
    public function testHasTimeMatch($args, $expected) {
        $result = $this->handler
        ->has_time($args[0])
        ->getMatch();

        $this->assertEquals($expected, $result);
    }

    public function hasTimeProvider(): array {
        return [
            [["the time is 10:30"], "TRUE"],
            [["the time is 10 PM"], "TRUE"],
            [["the time is 10:30:45"], "TRUE"],
            [["there is no time here, just the number 25"], "FALSE"]
        ];
    }

    public function hasTimeMatchProvider(): array {
        return [
            [["the time is 10:30"], "10:30:00"],
            [["the time is 10 PM"], "22:00:00"],
            [["the time is 10:30:45"], "10:30:45"],
        ];
    }

    private function getContactData() {
        return json_decode(
<<<JSON
{
    "groups": [
        {
            "name": "Testers",
            "uuid": "b7cf0d83-f1c9-411c-96fd-c511a4cfa86d"
        }
    ]
}
JSON
,
            "TRUE"
        );
    }

    /**
     * Return a "results" structure, as per https://app.rapidpro.io/mr/docs/flows.html#action:call_resthook
     *
     * @return array
     */
    private function getResultsData(): array {
        return json_decode(
<<<JSON
{
    "2factor": {
        "category": "",
        "category_localized": "",
        "created_on": "2018-04-11T18:24:30.123456Z",
        "input": "",
        "name": "2Factor",
        "node_uuid": "f5bb9b7a-7b5e-45c3-8f0e-61b4e95edf03",
        "value": "34634624463525"
    },
    "favorite_color": {
        "category": "Red",
        "category_localized": "Red",
        "created_on": "2018-04-11T18:24:30.123456Z",
        "input": "",
        "name": "Favorite Color",
        "node_uuid": "f5bb9b7a-7b5e-45c3-8f0e-61b4e95edf03",
        "value": "red"
    },
    "intent": {
        "category": "Success",
        "category_localized": "Success",
        "created_on": "2018-04-11T18:24:30.123456Z",
        "input": "Hi there",
        "name": "Intent",
        "node_uuid": "f5bb9b7a-7b5e-45c3-8f0e-61b4e95edf03",
        "value": "book_flight",
        "extra": {
            "intents": [
                {
                    "name": "book_flight",
                    "confidence": 0.5
                },
                {
                    "name": "book_hotel",
                    "confidence": 0.25
                }
            ],
            "entities": {
                "location": [
                    {
                        "value": "Quito",
                        "confidence": 1
                    }
                ]
            }
        }
    },
    "phone_number": {
        "category": "",
        "category_localized": "",
        "created_on": "2018-04-11T18:24:30.123456Z",
        "input": "",
        "name": "Phone Number",
        "node_uuid": "f5bb9b7a-7b5e-45c3-8f0e-61b4e95edf03",
        "value": "+12344563452"
    },
    "webhook": {
        "category": "Success",
        "category_localized": "Success",
        "created_on": "2018-04-11T18:24:30.123456Z",
        "input": "GET http://127.0.0.1:49998/?content=%7B%22results%22%3A%5B%7B%22state%22%3A%22WA%22%7D%2C%7B%22state%22%3A%22IN%22%7D%5D%7D",
        "name": "webhook",
        "node_uuid": "f5bb9b7a-7b5e-45c3-8f0e-61b4e95edf03",
        "value": "200"
    }
}
JSON
,
            "TRUE"
        );
    }
}
