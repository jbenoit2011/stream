<?php
namespace StreamTest;

use PHPUnit_Framework_TestCase;
use Stream\Stream;

class StreamTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Stream;
     */
    private $sut;

    public function setUp()
    {
    }

    public function testMemoryUsageMustNotGrow()
    {
        $source = function () {
            for ($i = 0; $i <= 10**5; $i++) {
                yield $i;
            }
        };

        $this->sut = new Stream($source());

        $mapFn = function ($i) {

            return $i+1;
        };

        $this->sut->map($mapFn)
            ->each(function () {});

        $this->assertLessThanOrEqual(5*(10**6), memory_get_usage());
    }

    public function testItemsMustBeMapped()
    {
        $source = function () {
            for ($i = 0; $i <= 3; $i++) {
                yield $i;
            }
        };

        $mapFn = function ($i) {

            return $i+1;
        };

        $this->sut = (new Stream($source()))->map($mapFn);

        $this->assertEquals(range(1, 4), $this->sut->toArray());
    }

    public function testItemsMustBeReduced()
    {
        $source = function () {
            for ($i = 0; $i <= 4; $i++) {
                yield $i;
            }
        };

        $reduceFn = function ($a, $b) {

            return $a + $b;
        };

        $reduction = (new Stream($source()))->reduce($reduceFn, 0);

        $this->assertEquals(10, $reduction);
    }

    public function testItemsMustBeMappedAndReduced()
    {
        $source = function () {
            for ($i = 0; $i <= 3; $i++) {
                yield $i;
            }
        };

        $mapFn = function ($i) {

            return $i+1;
        };

        $reduceFn = function ($a, $b) {

            return $a + $b;
        };

        $reduction = (new Stream($source()))->map($mapFn)->reduce($reduceFn, 0);

        $this->assertEquals(10, $reduction);
    }

    public function testItemsMustBeFiltered()
    {
        $source = function () {
            for ($i = 0; $i <= 3; $i++) {
                yield $i;
            }
        };

        $oddFilterFn = function ($i) {

            return $i%2 === 0;
        };

        $output = (new Stream($source()))->filter($oddFilterFn)->toArray();

        $this->assertEquals($output,[0, 2]);
    }

    public function testItemsMustBeTook()
    {
        $source = function () {
            for ($i = 0; $i <= 3; $i++) {
                yield $i;
            }
        };

        $output = (new Stream($source()))->take(2)->toArray();

        $this->assertCount(2, $output);
    }

    public function testHeadOfStreamMustBeEqualToOne()
    {
        $source = function () {
            for ($i = 0; $i <= 3; $i++) {
                yield $i;
            }
        };

        $output = (new Stream($source()))->head()->toArray();

        $this->assertCount(1, $output);
    }

    public function testStreamMustBeInterspersed()
    {
        $source = function () {
            for ($i = 0; $i <= 3; $i++) {
                yield $i;
            }
        };

        $output = (new Stream($source()))->intersperse(10)->toArray();

        $this->assertEquals([10, 0, 10, 1, 10, 2, 10, 3], $output);
    }
}
