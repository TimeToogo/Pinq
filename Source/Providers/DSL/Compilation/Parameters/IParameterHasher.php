<?php
namespace Pinq\Providers\DSL\Compilation\Parameters;

/**
 * Interface of the query parameter hasher.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
interface IParameterHasher
{
    /**
     * Returns a unique identifier of the query parameter according the resolved values.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function hash($value);
}
