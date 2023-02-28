<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use ArrayAccess;
use Carbon\Carbon;
use Exception;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use NajiDev\Permutation\PermutationIterator;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\MatchTest as MatchTestInterface;
use Viamo\Floip\Evaluator\Node;
use function addcslashes;
use function func_get_args;
use function gettype;
use function is_numeric;
use function json_encode;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function strcasecmp;
use function strlen;
use function strtotime;
use function substr;

// todo: do we need utf aware string comparison?
class MatchTest implements MatchTestInterface
{
    /**
     * Splits a string by punctuation or spaces
     *
     * @return string[]
     */
    private function splitByPunc(string $string): array {
        $punc = static::PUNCTUATION;
        $result = preg_split("/\\s*[{$punc}]\\s*|\\s/u", $string, -1, PREG_SPLIT_NO_EMPTY);
        if ($result === false) {
            throw new MethodNodeException('Error splitting string by punctuation');
        }
        return $result;
    }

    public function has_all_words(string $text, string $words): TestResult {
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

    public function has_any_word(string $text, string $words): TestResult {
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

    public function has_beginning(string $text, string $beginning): TestResult {
        $text = trim($text);
        $beginning = trim($beginning);

        $result = stripos($text, $beginning) === 0;

        if ($result) {
            return new TestResult(true, substr($text, 0, strlen($beginning)));
        } else {
            return new TestResult;
        }
    }

    // todo what exactly is the point of this...?
    public function has_category(Node|iterable $result, $categories): TestResult {
        if ($result instanceof Node) {
            $result = $result->getValue();
        }
        if (!(is_array($result) || $result instanceof ArrayAccess)) {
            $type = gettype($result);
            throw new MethodNodeException("Can only perform has_category on an array or ArrayAccess, got $type");
        }
        if (!isset($result['category'])) {
            throw new MethodNodeException("Can only perform has_category on a valid result structure, got: " . json_encode($result));
        }
        $categories = array_slice(func_get_args(), 1);
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
     */
    private function parseDateTimeFromString(string $string): bool|int {
        // remove non numeric or separator chars
        $string = trim(preg_replace('%[^\d\-/\\\:]%i', ' ', $string));
        // collapse whitespace
        $string = trim(preg_replace('/\s+/', ' ', $string));
        // try splitting the string into parts
        $parts = preg_split('%[/\-\\\]%', $string);

        if ($parts === false) {
            return false;
        }

        if (count($parts) < 3) {
            return false;
        }

        // try permutations of what we have
        foreach (new PermutationIterator($parts) as $permutation) {
            try {
                if ($result = strtotime(implode('/', $permutation))) {
                    return $result;
                }
            } catch (Exception) {
                continue;
            }
        }
        return false;
    }

    public function has_date(string $text): TestResult {
        $result = $this->parseDateTimeFromString($text);
        if ($result === false) {
            return new TestResult;
        } else {
            return new TestResult(true, Carbon::createFromTimestamp($result));
        }
    }

    private function has_date_callback($text, $compare, callable $fn): TestResult {
        $parsed = $this->parseDateTimeFromString($text);
        if ($parsed === false) {
            return new TestResult;
        }
        $compare = $this->parseDateTimeFromString($compare);
        if ($compare === false) {
            throw new MethodNodeException("arg 2 in has_date_eq must be a date, got " . json_encode($compare));
        }

        $parsed = Carbon::createFromTimestamp($parsed);
        $compare = Carbon::createFromTimestamp($compare);

        $result = $fn($parsed->copy(), $compare->startOfDay());

        if ($result) {
            return new TestResult(true, $parsed);
        } else {
            return new TestResult;
        }
    }

    public function has_date_eq(string $text, string $date): TestResult {
        return $this->has_date_callback($text, $date, function (Carbon $lhs, Carbon $rhs) {
            return $lhs->startOfDay()->eq($rhs);
        });
    }

    public function has_date_gt(string $text, string $min): TestResult {
        return $this->has_date_callback($text, $min, function (Carbon $lhs, Carbon $rhs) {
            return $lhs->gt($rhs);
        });
    }

    public function has_date_lt(string $text, string $max): TestResult {
        return $this->has_date_callback($text, $max, function (Carbon $lhs, Carbon $rhs) {
            return $lhs->lt($rhs);
        });
    }

    // todo: implementation?
    public function has_district(string $text, ?string $state = null): Contract\TestResult {
        throw new MethodNodeException('has_district not implemented');
    }

    public function has_email(string $text): TestResult {
        // this loose check should handle the cases we want.
        $regex = "/[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,64}/";
        $matches = [];
        if (preg_match($regex, $text, $matches)) {
            return new TestResult(true, $matches[0]);
        } else {
            return new TestResult;
        }
    }

    // todo: implementation once exceptions turn into error objects
    public function has_error($value): Contract\TestResult {
        throw new MethodNodeException('has_error not implemented');
    }

    public function has_group(Node|array $groups, string $group_uuid): TestResult {
        if ($groups instanceof Node) {
            $groups = $groups->getValue();
        }
        if (!(is_array($groups) || $groups instanceof ArrayAccess)) {
            $type = gettype($groups);
            throw new MethodNodeException("Can only perform has_group on an array or ArrayAccess, got $type");
        }
        foreach ($groups as $group) {
            if (isset($group['uuid']) && $group['uuid'] === $group_uuid) {
                return new TestResult(true, json_encode($group));
            }
        }
        return new TestResult;
    }

    public function has_intent(Node|array|string $result, string $name, float $confidence): TestResult {
        if ($result instanceof Node) {
            $result = $result->getValue();
        }
        if (!(is_array($result) || $result instanceof ArrayAccess)) {
            $type = gettype($result);
            throw new MethodNodeException("Can only perform has_group on an array or ArrayAccess, got $type");
        }
        if (!isset($result['extra']['intents'])) {
            return new TestResult;
        }
        foreach ($result['extra']['intents'] as $intent) {
            if (isset($intent['name']) && $intent['name'] === $name && isset($intent['confidence']) && $intent['confidence'] >= $confidence) {
                return new TestResult(true, json_encode($intent));
            }
        }
        return new TestResult;
    }

    const NUMBER_REGEX = "/\d+([.,]\d+)?/u";

    public function has_number(string $text): TestResult {
        $matches = [];

        $result = preg_match_all(self::NUMBER_REGEX, $text, $matches);

        if ($result) {
            return new TestResult(true, $matches[0][0]);
        } else {
            return new TestResult;
        }
    }

    private function assertNumeric($values) {
        foreach (func_get_args() as $value) {
            if (!(is_numeric($value))) {
                throw new MethodNodeException("Value must be a number has_number_between: " . json_encode(func_get_args()));
            }
        }
    }

    private function has_number_callback($text, callable $closure): TestResult {
        $matches = [];
        preg_match_all(self::NUMBER_REGEX, (string) $text, $matches);

        foreach ($matches[0] as $match) {
            if ($closure($match)) {
                return new TestResult(true, $match);
            }
        }
        return new TestResult;
    }

    public function has_number_between(string $text, int $min, int $max): TestResult {
        $this->assertNumeric($min, $max);
        return $this->has_number_callback($text, function ($number) use ($min, $max) {
            return $number >= $min && $number <= $max;
        });
    }

    public function has_number_eq(string $text, int $value): TestResult {
        $this->assertNumeric($value);
        return $this->has_number_callback($text, function ($number) use ($value) {
            return $number == $value;
        });
    }

    public function has_number_gt(string $text, int $min): TestResult {
        $this->assertNumeric($min);
        return $this->has_number_callback($text, function ($number) use ($min) {
            return $number > $min;
        });
    }

    public function has_number_gte(string $text, int $min): TestResult {
        $this->assertNumeric($min);
        return $this->has_number_callback($text, function ($number) use ($min) {
            return $number >= $min;
        });
    }

    public function has_number_lt(string $text, int $max): TestResult {
        $this->assertNumeric($max);
        return $this->has_number_callback($text, function ($number) use ($max) {
            return $number < $max;
        });
    }

    public function has_number_lte(string $text, int $max): TestResult {
        $this->assertNumeric($max);
        return $this->has_number_callback($text, function ($number) use ($max) {
            return $number <= $max;
        });
    }

    public function has_only_phrase(string $text, string $phrase): TestResult {
        $result = strcasecmp(trim($text), trim($phrase));

        if ($result === 0) {
            return new TestResult(true, $phrase);
        } else {
            return new TestResult;
        }
    }

    public function has_only_text(string $text1, string $text2): TestResult {
        if ($text1 === $text2) {
            return new TestResult(true, $text1);
        } else {
            return new TestResult;
        }
    }

    // todo this should return an object that has an "extra" field... what's that?
    public function has_pattern(string $text, string $pattern): TestResult {
        $pattern = addcslashes($pattern, '%');

        $matches = [];
        $result = preg_match("%$pattern%i", $text, $matches);

        if ($result) {
            return new TestResult(true, $matches[0]);
        } else {
            return new TestResult;
        }
    }

    public function has_phone(string $text, ?string $country_code = null): TestResult {
        $phoneUtil = PhoneNumberUtil::getInstance();

        $text = preg_replace('/[^\d+-]/', '', $text);

        try {
            $number = $phoneUtil->parse($text, $country_code);

            if ($phoneUtil->isValidNumber($number)) {
                return new TestResult(true, $phoneUtil->format($number, PhoneNumberFormat::E164));
            } else {
                return new TestResult;
            }
        } catch (Exception) {
            return new TestResult;
        }
    }

    public function has_phrase(string $text, string $phrase): TestResult {
        $matches = [];
        $result = preg_match("/$phrase/i", $text, $matches);

        if ($result) {
            return new TestResult(true, $matches[0]);
        } else {
            return new TestResult;
        }
    }

    // todo: implementation? perhaps up to the consumer
    public function has_state(string $text): Contract\TestResult {
        throw new MethodNodeException('has_state not implemented');
    }

    public function has_text(string $text): TestResult {
        $result = preg_match("/\S/", $text);

        if ($result) {
            return new TestResult(true, $text);
        } else {
            return new TestResult;
        }
    }

    public function has_time(string $text): TestResult {
        $matches = [];
        $result = preg_match('/\d{2}((:\d{2}){1,2}|\s[\w]{2})/', $text, $matches);

        if ($result) {
            $date = strtotime($matches[0]);
            if ($date) {
                return new TestResult(true, Carbon::createFromTimestamp($date)->toTimeString());
            }
        }
        return new TestResult;
    }

    // todo: implementation
    public function has_top_intent(string $result, string $name, int $confidence): Contract\TestResult {
        throw new MethodNodeException('has_top_intent not implemented');
    }

    // todo: implementation
    public function has_ward(string $text, string $district, string $state): Contract\TestResult {
        throw new MethodNodeException('has_ward not implemented');
    }

    public function handles(): array {
        return [
            'has_all_words',
            'has_any_word',
            'has_beginning',
            'has_category',
            'has_date',
            'has_date_eq',
            'has_date_gt',
            'has_date_lt',
            'has_district',
            'has_email',
            'has_error',
            'has_group',
            'has_intent',
            'has_number',
            'has_number_between',
            'has_number_eq',
            'has_number_gt',
            'has_number_gte',
            'has_number_lt',
            'has_number_lte',
            'has_only_phrase',
            'has_only_text',
            'has_pattern',
            'has_phone',
            'has_phrase',
            'has_state',
            'has_text',
            'has_time',
            'has_top_intent',
            'has_ward',
        ];
    }
}
