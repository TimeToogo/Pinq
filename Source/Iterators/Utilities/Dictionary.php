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
    
    private static function arrayIdentityHash(array $array)
    {
        array_walk_recursive($array, function (&$value) {
            //Cast identifier to object so it is impossible to collide with other types of values
            switch (gettype($value)) {
                case 'object':
                    $value = (object)['o' => spl_object_hash($value)];
                    break;
                
                case 'resource':
                case 'unknown type':
                    $value = (object)['r' => (string)$value];
                    break;
            }
        });
        
        return md5(serialize($array));
    }

    public function get($key)
    {
        if ($key === null) {
            return isset($this->storage['NULL']) ? $this->storage['NULL'] : null;
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
            case 'unknown type':
                $stringKey = (string)$key;

                return isset($typeStorage[$stringKey][1]) ? $typeStorage[$stringKey][1] : null;

            case 'array':
                $identityKey = self::arrayIdentityHash($key);
                
                return isset($typeStorage[$identityKey]) ? $typeStorage[$identityKey][1] : null;
        }
    }

    public function contains($key)
    {
        if ($key === null) {
            return isset($this->storage['NULL']) || array_key_exists('NULL', $this->storage);
        }

        $type = gettype($key);
        $typeStorage =& $this->storage[$type];
        switch ($type) {

            case 'string':
            case 'integer':
                return isset($typeStorage[$key]) || array_key_exists($key, $typeStorage);
                
            case 'boolean':
                return isset($typeStorage[$key]) || array_key_exists((int)$key, $typeStorage);
                
            case 'object':
                return isset($typeStorage[$key]);

            case 'double':
            case 'resource':
            case 'unknown type':
                $stringKey = (string)$key;
                
                return isset($typeStorage[$stringKey]) || array_key_exists($stringKey, $typeStorage);

            case 'array':
                $identityHash = self::arrayIdentityHash($key);
                
                return isset($typeStorage[$identityHash]) || array_key_exists($identityHash, $typeStorage);
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
            case 'unknown type':
                $typeStorage[(string)$key] = [$key, $value];
                break;

            case 'array':
                $typeStorage[self::arrayIdentityHash($key)] = [$key, $value];
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
            case 'unknown type':
                unset($typeStorage[(string)$key]);
                break;

            case 'NULL':
                unset($this->storage['NULL']);
                break;

            case 'array':
                unset($typeStorage[self::arrayIdentityHash($key)]);
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
     * Returns all the values in the dictionary as an array.
     *
     * @return array
     */
    public function values()
    {
        $values = [];
        
        //Stored as plain value
        foreach(['string', 'integer', 'boolean', 'double'] as $storageKey) {
            foreach($this->storage[$storageKey] as $value) {
                $values[] = $value;
            }
        }
        
        $objectKeyStorage = $this->storage['object'];
        foreach($objectKeyStorage as $object) {
            $values[] = $objectKeyStorage[$object];
        }
        
        $values[] = $this->storage['NULL'];
        
        //Stored as tuple
        foreach(['resource', 'unknown type', 'array'] as $storageKey) {
            foreach($this->storage[$storageKey] as $value) {
                $values[] = $value[1];
            }
        }
        
        return $values;
    }

    /**
     * This will return an iterator for all the keys in the dictionary.
     * ->get($key) can be called to retrieve the value.
     * This is due to PHP limiting the types of values allowed to be returned
     * as an iterator key.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $firstIndex = function ($value) { return $value[0]; };
        return new \ArrayIterator(array_values(array_merge(
                array_map('strval', array_keys($this->storage['string'])),
                array_keys($this->storage['integer']),
                array_map(function ($i) { return (bool)$i; }, array_keys($this->storage['boolean'])),
                array_map('doubleval', array_keys($this->storage['double'])),
                iterator_to_array($this->storage['object'], false),
                array_key_exists('NULL', $this->storage) ? [null] : [],
                array_map($firstIndex, $this->storage['resource']),
                array_map($firstIndex, $this->storage['array']),
                array_map($firstIndex, $this->storage['unknown type']))));
    }
}
