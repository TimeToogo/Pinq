<?php

namespace Pinq\Iterators\Utilities;

/**
 * This class is like an array that supports keys of any type: Optimized for quick lookup speeds.
 * Depending on the key type, the key value pair is stored in the most optimized way to check if
 * they are contained in the dictionary.
 *
 * Key type     - Value storage method:
 *
 * string       - array value indexed by key
 * integer      - array value indexed by key
 * double       - array value indexed by string cast key
 * boolean      - array value indexed by key (auto casts to int 1/0)
 * object       - info in \SplObjectStorage
 * null         - Single value (only one null)
 * array        - array tuple (no good way to hash an array identity)
 * resource     - array tuple with string cast array key (will become 'Resource id #{id}', guaranteed unique as per docs)
 * unknown type - array tuple (wtf, why does this exist)
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class Dictionary implements \IteratorAggregate
{
    /**
     * @var array
     */
    private $storage;

    public function __construct()
    {
        $this->storage = [
            'array' => [],
            'boolean' => [],
            'integer' => [],
            'double' => [],
            'string' => [],
            'object' => new \SplObjectStorage(),
            'resource' => [],
            'unknown type' => []
        ];
    }

    public function get($key)
    {
        if ($key === null) {
            return $this->storage['NULL'];
        }

        $type = gettype($key);
        $typeStorage =& $this->storage[$type];
        switch ($type) {

            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                return isset($typeStorage[$key]) ? $typeStorage[$key] : null;

            case 'double':
                $stringKey = (string)$key;

                return isset($typeStorage[$stringKey]) ? $typeStorage[$stringKey] : null;

            case 'resource':
                $stringKey = (string)$key;

                return isset($typeStorage[$stringKey][1]) ? $typeStorage[$stringKey][1] : null;

            case 'array':

            case 'unknown type':
                foreach ($typeStorage as $keyValuePair) {
                    if ($keyValuePair[0] === $key) {
                        return $keyValuePair[1];
                    }
                }

                return null;
        }
    }

    public function contains($key)
    {
        if ($key === null) {
            return array_key_exists('NULL', $this->storage);
        }

        $type = gettype($key);
        $typeStorage =& $this->storage[$type];
        switch ($type) {

            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                return isset($typeStorage[$key]);

            case 'double':

            case 'resource':
                return isset($typeStorage[(string)$key]);

            case 'array':

            case 'unknown type':
                foreach ($typeStorage as $keyValuePair) {
                    if ($keyValuePair[0] === $key) {
                        return true;
                    }
                }

                return false;
        }
    }

    public function set($key, $value)
    {
        $type = gettype($key);
        $typeStorage =& $this->storage[$type];
        switch ($type) {

            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                $typeStorage[$key] = $value;
                break;

            case 'double':
                $typeStorage[(string)$key] = $value;
                break;

            case 'NULL':
                $typeStorage = $value;
                break;

            case 'resource':
                $typeStorage[(string)$key] = [$key, $value];
                break;

            case 'array':

            case 'unknown type':
                $typeStorage[] = [$key, $value];
                break;
        }
    }

    public function addRange($values)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function remove($key)
    {
        $type = gettype($key);
        $typeStorage =& $this->storage[$type];
        switch ($type) {

            case 'string':
            case 'integer':
            case 'boolean':
            case 'object':
                unset($typeStorage[$key]);
                break;

            case 'double':

            case 'resource':
                unset($typeStorage[(string)$key]);
                break;

            case 'NULL':
                unset($this->storage['NULL']);
                break;

            case 'array':

            case 'unknown type':
                foreach ($typeStorage as $storageKey => $keyValuePair) {
                    if ($keyValuePair[0] === $key) {
                        unset($typeStorage[$storageKey]);
                        break;
                    }
                }

                break;
        }
    }

    public function removeRange($keys)
    {
        foreach ($keys as $key) {
            $this->remove($key);
        }
    }

    /**
     * This will return an iterator for all the keys in the dictionary.
     * ->Get($Key) can be called to retrieve the value.
     * This is due to PHP limiting the types of values allowed to be returned
     * as an iterator key.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array_values(array_merge(
                array_map('strval', array_keys($this->storage['string'])),
                array_keys($this->storage['integer']),
                array_map(function ($i) { return (bool)$i; }, array_keys($this->storage['boolean'])),
                array_map('doubleval', array_keys($this->storage['double'])),
                iterator_to_array($this->storage['object'], false),
                array_key_exists('NULL', $this->storage) ? [null] : [],
                array_map('reset', $this->storage['resource']),
                array_map('reset', $this->storage['array']),
                array_map('reset', $this->storage['unknown type']))));
    }
}
