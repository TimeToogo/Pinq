<?php

namespace Pinq\Expressions\Walkers;

use Pinq\Expressions as O;
use Pinq\Expressions\ClosureExpression;
use Pinq\Parsing\IFunctionScope;

/**
 * Simplifies the expression tree.
 * Example:
 * <code>
 * -2 + 4
 * </code>
 * Will become:
 * <code>
 * 2
 * </code>
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class ExpressionSimplifier extends O\ExpressionWalker
{
    /**
     * The scope class to simplify the expressions from.
     *
     * @var IFunctionScope|null
     */
    private $scope;

    /**
     * The namespace to attempt local resolution of constants and functions.
     *
     * @var string|null
     */
    private $resolutionNamespace;

    /**
     * Whether any index or field traversals should be checked if
     * existing to prevent notices.
     *
     * @var boolean
     */
    private $preventIndexOrFieldNotices = false;

    public function __construct(IFunctionScope $scope = null, $resolutionNamespace = null)
    {
        $this->scope               = $scope;
        $this->resolutionNamespace = $resolutionNamespace;
    }

    final protected function scoped(\Closure $action)
    {
        if ($this->scope === null) {
            return $action();
        }

        $scopedAction = $action->bindTo($this->scope->getThis(), $this->scope->getScopeType());

        return $scopedAction();
    }

    final protected function tryResolveSymbolNamespace($symbolName, callable $symbolExistsFunction)
    {
        //If absolute or in global namespace, return if symbol is defined
        if (strpos($symbolName, '\\') === 0 || $this->resolutionNamespace === null) {
            return $symbolExistsFunction($symbolName) ? $symbolName : null;
        }

        $relativeName = $this->resolutionNamespace . '\\' . $symbolName;
        $globalName   = '\\' . $symbolName;

        //Prefer if the symbol is defined under the resolution namespace
        if ($symbolExistsFunction($relativeName)) {
            return $relativeName;
            //Else fallback to global
        } elseif ($symbolExistsFunction($globalName)) {
            return $globalName;
        } else {
            return null;
        }
    }

    public function walkArray(O\ArrayExpression $expression)
    {
        $itemExpressions = $this->walkAll($expression->getItems());

        $resolvedArray = [];
        foreach ($itemExpressions as $itemExpression) {
            $keyExpression   = $itemExpression->getKey();
            $valueExpression = $itemExpression->getValue();

            if (($keyExpression !== null && !($keyExpression instanceof O\ValueExpression))
                    || !($valueExpression instanceof O\ValueExpression)
            ) {
                return $expression->update($itemExpressions);
            }

            if ($keyExpression === null) {
                $resolvedArray[] = $valueExpression->getValue();
            } else {
                $resolvedArray[$keyExpression->getValue()] = $valueExpression->getValue();
            }
        }

        return O\Expression::value($resolvedArray);
    }

    public function walkBinaryOperation(O\BinaryOperationExpression $expression)
    {
        $left  = $this->walk($expression->getLeftOperand());
        $right = $this->walk($expression->getRightOperand());

        if ($left instanceof O\ValueExpression && $right instanceof O\ValueExpression) {
            return O\Expression::value(
                    O\Operators\Binary::doBinaryOperation(
                            $left->getValue(),
                            $expression->getOperator(),
                            $right->getValue()
                    )
            );
        }

        return $expression->update($left, $expression->getOperator(), $right);
    }

    public function walkUnaryOperation(O\UnaryOperationExpression $expression)
    {
        $operand = $this->walk($expression->getOperand());

        if ($operand instanceof O\ValueExpression) {
            return O\Expression::value(
                    O\Operators\Unary::doUnaryOperation(
                            $expression->getOperator(),
                            $operand->getValue()
                    )
            );
        }

        return $expression->update($expression->getOperator(), $operand);
    }

    public function walkCast(O\CastExpression $expression)
    {
        $valueExpression = $this->walk($expression->getCastValue());

        if ($valueExpression instanceof O\ValueExpression) {
            $castType = $expression->getCastType();
            $value    = $valueExpression->getValue();

            //Handle scoped __toString magic method possibility
            if (is_object($value) && $castType === O\Operators\Cast::STRING) {
                $castValue = $this->scoped(
                        function () use ($value) {
                            return (string)$value;
                        }
                );
            } else {
                $castValue = O\Operators\Cast::doCast($castType, $value);
            }

            return O\Expression::value($castValue);
        }

        return $expression->update(
                $expression->getCastType(),
                $valueExpression
        );
    }

    public function walkFunctionCall(O\FunctionCallExpression $expression)
    {
        $nameExpression      = $this->walk($expression->getName());
        $argumentExpressions = $this->walkAll($expression->getArguments());

        if ($nameExpression instanceof O\ValueExpression) {
            $name = $this->tryResolveSymbolNamespace($nameExpression->getValue(), 'function_exists');

            if ($name !== null) {
                $returnedValueExpression = $this->tryInvoke(O\Expression::value($name), $argumentExpressions);
                if ($returnedValueExpression !== null) {
                    return $returnedValueExpression;
                }
            }
        }

        return $expression->update($nameExpression, $argumentExpressions);
    }

    public function walkInvocation(O\InvocationExpression $expression)
    {
        $valueExpression     = $this->walk($expression->getValue());
        $argumentExpressions = $this->walkAll($expression->getArguments());

        $returnedValueExpression = $this->tryInvoke($valueExpression, $argumentExpressions);
        if ($returnedValueExpression !== null) {
            return $returnedValueExpression;
        }

        return $expression->update($valueExpression, $argumentExpressions);
    }

    public function walkStaticMethodCall(O\StaticMethodCallExpression $expression)
    {
        $classExpression     = $this->walk($expression->getClass());
        $nameExpression      = $this->walk($expression->getName());
        $argumentExpressions = $this->walkAll($expression->getArguments());

        $returnedValueExpression = $this->tryInvokeMethod($classExpression, $nameExpression, $argumentExpressions);
        if ($returnedValueExpression !== null) {
            return $returnedValueExpression;
        }

        return $expression->update(
                $classExpression,
                $nameExpression,
                $argumentExpressions
        );
    }

    public function walkMethodCall(O\MethodCallExpression $expression)
    {
        $valueExpression     = $this->walk($expression->getValue());
        $nameExpression      = $this->walk($expression->getName());
        $argumentExpressions = $this->walkAll($expression->getArguments());

        $returnedValueExpression = $this->tryInvokeMethod($valueExpression, $nameExpression, $argumentExpressions);
        if ($returnedValueExpression !== null) {
            return $returnedValueExpression;
        }

        return $expression->update(
                $valueExpression,
                $nameExpression,
                $argumentExpressions
        );
    }

    private function tryInvokeMethod(
            O\Expression $classExpression,
            O\Expression $nameExpression,
            array $argumentExpressions
    ) {
        if ($classExpression instanceof O\ValueExpression && $nameExpression instanceof O\ValueExpression) {
            $returnedValueExpression = $this->tryInvoke(
                    O\Expression::value([$classExpression->getValue(), $nameExpression->getValue()]),
                    $argumentExpressions
            );

            return $returnedValueExpression;
        }

        return null;
    }

    private function tryGetValues(array $expressions, &$argumentValues)
    {
        $argumentValues = [];

        foreach ($expressions as $expression) {
            if (!($expression instanceof O\ValueExpression)) {
                $argumentValues = null;
                return false;
            }

            $argumentValues[] = $expression->getValue();
        }

        return true;
    }

    private function tryInvoke(O\Expression $valueExpression, array $argumentExpressions)
    {
        if ($valueExpression instanceof O\ValueExpression
                && $this->tryGetValues($argumentExpressions, $argumentValues)
        ) {
            /** @var $value callable */
            $value = $valueExpression->getValue();

            return O\Expression::value(
                    $this->scoped(
                            function () use ($value, $argumentValues) {
                                return empty($argumentValues) ? $value() : call_user_func_array(
                                        $value,
                                        $argumentValues
                                );
                            }
                    )
            );
        }

        return null;
    }

    final protected function preventNotices($shouldPreventNotices, callable $walkScope)
    {
        $original                         = $this->preventIndexOrFieldNotices;
        $this->preventIndexOrFieldNotices = $shouldPreventNotices;
        $return                           = $walkScope();
        $this->preventIndexOrFieldNotices = $original;

        return $return;
    }

    public function walkField(O\FieldExpression $expression)
    {
        $valueExpression = $this->walk($expression->getValue());
        $nameExpression  = $this->preventNotices(
                false,
                function () use ($expression) {
                    return $this->walk($expression->getName());
                }
        );


        if ($valueExpression instanceof O\ValueExpression && $nameExpression instanceof O\ValueExpression) {
            $value = $valueExpression->getValue();
            $name  = $nameExpression->getValue();

            if ($this->preventIndexOrFieldNotices) {
                $getter = function () use ($value, $name) {
                    return isset($value->{$name}) ? $value->{$name} : null;
                };
            } else {
                $getter = function () use ($value, $name) {
                    return $value->{$name};
                };
            }

            return O\Expression::value($this->scoped($getter));
        }

        return $expression->update($valueExpression, $nameExpression);
    }

    public function walkIndex(O\IndexExpression $expression)
    {
        $valueExpression = $this->walk($expression->getValue());
        $indexExpression = $this->preventNotices(
                false,
                function () use ($expression) {
                    return $this->walk($expression->getIndex());
                }
        );

        if ($valueExpression instanceof O\ValueExpression && $indexExpression instanceof O\ValueExpression) {
            $value = $valueExpression->getValue();
            $index = $indexExpression->getValue();

            if ($this->preventIndexOrFieldNotices) {
                $indexValue = isset($value[$index]) ? $value[$index] : null;
            } else {
                $indexValue = $value[$index];
            }

            return O\Expression::value($indexValue);
        }

        return $expression->update($valueExpression, $indexExpression);
    }

    public function walkIsset(O\IssetExpression $expression)
    {
        $valueExpressions = $this->preventNotices(
                true,
                function () use ($expression) {
                    return $this->walkAll($expression->getValues());
                }
        );

        foreach ($valueExpressions as $valueExpression) {
            if ($valueExpression instanceof O\ValueExpression) {
                if ($valueExpression->getValue() === null) {
                    return O\Expression::value(false);
                }
            } else {
                return $expression->update($valueExpressions);
            }
        }

        return O\Expression::value(true);
    }

    public function walkEmpty(O\EmptyExpression $expression)
    {
        $valueExpression = $this->preventNotices(
                true,
                function () use ($expression) {
                    return $this->walk($expression->getValue());
                }
        );

        if ($valueExpression instanceof O\ValueExpression) {
            $value = $valueExpression->getValue();

            return O\Expression::value(empty($value));
        }

        return $expression->update($valueExpression);
    }

    public function walkNew(O\NewExpression $expression)
    {
        $classExpression     = $this->walk($expression->getClass());
        $argumentExpressions = $this->walkAll($expression->getArguments());

        if ($classExpression instanceof O\ValueExpression
                && $this->tryGetValues($argumentExpressions, $argumentValues)
        ) {
            $class = $classExpression->getValue();

            $reflection  = new \ReflectionClass($class);
            $constructor = $reflection->getConstructor();

            //If there is no constructor or it is publice, 
            //use reflection to construct instance normally.
            if ($constructor === null || $constructor->isPublic()) {
                $instance = $reflection->newInstanceArgs($argumentValues);
            }
            //If the constructor is not public, attempt to create a new instance 
            //and then seperately invoke the constructor under the supplied scope.
            else {
                $instance = $reflection->newInstanceWithoutConstructor();
                if ($constructor !== null) {
                    $this->scoped($constructor->getClosure($instance));
                }
            }

            return O\Expression::value($instance);
        }

        return $expression->update(
                $classExpression,
                $argumentExpressions
        );
    }

    public function walkConstant(O\ConstantExpression $expression)
    {
        $constantName = $this->tryResolveSymbolNamespace($expression->getName(), 'defined');

        if ($constantName !== null) {
            return O\Expression::value(constant($constantName));
        }

        return $expression;
    }

    protected function normalizeType($type)
    {
        if(strpos($type, '\\') === 0) {
            return substr($type, 1);
        }

        return $type;
    }

    public function walkClassConstant(O\ClassConstantExpression $expression)
    {
        $classExpression = $this->walk($expression->getClass());
        $name            = $expression->getName();

        if ($classExpression instanceof O\ValueExpression) {
            $classType = $this->normalizeType($classExpression->getValue());
            return O\Expression::value($name === 'class' ? $classType : constant($classType . '::' . $name));
        }

        return $expression->update($classExpression, $name);
    }

    public function walkStaticField(O\StaticFieldExpression $expression)
    {
        $classExpression = $this->walk($expression->getClass());
        $nameExpression  = $this->walk($expression->getName());

        if ($classExpression instanceof O\ValueExpression && $nameExpression instanceof O\ValueExpression) {
            $class = $classExpression->getValue();
            $name  = $nameExpression->getValue();

            if ($this->preventIndexOrFieldNotices) {
                $getter = function () use ($class, $name) {
                    return isset($class::$$name) ? $class::$$name : null;
                };
            } else {
                $getter = function () use ($class, $name) {
                    return $class::$$name;
                };
            }

            return O\Expression::value($this->scoped($getter));
        }

        return $expression->update(
                $classExpression,
                $nameExpression
        );
    }

    public function walkTernary(O\TernaryExpression $expression)
    {
        $conditionExpression = $this->walk($expression->getCondition());
        $ifTrueExpression    = $this->walk($expression->getIfTrue());
        $ifFalseExpression   = $this->walk($expression->getIfFalse());

        if ($conditionExpression instanceof O\ValueExpression) {
            return $conditionExpression->getValue() ? $ifTrueExpression : $ifFalseExpression;
        }

        return $expression->update(
                $conditionExpression,
                $ifTrueExpression,
                $ifFalseExpression
        );
    }

    public function walkVariable(O\VariableExpression $expression)
    {
        $nameExpression = $this->walk($expression->getName());

        if ($nameExpression instanceof O\ValueExpression) {
            if ($this->tryGetIfSuperGlobal($nameExpression->getValue(), $result)) {
                return O\Expression::value($result);
            }
        }

        return $expression->update($nameExpression);
    }

    private function tryGetIfSuperGlobal($name, &$result)
    {
        switch ($name) {
            case 'GLOBALS':
                $result = $GLOBALS;
                break;

            case '_SERVER':
                $result = $_SERVER;
                break;

            case '_ENV':
                $result = $_ENV;
                break;

            case '_REQUEST':
                $result = $_REQUEST;
                break;

            case '_GET':
                $result = $_GET;
                break;

            case '_POST':
                $result = $_POST;
                break;

            case '_COOKIE':
                $result = $_COOKIE;
                break;

            case '_FILES':
                $result = $_FILES;
                break;

            case '_SESSION':
                $result = $_SESSION;
                break;

            default:
                return false;
        }

        return true;
    }
}
