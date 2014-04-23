<?php

namespace Pinq\Tests\Integration\Traversable;

class IssetIndexTest extends TraversableTest
{
    /**
     * @dataProvider Everything
     */
    public function testThatIssetOnValidIndexesReturnTrue(\Pinq\ITraversable $traversable, array $data)
    {
        foreach ($data as $key => $value) {
            $this->assertTrue(isset($traversable[$key]));
        }
    }

    /**
     * @dataProvider Everything
     */
    public function testThatIssetOnInvalidIndexesReturnFalse(\Pinq\ITraversable $traversable, array $data)
    {
        $notAnIndex = 0;

        while (isset($data[$notAnIndex])) {
            $notAnIndex++;
        }

        $this->assertFalse(isset($traversable[$notAnIndex]));
    }
}
