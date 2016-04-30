<?php

namespace DPolac\OnlyOnce\Tests;

use DPolac\OnlyOnce\OnlyOnceExtension;

class OnlyOnceExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OnlyOnceExtension
     */
    private $ext;

    public function setUp()
    {
        $this->ext = new OnlyOnceExtension();
    }

    /**
     * @dataProvider incorrectSpaceNameProvider
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyOnce_SpaceIsNotString_ThrowException($spaceName)
    {
        $this->ext->onlyOnce(1, $spaceName);
    }

    /**
     * @dataProvider incorrectSpaceNameProvider
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyOnceWhenOccurs_SpaceIsNotString_ThrowException($spaceName)
    {
        $this->ext->onlyOnceWhenOccurs(10, "A", $spaceName);
    }

    public function incorrectSpaceNameProvider()
    {
        return array(
            'integer' => array(13),
            'float'   => array(1.13),
            'array'   => array(array(1, "A")),
            'object'  => array(new \stdClass()),
            'true'    => array(true),
            'null'    => array(null),
        );
    }

    /**
     * @dataProvider incorrectOccurrenceNumberProvider
     * @expectedException \InvalidArgumentException
     */
    public function testOnlyOnceWhenOccurs_OccurrenceNumberInvalid_ThrowException($number)
    {
        $this->ext->onlyOnceWhenOccurs("A", $number);
    }

    public function incorrectOccurrenceNumberProvider()
    {
        return array(
            'negative'              => array(-13),
            'zero'                  => array(0),
            'float'                 => array(7.7),
            'string with number'    => array('12'),
            'string'                => array('abc'),
            'null'                  => array(null),
            'true'                  => array(true),
        );
    }

    /**
     * @dataProvider singleValueProvider
     */
    public function testOnlyOnce_CalledMultipleTimesForOneValue_ReturnTrueOnce($value)
    {
        $this->assertTrue($this->ext->onlyOnce($value));
        for($i = 1; $i <= 19; ++$i) {
            $this->assertFalse($this->ext->onlyOnce($value));
        }
    }

    /**
     * @dataProvider singleValueProvider
     */
    public function testOnlyOnceWhenOccurs_CalledMultipleTimesForOneValue_ReturnTrueOnce($value)
    {
        for($i = 1; $i <= 19; ++$i) {
            $this->assertFalse($this->ext->onlyOnceWhenOccurs($value, 20));
        }

        $this->assertTrue($this->ext->onlyOnceWhenOccurs($value, 20));

        for($i = 1; $i <= 19; ++$i) {
            $this->assertFalse($this->ext->onlyOnceWhenOccurs($value, 20));
        }
    }

    /**
     * @dataProvider singleValueProvider
     */
    public function testOnlyOnce_CalledOneValueForMultipleSpaces_AlwaysReturnTrue($value)
    {
        foreach (array("a", "b", "c") as $prefix) {
            for ($i = 0; $i < 10; ++$i) {
                $this->assertTrue($this->ext->onlyOnce($value, $prefix.$i));
            }
        }
    }

    /**
     * @dataProvider singleValueProvider
     */
    public function testOnlyOnceWhenOccurs_CalledOneValueForMultipleSpaces_AlwaysReturnFalse($value)
    {
        foreach (array("a", "b", "c") as $prefix) {
            for ($i = 0; $i < 10; ++$i) {
                $this->assertFalse($this->ext->onlyOnceWhenOccurs($value, 2, $prefix.$i));
            }
        }
    }

    /**
     * @dataProvider listOfValuesProvider
     */
    public function testOnlyOnce_CalledForDifferentValuesAndSpaces_ReturnsTrueOnceForEveryValue(array $values)
    {
        foreach (array("a", "b") as $space) {
            $this->assertTrue($this->ext->onlyOnce($values[0], $space));
            $this->assertFalse($this->ext->onlyOnce($values[0], $space));

            for ($i = 1; $i <= 4; ++$i) {
                $this->assertTrue($this->ext->onlyOnce($values[$i], $space));
            }

            for ($i = 1; $i <= 4; ++$i) {
                $this->assertFalse($this->ext->onlyOnce($values[$i], $space));
            }
        }
    }

    /**
     * @dataProvider listOfValuesProvider
     */
    public function testOnlyOnceWhenOccurs_CalledForDifferentValuesAndSpaces_ReturnsTrueOnceForEveryValue(array $values)
    {
        foreach (array("a", "b") as $space) {
            $this->assertFalse($this->ext->onlyOnceWhenOccurs($values[0], 2, $space));
            $this->assertTrue($this->ext->onlyOnceWhenOccurs($values[0], 2, $space));
            $this->assertFalse($this->ext->onlyOnceWhenOccurs($values[0], 2, $space));

            for ($i = 1; $i <= 4; ++$i) {
                $this->assertFalse($this->ext->onlyOnceWhenOccurs($values[$i], 2, $space));
            }

            for ($i = 1; $i <= 4; ++$i) {
                $this->assertTrue($this->ext->onlyOnceWhenOccurs($values[$i], 2, $space));
            }
        }
    }

    public function singleValueProvider()
    {
        return array(
            'integer'   => array(36),
            'float'     => array(0.17),
            'string'    => array('abc'),
            'array'     => array(array(1, 2, 3)),
            'object'    => array(new \stdClass()),
        );
    }

    /**
     * @return  array   Array with exactly 5 different elements.
     */
    public function listOfValuesProvider()
    {
        return array(
            'integers'      => array(array(1, 2, 3, 4, 5)),
            'floats'        => array(array(1.1, 2.2, 3.1, 44.4, 51.0)),
            'strings'       => array(array('abc', 'cde', 'efg', 'hij', 'zzz')),
            'objects'       => array(array(
                new \stdClass(),
                new \stdClass(),
                new \stdClass(),
                new \stdClass(),
                new \stdClass(),
            )),
            'arrays'        => array(array(
                array(1,2,3),
                array(),
                array(5,6,7),
                array(1,2,3,4),
                array(5),
            )),
            'mixed arrays'  => array(array(
                array(1,2,3),
                array(1,2,3,4),
                array("1", "2", "3"),
                array(new \stdClass()),
                array(new \stdClass()),
            )),
            'mixed'         => array(array(
                true,
                new \stdClass(),
                12,
                'xyz',
                new \stdClass()
            )),
            'nested arrays' => array(array(
                array(1, 2, array(3)),
                array(1, array(2), 3),
                array(1, new \stdClass(), array(3)),
                array(array(1, 2), array(new \stdClass())),
                array(array(array(1, 2, 'a'))),
            )),
            'dictionary'    => array(array(
                array('a' => 'apple', 'o' => 'orange'),
                array('a' => 'apple', 'o' => 'apple'),
                array('a' => 'apple', 'o' => 'orange', 'b' => 'banana'),
                array('a' => 'orange', 'o' => 'apple', 'b' => 'banana'),
                array('o' => 'orange'),
            )),
        );
    }

    /**
     * @dataProvider timesProvider
     */
    public function testOnlyOnceWhenOccurs_CalledMultipleTimes_ReturnTrueOnlyOnce($times)
    {
        for ($i = 1; $i < $times; ++$i) {
            $this->assertFalse($this->ext->onlyOnceWhenOccurs("abc", $times));
        }

        $this->assertTrue($this->ext->onlyOnceWhenOccurs("abc", $times));

        for ($i = 1; $i < $times; ++$i) {
            $this->assertFalse($this->ext->onlyOnceWhenOccurs("abc", $times));
        }
    }

    public function timesProvider()
    {
        return array(
            1   =>  array(1),
            2   =>  array(2),
            3   =>  array(3),
            5   =>  array(5),
            15  =>  array(15),
            100 =>  array(100),
            400 =>  array(400),
        );
    }
}
