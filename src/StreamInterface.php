<?php

namespace Stream;

interface StreamInterface
{
    /**
     * Apply a function on each items
     *
     * @param callable $mapper
     *
     * @return StreamInterface
     */
    public function map(callable $mapper);

    /**
     * Apply a function on each items to reduce list
     * to a scalar
     *
     * @note Terminal operation
     *
     * @param callable $accumulator
     * @param null $accumulation
     *
     * @return mixed
     */
    public function reduce(callable $accumulator, $accumulation = null);

    /**
     * Filter items of stream according filter function
     *
     * @param callable $filter
     *
     * @return StreamInterface
     */
    public function filter(callable $filter);

    /**
     * Intersperse an element between each items of filter
     *
     * @param $separator
     *
     * @return StreamInterface
     */
    public function intersperse($separator);

    /**
     * Alias for take(1)
     *
     * @return StreamInterface
     */
    public function head();

    /**
     * Take a only the specified count of items at the start of stream
     *
     * @param $count
     *
     * @return StreamInterface
     */
    public function take($count);

    /**
     * Apply a function on each items of streams
     *
     * @note Terminal operation
     *
     * @param callable $fn
     *
     * @return StreamInterface
     */
    public function each(callable $fn);

    /**
     * Returns all values of the stream to an array
     *
     * @return array
     */
    public function toArray();
}
