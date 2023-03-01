<?php

namespace Viamo\Floip\Util;
use ArrayAccess;

/**
 * Adapted from Illuminate\Support\Arr
 */
class Arr
{
    /**
     * Check if an item or items exist in an array using "dot" notation.
     */
    public static function has(ArrayAccess|array $array, string|array $keys): bool {
        $keys = (array) $keys;

        if (!$array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', (string) $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Determine if the given key exists in the provided array.
     */
    public static function exists(ArrayAccess|array $array, string|int $key): bool {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * Determine whether the given value is array accessible.
     */
    public static function accessible(mixed $value): bool {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine whether the given thing looks like an array.
     */
    public static function isArray(mixed $thing): bool {
        return $thing instanceof ArrayAccess || is_array($thing);
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     */
    public static function isAssoc(array $array): bool {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}
