<?php

namespace Viamo\Floip\Tests\Evaluator\MethodHandler;

use PHPUnit\Framework\TestCase;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\RouterTest as RouterTestContract;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\RouterTest;

class RouterTestHandlerTest extends TestCase
{
    /** @var RouterTestContract */
    private $handler;

    public function setUp()
    {
        $this->handler = new RouterTest;
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

    public function hasAllWordsProvider() {
        return [
            [["the quick brown FOX", "the fox"], true],
            [["the quick brown FOX", "red fox"], false]
        ];
    }

    public function hasAllWordsMatchProvider() {
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

    public function hasAnyWordProvider() {
        return [
            [["The Quick Brown Fox", "fox quick"], true],
            [["The Quick Brown Fox", "foo bar"], false],
        ];
    }

    public function hasAnyWordMatchProvider() {
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

    public function hasBeginningProvider() {
        return [
            [["The Quick Brown Fox", "the quick"], true],
            [["The Quick Brown Fox", "quick brown"], false],
            [["The Quick Brown Fox", "the   quick"], false],
        ];
    }

    public function hasBeginningMatchProvider() {
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

    public function hasCategoryProvider() {
        return [
            [[$this->getResultsData()['webhook'], 'Success', 'Failure' ], true],
            [[$this->getResultsData()['webhook'], 'Failure' ], false],
        ];
    }

    public function hasCategoryMatchProvider() {
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

    public function hasDateProvider() {
        return [
            ["the date is 15/01/2017", true],
            ["there is no date here, just a year 2017", false],
        ];
    }

    public function hasDateMatchProvider() {
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

    public function hasDateEqProvider() {
        return [
            [["the date is 15/01/2017", "2017-01-15"], true],
            [["there is no date here, just a year 2017", "2017-06-01"], false],
        ];
    }

    public function hasDateEqMatchProvider() {
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

    public function hasDateGtProvider() {
        return [
            [["the date is 15/01/2017", "2017-01-01"], true],
            [["the date is 15/01/2017", "2017-03-15"], false],
            [["there is no date here, just a year 2017", "2017-06-01"], false]
        ];
    }

    public function hasDateGtMatchProvider() {
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

    public function hasDistrictProvider() {
        return [
            [["Gasabo", "Kigali"], true],
            [["I live in Gasabo", "Kigali"], true],
            [["Gasabo", "Boston"], false],
        ];
    }

    public function hasDistrictMatchProvider() {
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

    public function hasEmailProvider() {
        return [
            [["my email is foo1@bar.com, please respond"], true],
            [["my email is <foo@bar2.com>"], true],
            [["i'm not sharing my email"], false]
        ];
    }

    public function hasEmailMatchProvider() {
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

    public function hasGroupProvider() {
        return [
            [[$this->getContactData()['groups'], "b7cf0d83-f1c9-411c-96fd-c511a4cfa86d"], true],
            [[$this->getContactData()['groups'], "97fe7029-3a15-4005-b0c7-277b884fc1d5"], false],
        ];
    }

    public function hasGroupMatchProvider() {
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

    public function hasIntentProvider() {
        return [
            [[$this->getResultsData()['intent'], "book_flight", 0.5], true],
            [[$this->getResultsData()['intent'], "book_hotel", 0.2], true],
            [[$this->getResultsData()['intent'], "book_car", 0.9], false],
        ];
    }

    public function hasIntentMatchProvider() {
        return [
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
JSON,
            true
        );
    }

    /**
     * Return a "results" structure, as per https://app.rapidpro.io/mr/docs/flows.html#action:call_resthook
     *
     * @return array
     */
    private function getResultsData() {
        return \json_decode(
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
JSON,
            true
        );
    }
}
