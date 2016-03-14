<?php
namespace Stream;

use ArrayIterator;
use Generator;
use Iterator;
use IteratorAggregate;
use LimitIterator;

class Stream implements StreamInterface, IteratorAggregate
{
    /**
     * Stream source
     *
     * @var Iterator
     */
    protected $source;

    /**
     * Stream constructor.
     *
     * @param $source
     */
    public function __construct(Iterator $source)
    {
        if (is_array($this->source)) {
            $this->source = new ArrayIterator($this->source);
        }

        $this->source = $source;
    }

    /**
     * Apply a function on each items
     *
     * @param callable $mapper
     *
     * @return Stream
     */
    public function map(callable $mapper)
    {
        $generator = $this->doMap($mapper);

        return new self($generator);
    }

    /**
     * @param callable $fn
     *
     * @return Generator
     */
    protected function doMap(callable $fn)
    {
        foreach ($this->source as $item) {
            yield call_user_func($fn, $item);
        }
    }

    /**
     * Apply a function on each items to reduce list
     * to a single value
     *
     * @param callable $accumulator
     * @param mixed $accumulation
     *
     * @return Stream
     */
    public function reduce(callable $accumulator, $accumulation = null)
    {
        foreach($this->source as $item) {
            $accumulation = call_user_func($accumulator, $item, $accumulation);
        }

        return $accumulation;
    }

    /**
     * @param callable $filter
     *
     * @return Stream
     */
    public function filter(callable $filter)
    {
        $generator = $this->doFilter($filter);

        return new self($generator);
    }

    /**
     * @param callable $filter
     *
     * @return Generator
     */
    protected function doFilter(callable $filter)
    {
        foreach ($this->source as $item) {
            if ($value = call_user_func($filter, $item)) {
                yield $item;
            }
        }
    }

    /**
     * Returns all values of the stream to an array
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach($this->source as $item) {
            $array[] = $item;
        }

        return $array;
    }

    /**
     * @param callable $fn
     */
    public function each(callable $fn)
    {
        foreach ($this->source as $item) {
            call_user_func($fn, $item);
        }
    }

    /**
     * @param mixed $sep
     *
     * @return Stream
     */
    public function intersperse($sep)
    {
        $generator = $this->doIntersperse($sep);

        return new self($generator);
    }

    /**
     * @param $sep
     *
     * @return Generator
     */
    protected function doIntersperse($sep)
    {
        $lastInterspersed = false;
        do {
            if ($lastInterspersed === false) {
                $value = $sep;
                $lastInterspersed = true;
            } else {
                $value = $this->source->current();
                $this->source->next();
                $lastInterspersed = false;
            }

            yield $value;
        } while($this->source->valid());
    }

    /**
     * @return Stream
     */
    public function head()
    {
        return $this->take(1);
    }

    /**
     * @param $count
     *
     * @return Stream
     */
    public function take($count)
    {
        $generator = $this->doTake($count);

        return new self($generator);
    }

    /**
     * @param $count
     *
     * @return Generator
     */
    protected function doTake($count)
    {
        $limitIt = new LimitIterator($this->source, 0, $count);
        foreach($limitIt as $item) {
            yield $item;
        }
    }

    /**
     * @return Iterator $iterator
     */
    public function getIterator()
    {
        return $this->source;
    }
}
