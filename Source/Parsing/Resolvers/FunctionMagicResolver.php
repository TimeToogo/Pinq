<?php

namespace Pinq\Parsing\Resolvers;

use Pinq\Expressions as O;
use Pinq\Expressions\ParameterExpression;
use Pinq\Parsing\IFunctionMagic;
use Pinq\Parsing\IMagicConstants;
use Pinq\Parsing\IMagicScopes;

/**
 * Resolves magic constants (__DIR__...) and scopes (self::...)
 * to their actual values.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionMagicResolver extends O\ExpressionWalker
{
    /**
     * @var IMagicConstants
     */
    private $magicConstants;

    /**
     * @var IMagicScopes
     */
    private $magicScopes;

    /**
     * @var int
     */
    private $closureNestingLevel = 0;

    public function __construct(IFunctionMagic $functionMagic)
    {
        $this->magicConstants = $functionMagic->getConstants();
        $this->magicScopes    = $functionMagic->getScopes();
    }

    /**
     * Resolves any magic constants / scopes with the supplied resolved values.
     *
     * @param IFunctionMagic $functionMagic
     * @param O\Expression[] $expressions
     *
     * @return O\Expression[]
     */
    public static function resolve(IFunctionMagic $functionMagic, array $expressions)
    {
        $self = new self($functionMagic);

        return $self->walkAll($expressions);
    }

    public function walkParameter(ParameterExpression $expression)
    {
        $parameter = parent::walkParameter($expression);

        return $parameter->update(
                $parameter->getName(),
                $this->resolveMagicScopeClass($expression->getTypeHint()) ?: $expression->getTypeHint(),
                $parameter->getDefaultValue(),
                $parameter->isPassedByReference(),
                $parameter->isVariadic()
        );
    }

    public function walkClosure(O\ClosureExpression $expression)
    {
        $this->closureNestingLevel++;
        $walkedClosure = parent::walkClosure($expression);
        $this->closureNestingLevel--;

        return $walkedClosure;
    }

    public function walkStaticField(O\StaticFieldExpression $expression)
    {
        return parent::walkStaticField($this->resolveMagicScopeExpression($expression));
    }

    public function walkStaticMethodCall(O\StaticMethodCallExpression $expression)
    {
        return parent::walkStaticMethodCall($this->resolveMagicScopeExpression($expression));
    }

    public function walkClassConstant(O\ClassConstantExpression $expression)
    {
        $classExpression = $expression->getClass();

        if ($classExpression instanceof O\ValueExpression && strtolower($expression->getName()) === 'class') {
            $classConstantValue = $this->resolveMagicScopeClassConstant($classExpression->getValue());

            if ($classConstantValue === null) {
                return $expression;
            }

            return O\Expression::value($classConstantValue);
        }

        return parent::walkClassConstant($this->resolveMagicScopeExpression($expression));
    }

    private function resolveMagicScopeClassConstant($class)
    {
        switch ($this->normalScopeClass($class)) {
            case 'self':
                return $this->magicScopes->getSelfClassConstant();

            case 'static':
                return $this->magicScopes->getStaticClassConstant();

            case 'parent':
                return $this->magicScopes->getParentClassConstant();
        }
    }

    private function normalScopeClass(?string $class)
    {
        return $class ? strtolower(ltrim($class, '\\')) : null;
    }

    private function resolveMagicScopeExpression(O\StaticClassExpression $expression)
    {
        $classExpression = $expression->getClass();

        if (!($classExpression instanceof O\ValueExpression)) {
            return $expression;
        }

        $classScope = $this->resolveMagicScopeClass($classExpression->getValue());

        if ($classScope === null) {
            return $expression;
        }

        return $expression->updateClass(O\Expression::value($classScope));
    }

    private function resolveMagicScopeClass($class)
    {
        switch ($this->normalScopeClass($class)) {
            case 'self':
                return $this->magicScopes->getSelfClass();

            case 'static':
                return $this->magicScopes->getStaticClass();

            case 'parent':
                return $this->magicScopes->getParentClass();
        }
    }

    public function walkConstant(O\ConstantExpression $expression)
    {
        $resolvedMagicConstant = $this->resolveMagicConstantValue($expression->getName());

        if ($resolvedMagicConstant !== null) {
            return O\Expression::value($resolvedMagicConstant);
        } else {
            return $expression;
        }
    }

    private function resolveMagicConstantValue($name)
    {
        switch (strtoupper($name)) {
            case '__DIR__':
                return $this->magicConstants->getDirectory();

            case '__FILE__':
                return $this->magicConstants->getFile();

            case '__NAMESPACE__':
                return $this->magicConstants->getNamespace();

            case '__CLASS__':
                return $this->magicConstants->getClass();

            case '__TRAIT__':
                return $this->magicConstants->getTrait();

            case '__METHOD__':
                return $this->magicConstants->getMethod($this->closureNestingLevel > 0);

            case '__FUNCTION__':
                return $this->magicConstants->getFunction($this->closureNestingLevel > 0);

            default:
                return null;
        }
    }
}
