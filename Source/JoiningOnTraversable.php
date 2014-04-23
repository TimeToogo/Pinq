<?php 

namespace Pinq;

/**
 * Implements the filtering API for a join / group join traversable.
 *
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class JoiningOnTraversable implements IJoiningOnTraversable
{
    /**
     * @var \Traversable
     */
    private $outerValues;
    
    /**
     * @var \Traversable
     */
    private $innerValues;
    
    /**
     * @var boolean
     */
    private $isGroupJoin;
    
    /**
     * @param boolean $isGroupJoin
     */
    public function __construct(\Traversable $outerValues, \Traversable $innerValues, $isGroupJoin)
    {
        $this->outerValues = $outerValues;
        $this->innerValues = $innerValues;
        $this->isGroupJoin = $isGroupJoin;
    }
    
    public function on(callable $joiningOnFunction)
    {
        return new JoiningToTraversable(function (callable $joiningFunction) use($joiningOnFunction) {
            if ($this->isGroupJoin) {
                return new Iterators\CustomGroupJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $joiningOnFunction,
                        $joiningFunction);
            }
            else {
                return new Iterators\CustomJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $joiningOnFunction,
                        $joiningFunction);
            }
        });
    }
    
    public function onEquality(callable $outerKeyFunction, callable $innerKeyFunction)
    {
        return new JoiningToTraversable(function (callable $joiningFunction) use($outerKeyFunction, $innerKeyFunction) {
            if ($this->isGroupJoin) {
                return new Iterators\EqualityGroupJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $outerKeyFunction,
                        $innerKeyFunction,
                        $joiningFunction);
            }
            else {
                return new Iterators\EqualityJoinIterator(
                        $this->outerValues,
                        $this->innerValues,
                        $outerKeyFunction,
                        $innerKeyFunction,
                        $joiningFunction);
            }
        });
    }
}