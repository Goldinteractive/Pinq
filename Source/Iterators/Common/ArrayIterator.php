<?php

namespace Pinq\Iterators\Common;

/**
 * Common functionality for the array iterator
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
trait ArrayIterator
{
    /**
     * @var array
     */
    protected $array;

    public function __constructIterator(array &$array)
    {
        $this->array =& $array;
    }
    
    /**
     * @return array
     */
    final public function getArrayCopy()
    {
        return $this->array;
    }
}