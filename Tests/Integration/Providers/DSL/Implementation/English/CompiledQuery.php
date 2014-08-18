<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Expressions\Expression;
use Pinq\Providers\DSL\Compilation;
use Pinq\Queries\Requests;
use Pinq\Queries;
use Symfony\Component\Config\Definition\Exception\Exception;

class CompiledQuery implements Compilation\ICompiledRequest, Compilation\ICompiledOperation
{
    /**
     * @var string
     */
    private $english = '';

    /**
     * @param string $english
     *
     * @return void
     */
    public function append($english)
    {
        $this->english .= $english;
    }

    /**
     * @param string $english
     *
     * @return void
     */
    public function appendLine($english = '')
    {
        $this->english .= $english . PHP_EOL;
    }

    public function appendFunction(Queries\Functions\FunctionBase $function)
    {
        if ($function->isInternal()) {
            throw new Exception('Internal functions are not supported');
        }

        $this->append('{');
        if ($function->countBodyExpressions() === 0) {
            $this->append('}');
        } elseif ($function->countBodyExpressions() === 1) {
            $this->append(' ');
            $this->append($function->getBodyExpressions()[0]->compile() . ';');
            $this->append(' }');
        } else {
            $this->appendLine();
            $this->append(
                    implode(';' . PHP_EOL, Expression::compileAll($function->getBodyExpressions())) . ';'
            );
            $this->appendLine();
            $this->append('}');
        }

        $parameterMap = $function->getParameterScopedVariableMap();
        if (!empty($parameterMap)) {
            $this->append(' with parameters: [');
            $parameters = [];
            foreach ($parameterMap as $variableName) {
                $parameters[] = "$$variableName";
            }
            $this->append(implode(', ', $parameters));
            $this->append(']');
        }
    }

    public function appendSource(
            ScopeCompiler $scopeCompiler,
            Queries\IResolvedParameterRegistry $parameters,
            Queries\Common\ISource $source
    ) {
        if ($source instanceof Queries\Common\Source\ArrayOrIterator) {
            $this->append('[array or iterator]');
        } elseif ($source instanceof Queries\Common\Source\SingleValue) {
            $this->append('[single value]');
        }elseif ($source instanceof Queries\Common\Source\QueryScope) {
            $compilation = $scopeCompiler->createCompiledScopeQuery($source->getScope(), $parameters);

            $this->appendLine('[');
            foreach (array_filter(explode(PHP_EOL, $compilation)) as $line) {
                $this->appendLine('    ' . $line);
            }
            $this->append(']');
        }
    }

    public function appendJoinOptions(
            ScopeCompiler $scopeCompiler,
            Queries\IResolvedParameterRegistry $parameters,
            Queries\Common\Join\Options $joinOptions
    ) {
        $this->appendSource($scopeCompiler, $parameters, $joinOptions->getSource());

        if($joinOptions->isGroupJoin()) {
            $this->append(' into groups');
        }

        if ($joinOptions->hasFilter()) {
            $this->append(' filtered according to: ');
            $filter = $joinOptions->getFilter();
            if ($filter instanceof Queries\Common\Join\Filter\Custom) {
                $this->appendFunction($filter->getOnFunction());
            } elseif ($filter instanceof Queries\Common\Join\Filter\Equality) {
                $this->appendFunction($filter->getOuterKeyFunction());
                $this->append(' equaling ');
                $this->appendFunction($filter->getInnerKeyFunction());
            }
        }

        if($joinOptions->hasDefault()) {
            $this->append(' with default values');
        }
    }

    public function appendOtherQuery(CompiledQuery $compilation)
    {
        $this->appendLine('[');
        foreach (explode(PHP_EOL, $compilation) as $line) {
            $this->appendLine('    ' . $line);
        }
        $this->append(']');
    }

    /**
     * @return string
     */
    public function getEnglish()
    {
        return $this->english;
    }

    public function __toString()
    {
        return $this->english;
    }
}
