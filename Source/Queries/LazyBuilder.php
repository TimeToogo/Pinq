<?php

namespace Pinq\Queries;

class LazyBuilder implements \Pinq\IQueryBuilder
{
    /**
     * @var \Pinq\IQueryBuilder
     */
    protected $Builder;

    /**
     * @var array
     */
    protected $DefferedExecutionQueue = [];

    public function __construct(\Pinq\IQueryBuilder $Builder)
    {
        $this->Builder = $Builder;
    }

    final public function ClearStream()
    {
        $this->DefferedExecutionQueue = [];
        $this->Builder->ClearStream();
    }

    final public function IsEmpty()
    {
        return $this->Builder->IsEmpty() && count($this->DefferedExecutionQueue) === 0;
    }

    final public function GetStream()
    {
        $this->ExecuteDeferedMethods();
        $this->DefferedExecutionQueue = [];

        return $this->Builder->GetStream();
    }

    private function ExecuteDeferedMethods()
    {
        foreach ($this->DefferedExecutionQueue as $MethodNameArguments) {
            $MethodName = array_pop($MethodNameArguments);

            call_user_func_array([$this->Builder, $MethodName], $MethodNameArguments);
        }
    }

    public function Where(callable $Predicate)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Predicate];
    }

    public function Limit($Amount)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Amount];
    }

    public function Skip($Amount)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Amount];
    }

    public function Slice($Start, $Amount)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Start, $Amount];
    }

    public function Select(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function SelectMany(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function GroupBy(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function Append(\Pinq\IQueryable $Query)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Query];
    }

    public function Union(\Pinq\IQueryable $Query)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Query];
    }

    public function Except(\Pinq\IQueryable $Query)
    {
        $this->DefferedExecutionQueue[] = [$Query];
    }

    public function Intersect(\Pinq\IQueryable $Query)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Query];
    }

    public function OrderBy(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function OrderByDescending(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function ThenOrderBy(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function ThenOrderByDescending(callable $Function)
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__, $Function];
    }

    public function Unique()
    {
        $this->DefferedExecutionQueue[] = [__FUNCTION__];
    }

}
