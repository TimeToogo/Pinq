<?php

namespace Pinq\Parsing;

/**
 * Implementation of the function reflection interface.
 *
 * @author Elliot Levin <elliotlevin@hotmail.com>
 */
class FunctionReflection extends LocatedFunction implements IFunctionReflection
{
    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var \ReflectionFunctionAbstract
     */
    protected $innerReflection;

    /**
     * @var IFunctionScope
     */
    protected $scope;

    /**
     * @var string
     */
    protected $globalHash;

    public function __construct(
            callable $callable,
            \ReflectionFunctionAbstract $innerReflection,
            IFunctionSignature $signature,
            IFunctionLocation $location,
            IFunctionScope $scope
    ) {
        parent::__construct($signature, $location);
        $this->callable        = $callable;
        $this->innerReflection = $innerReflection;
        $this->scope           = $scope;

        //Hashes the signature and location along with the scoped class type due to the
        //resolution of scoped class constants (self::, static::, parent::).
        //These should be fully qualified in the expression tree hence requiring
        //a different hash.
        $this->globalHash = md5(
                implode(
                        '!',
                        [
                                $this->scope->getThisType(),
                                $this->locationAndSignatureHash,
                        ]
                )
        );
    }

    /**
     * Creates a new function reflection instance from the supplied callable.
     *
     * @param callable $callable
     *
     * @return self
     */
    public static function fromCallable(callable $callable)
    {
        $reflection = Reflection::fromCallable($callable);

        return new self(
                $callable,
                $reflection,
                FunctionSignature::fromReflection($reflection),
                FunctionLocation::fromReflection($reflection),
                FunctionScope::fromReflection($reflection, $callable));
    }

    public function resolveMagic(IFunctionDeclaration $declaration)
    {
        $magicConstants = $this->resolveMagicConstants($declaration);
        $magicScopes    = $this->resolveMagicScopes($declaration);

        return new FunctionMagic($magicConstants, $magicScopes);
    }

    private function resolveMagicConstants(IFunctionDeclaration $declaration)
    {
        $reflection      = $this->innerReflection;
        $__FILE__        = $this->location->getFilePath();
        $__DIR__         = dirname($__FILE__);
        $__NAMESPACE__   = $declaration->getNamespace() ?: '';
        $__TRAIT__       = '';
        $namespacePrefix = $__NAMESPACE__ === '' ? '' : $__NAMESPACE__ . '\\';

        if ($declaration->isWithinClass()) {
            $__CLASS__       = $namespacePrefix . $declaration->getClass();
            $declarationType = $__CLASS__;
        } elseif ($declaration->isWithinTrait()) {
            $__TRAIT__       = $namespacePrefix . $declaration->getTrait();
            $declarationType = $__TRAIT__;

            //If the function is method declared within a trait the __CLASS__ constant
            //is programmed to be the class in which the trait is used in: https://bugs.php.net/bug.php?id=55214&edit=1
            //ReflectionMethod::getDeclaringClass() will resolve this.
            if ($reflection instanceof \ReflectionMethod) {
                $__CLASS__ = $reflection->getDeclaringClass()->getName();
            }
            //Else the function must be a closure declared in a trait, __CLASS__ will resolve to
            //the scoped class e.g. get_called_class().
            else {
                $__CLASS__ = $this->scope->getScopeType() ?: '';
            }
        } else {
            $__CLASS__       = '';
            $__TRAIT__       = '';
            $declarationType = null;
        }

        $__FUNCTION__              = $reflection->getName();
        $__FUNCTION__WithinClosure = $namespacePrefix . '{closure}';
        if ($declarationType === null) {
            $__METHOD__              = $__FUNCTION__;
            $__METHOD__WithinClosure = $__FUNCTION__WithinClosure;
        } //__METHOD__ always uses declaration type
        else {
            $__METHOD__              = $declarationType . '::' . $__FUNCTION__;
            $__METHOD__WithinClosure = $declarationType . '::' . $__FUNCTION__WithinClosure;
        }

        return new MagicConstants(
                $__DIR__,
                $__FILE__,
                $__NAMESPACE__,
                $__CLASS__,
                $__TRAIT__,
                $__FUNCTION__,
                $__FUNCTION__WithinClosure,
                $__METHOD__,
                $__METHOD__WithinClosure);
    }

    private function resolveMagicScopes(IFunctionDeclaration $declaration)
    {
        $selfType        = $this->scope->getScopeType();
        $declarationType = $declaration->getClass() ?: $declaration->getTrait();
        $selfConstant    = $declarationType !== null ?
                ($declaration->getNamespace() !== null ? $declaration->getNamespace() . '\\' : '') . $declarationType : null;
        $staticType      = $this->scope->getThisType() ? $this->scope->getThisType() : $selfType;
        $staticConstant  = $staticType;
        $parentType      = $selfType ? (get_parent_class($selfType) ?: null) : null;
        $parentConstant  = $parentType;

        return new MagicScopes(
                $this->fullyQualify($selfType),     //self::
                $selfConstant,                      //self::class
                $this->fullyQualify($staticType),   //static::
                $staticConstant,                    //static::class
                $this->fullyQualify($parentType),   //parent::
                $parentConstant);                   //parent::class
    }

    protected function fullyQualify($type)
    {
        return $type && $type[0] !== '\\' ? '\\' . $type : $type;
    }

    public function getCallable()
    {
        return $this->callable;
    }

    public function getInnerReflection()
    {
        return $this->innerReflection;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getGlobalHash()
    {
        return $this->globalHash;
    }

    public function asEvaluationContext(array $variableTable = [])
    {
        return $this->scope->asEvaluationContext($variableTable, $this->location->getNamespace());
    }
}
