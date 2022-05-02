<?php

namespace Pinq\Iterators\Common;

/**
 * Contains the common functionality for the IOrderedMap implementation.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
trait OrderedMap
{
    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $keyIdentityPositionMap = [];

    /**
     * @var int
     */
    protected $length = 0;

    /**
     * @var int
     */
    protected $largestIntKey = -1;

    /**
     * {@inheritDoc}
     */
    public function keys()
    {
        return $this->keys;
    }

    /**
     * {@inheritDoc}
     */
    public function values()
    {
        return $this->values;
    }

    /**
     * {@inheritDoc}
     */
    public function map(callable $function)
    {
        $function = Functions::allowExcessiveArguments($function);

        $clone = clone $this;

        foreach ($clone->keyIdentityPositionMap as $position) {
            $keyCopy                  = $this->keys[$position];
            $valueCopy                = $this->values[$position];
            $clone->values[$position] = $function($valueCopy, $keyCopy);
        }

        return $clone;
    }

    /**
     * {@inheritDoc}
     */
    public function walk(callable $function)
    {
        $function = Functions::allowExcessiveArguments($function);

        foreach ($this->keys as $position => $key) {
            $function($this->values[$position], $key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function groupBy(callable $groupKeyFunction)
    {
        $groupedMap = new self();

        foreach ($this->keyIdentityPositionMap as $identityHash => $position) {
            $keyCopy   = $key = $this->keys[$position];
            $valueCopy = $value =& $this->values[$position];

            $groupKey = $groupKeyFunction($valueCopy, $keyCopy);

            if ($groupedMap->contains($groupKey)) {
                $groupMap = $groupedMap->get($groupKey);
            } else {
                $groupMap = new self();
                $groupedMap->set($groupKey, $groupMap);
            }

            $groupMap->setInternal($key, $value, $identityHash, true);
        }

        return $groupedMap;
    }

    /**
     * {@inheritDoc}
     */
    public function multisort(array $orderByFunctions, array $isAscending)
    {
        $positionKeyIdentityMap = [];
        $multisortArguments     = [];

        foreach ($this->keyIdentityPositionMap as $keyIdentityHash => $position) {
            $positionKeyIdentityMap['0' . $position] = $keyIdentityHash;
        }

        foreach ($orderByFunctions as $key => $function) {
            $orderByValues = [];

            foreach ($this->keyIdentityPositionMap as $position) {
                $orderByValues['0' . $position] = $function($this->values[$position], $this->keys[$position]);
            }

            $multisortArguments[] =& $orderByValues;
            $multisortArguments[] = $isAscending[$key] ? SORT_ASC : SORT_DESC;
            $multisortArguments[] = SORT_REGULAR;

            unset($orderByValues);
        }

        $multisortArguments[] =& $positionKeyIdentityMap;

        call_user_func_array('array_multisort', $multisortArguments);

        $sortedMap = new self();

        $newPosition = 0;
        foreach ($positionKeyIdentityMap as $stringPosition => $keyIdentityHash) {
            $originalPosition = (int) $stringPosition;

            $sortedMap->keyIdentityPositionMap[$keyIdentityHash] = $newPosition;
            $sortedMap->keys[$newPosition]                       = $this->keys[$originalPosition];
            $sortedMap->values[$newPosition]                     =& $this->values[$originalPosition];

            $newPosition++;
        }

        return $sortedMap;
    }

    public function count(): int
    {
        return $this->length;
    }

    private function loadLargestIntKey()
    {
        $this->largestIntKey = -1;
        foreach ($this->keys as $key) {
            if (is_int($key) && $key > $this->largestIntKey) {
                $this->largestIntKey = $key;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function &get($key)
    {
        $identityHash = Identity::hash($key);

        if (isset($this->keyIdentityPositionMap[$identityHash])) {
            return $this->values[$this->keyIdentityPositionMap[$identityHash]];
        } else {
            $null = null;

            return $null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function contains($key)
    {
        return isset($this->keyIdentityPositionMap[Identity::hash($key)]);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->setInternal($key, $value, Identity::hash($key));
    }

    /**
     * {@inheritDoc}
     */
    public function setRef($key, &$value)
    {
        $this->setInternal($key, $value, Identity::hash($key), true);
    }

    final protected function setInternal($key, &$value, $identityHash, $reference = false)
    {
        if (isset($this->keyIdentityPositionMap[$identityHash])) {
            $position = $this->keyIdentityPositionMap[$identityHash];
        } else {
            $position                                    = $this->length++;
            $this->keyIdentityPositionMap[$identityHash] = $position;
        }
        if (is_int($key) && $key > $this->largestIntKey) {
            $this->largestIntKey = $key;
        }

        $this->keys[$position] = $key;
        if ($reference) {
            $this->values[$position] =& $value;
        } else {
            $this->values[$position] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $identityHash = Identity::hash($key);

        if (isset($this->keyIdentityPositionMap[$identityHash])) {
            $position = $this->keyIdentityPositionMap[$identityHash];

            unset($this->keys[$position],
            $this->values[$position],
            $this->keyIdentityPositionMap[$identityHash]);

            if ($position !== $this->length) {
                $this->keys   = array_values($this->keys);
                $this->values = array_values($this->values);
            }
            $this->length--;

            if ($key === $this->largestIntKey) {
                $this->loadLargestIntKey();
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->keyIdentityPositionMap = [];
        $this->keys                   = [];
        $this->values                 = [];
        $this->length                 = 0;
        $this->largestIntKey          = -1;
    }

    public function offsetExists($offset): bool
    {
        return $this->contains($offset);
    }

    #[\ReturnTypeWillChange]
    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $offset = ++$this->largestIntKey;
        }
        $this->set($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->remove($offset);
    }

    /**
     * @return bool
     */
    final public function isArrayCompatible()
    {
        return false;
    }
}
