<?php

namespace Pinq\Tests\Integration\Providers\DSL\Implementation\English;

use Pinq\Providers\DSL\Compilation;
use Pinq\Queries;
use Pinq\Queries\Requests;
use Pinq\Queries\Segments;

class ScopeCompiler extends Compilation\ScopeCompiler
{
    /**
     * @var CompiledQuery
     */
    protected $compilation;

    public function createCompiledScopeQuery(Queries\IScope $scope, Queries\IResolvedParameterRegistry $structuralParameters)
    {
        $query = new CompiledQuery();
        $this->compileScope($query, $scope, $structuralParameters);

        return $query;
    }

    public function visitIndexBy(Segments\IndexBy $query)
    {
        $this->compilation->append('Index according to: ');
        $this->compilation->appendFunction($query->getProjectionFunction());
        $this->compilation->appendLine();
    }

    public function visitSelect(Segments\Select $query)
    {
        $this->compilation->append('Map according to: ');
        $this->compilation->appendFunction($query->getProjectionFunction());
        $this->compilation->appendLine();
    }

    public function visitKeys(Segments\Keys $query)
    {
        $this->compilation->appendLine('Use keys');
    }

    public function visitReindex(Segments\Reindex $query)
    {
        $this->compilation->appendLine('Reindex keys');
    }

    public function visitOperation(Segments\Operation $query)
    {
        $textMap = [
                Segments\Operation::APPEND     => 'Append with: ',
                Segments\Operation::DIFFERENCE => 'The difference from: ',
                Segments\Operation::EXCEPT     => 'Where not contained in: ',
                Segments\Operation::INTERSECT  => 'The intersection with: ',
                Segments\Operation::UNION      => 'The union with: ',
                Segments\Operation::WHERE_IN   => 'Where contained in: ',
        ];

        $this->compilation->append($textMap[$query->getOperationType()]);
        $this->compilation->appendSource($this, $this->parameters, $query->getSource());
        $this->compilation->appendLine();
    }

    public function visitRange(Segments\Range $query)
    {
        $this->compilation->appendLine('Starting from and up to the specified element');
    }

    public function visitOrderBy(Segments\OrderBy $query)
    {
        $this->compilation->append('Order according to: ');

        $first = true;
        foreach ($query->getOrderings() as $ordering) {
            if ($first === true) {
                $first = false;
            } else {
                $this->compilation->append(', ');
            }

            $this->compilation->appendFunction($ordering->getProjectionFunction());
            $this->compilation->append(' asc or desc');
        }
        $this->compilation->appendLine();
    }

    public function visitGroupBy(Segments\GroupBy $query)
    {
        $this->compilation->append('Group according to: ');
        $this->compilation->appendFunction($query->getProjectionFunction());
        $this->compilation->appendLine();
    }

    public function visitSelectMany(Segments\SelectMany $query)
    {
        $this->compilation->append('Map and flatten according to: ');
        $this->compilation->appendFunction($query->getProjectionFunction());
        $this->compilation->appendLine();
    }

    public function visitFilter(Segments\Filter $query)
    {
        $this->compilation->append('Filter according to: ');
        $this->compilation->appendFunction($query->getProjectionFunction());
        $this->compilation->appendLine();
    }

    public function visitUnique(Segments\Unique $query)
    {
        $this->compilation->appendLine('Only unique values');
    }

    public function visitJoin(Segments\Join $query)
    {
        $this->compilation->append('Join with: ');
        $this->compilation->appendJoinOptions($this, $this->parameters, $query->getOptions());

        $this->compilation->append(' and correlate the values according to: ');
        $this->compilation->appendFunction($query->getJoiningFunction());
        $this->compilation->appendLine();
    }
}
