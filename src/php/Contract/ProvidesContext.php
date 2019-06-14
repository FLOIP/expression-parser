<?php

namespace Viamo\Floip\Contract;

interface ProvidesContext
{
    /**
     * This method should return the value associated with $key
     * for FLOIP expression evaluation.
     *
     * @param string $key
     * @return mixed
     */
    public function getContextValue($key);

    /**
     * This method should return whether or not the specified $key
     * exists for FLOIP expression evaluation.
     *
     * @param string $key
     * @return bool
     */
    public function hasContextKey($key);

    /**
     * This method should return the entire context as an array.
     *
     * @return array
     */
    public function getContextArray();
}
