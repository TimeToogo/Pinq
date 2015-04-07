<?php

namespace Pinq\Analysis;

use Pinq\Expressions as O;

/**
 * Implementation of the expression type analyser that will
 * convert exceptions from not being able to be statically analysed
 * into the mixed type.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class TolerantExpressionAnalyser extends ExpressionAnalyser
{
    protected function doWalk(O\Expression $expression)
    {
        $caught = false;
        try {
            return parent::doWalk($expression);
        } catch (TypeException $exception) {
            $caught = true;
        } catch (\ReflectionException $exception) {
            $caught = true;
        }

        if($caught) {
            $this->analysis[$expression] = $this->typeSystem->getNativeType(INativeType::TYPE_MIXED);
            return $expression;
        }
    }
}
