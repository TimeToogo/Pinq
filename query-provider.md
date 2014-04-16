---
layout: default
title:  The Query Provider
---
Querying on external data sources
=================================
The `IQueryable` and `IRepository` interfaces are respective to `ITraversable` and `ICollection`. But the data source for these is not limited to an array or iterator, they are able to use any implementation of `IQueryProvider`/`IRepositoryProvider` respectively, one could query a database, XML, DOM, CSV, the possibilities are endless.

Implementing an IQueryProvider
==============================
In this exercise to understand the `IQueryProvider` and how it is to be implemented, 
we will create a basic query provider for a constant array of numbers `[1,2,3,4,5,6,7,8,9,10]` 
(This is pointless as there is `Traversable` for a PHP array but this should help to illustrate the concept).

You may need to refer to [Expressions documentation](expressions.html) if you have not done so previously.

The goal of this will be making a query provider capable of executing:

{% highlight php startinline %}
$MyQueryableArray
        ->Where(function ($Number)  { return $Number > 5; })
        ->Select(function ($Number)  { return $Number * 10; })
        ->Sum();
{% endhighlight %}

To implement a `IQueryProvider` one must understand the how the query will be represented, a query is represented in two parts:
 - **`IScope`** - This contains many `ISegment`, each segment represents one or more methods calls from the `IQueryable` implementation. For example the `->Where(...)` would become a `Filter` segment and the `->Select(...)` would becom a `Select` segment. 
 - **`IRequest`** - This represents the actual data requested, in this case `->Sum()`  so a `Sum` request, but it could be the underlying values (`AsArray()`), another aggregate (`Count()`, ...) etc.

**Steps**

 - For the first step we need the a class that will evaluate the expression tree of a given function against the array of values, this will extend the `ExpressionVisitor` class to traverse the expression tree:

{% highlight php startinline %}
use \Pinq\Expressions as O;
use \Pinq\FunctionExpressionTree;

/**
 * To keep it consice we will only implement the bare minimum needed to evaluate the requirements.
 * This is capable of evaluating a single binary operation involving > or *
 */
class ExpressionTreeEvaluator extends O\ExpressionVisitor
{
    private $Array;
    private $MappedReturnedValues;
    
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }
    
    /**
     * Nice static method to easily evaluate the return expression against the array
     */
    public static function EvaluateReturn(FunctionExpressionTree $ExpressionTree, array $Array) {
        $Evaluator = new self($Array);
        
        $Evaluator->Walk($ExpressionTree->GetFirstResolvedReturnValueExpression());
        
        return $Evaluator->MappedReturnedValues;
    }
    
    protected function VisitBinaryOperation(O\BinaryOperationExpression $Expression)
    {
        //Ignore the left operand, assume it is the value parameter
        
        $RightExpression = $Expression->GetRightOperandExpression();
        
        if(!($RightExpression instanceof O\ValueExpression)) {
            throw new \Exception('I need a constant value on the right side of the query expression');
        }
        
        $RightValue = $RightExpression->GetValue();
        
        switch ($Expression->GetOperator()) {
            case O\Operators\Binary::GreaterThan:
                $this->MappedReturnedValues = array_map(function ($I) use ($RightValue) { return $I > $RightValue; }, $this->Array);
                break;
            
            case O\Operators\Binary::Multiplication:
                $this->MappedReturnedValues = array_map(function ($I) use ($RightValue) { return $I * $RightValue; }, $this->Array);
                break;

            default:
                throw new \Exception('I cannot do this operation: ' . $Expression->GetOperator());
        }
    }
}
{% endhighlight %}

 - For the second step we need the a class that will evaluate the query scope of the supplied array, this extends the `SegmentVisitor` and will evaluate the `->Where(...)->Select(...)` part of the query:

{% highlight php startinline %}
use \Pinq\Queries\Segments;

/**
 * This is also the bare minimum.
 * Evaluating only 'Where' and 'Select' and ignoring the rest
 */
class QueryScopeEvaluator extends Segments\SegmentVisitor
{
    private $Array;
    
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }
    
    public function GetScopedArray()
    {
        return $this->Array;
    }
    
    public function VisitFilter(Segments\Filter $Query)
    {
        $MappedResults = ExpressionTreeEvaluator::EvaluateReturn($Query->GetFunctionExpressionTree(), $this->Array);
        
        foreach($MappedResults as $Key => $Value) {
            //Remove any values that returned false from the function
            if(!$Value) {
                unset($this->Array[$Key]);
            }
        }
    }
    
    public function VisitSelect(Segments\Select $Query)
    {
        $MappedResults = ExpressionTreeEvaluator::EvaluateReturn($Query->GetFunctionExpressionTree(), $this->Array);
        
        //Set the array to the projections from the function
        $this->Array = $MappedResults;
    }
}
{% endhighlight %}`

 - Now to evaluate the `->Sum()` aggregate value, extending the `RequestVisitor` class, this is responsible for evaluating all the aggregates and retrieving the values:

{% highlight php startinline %}

use \Pinq\Queries\Requests;
use \Pinq\FunctionExpressionTree;

/**
 * This is also the bare minimum, evaluating only 'Sum'
 */
class RequestEvaluator extends Requests\RequestVisitor
{
    private $Array;
    
    public function __construct(array $Array)
    {
        $this->Array = $Array;
    }
    
    public function VisitSum(Requests\Sum $Request)
    {
        if(count($this->Array) === 0) {
            return null;
        }
        
        //BONUS: if average was called with a projection function we can evaluate that to
        $ProjectedValues = $this->EvaluateProjection($Request);
        
        $Sum = 0;
        foreach($ProjectedValues as $Value) {
            $Sum += $Value;
        }
        
        return $Sum;
    }
    
    private function EvaluateProjection(Requests\ProjectionRequest $Request) 
    {
        if($Request->HasFunctionExpressionTree()) {
            return ExpressionTreeEvaluator::EvaluateReturn($Request->GetFunctionExpressionTree(), $this->Array);
        }
        else {
            return $this->Array;
        }
    }
}
{% endhighlight %}

- Bringing it all together and implementing the `IQueryProvider` by extending `QueryProvider` which implements basic boiler plate for the query provider:

{% highlight php startinline %}
use \Pinq\Providers;
use \Pinq\Queries;

class ArrayQueryProvider extends Providers\QueryProvider 
{
    private $Array = [1,2,3,4,6,7,8,9,10];
    
    protected function LoadRequestEvaluatorVisitor(Queries\IScope $Scope)
    {
        //Evaluate the query scope: ->Where(...)->Select(...)
        $ScopedEvaluator = new QueryScopeEvaluator($this->Array);
        $ScopedEvaluator->Walk($Scope);
        $ScopedArray = $ScopedEvaluator->GetScopedArray();
        
        //Return the request evaluator, it will be called and evaluate the ->Sum() query
        return new RequestEvaluator($ScopedArray);
    }
}
{% endhighlight %}

- If we wanted to we could wrap this in a nice subclass of `Queryable` and automatically pass the appropriate query provider:

{% highlight php startinline %}
class ArrayQueryable extends \Pinq\Queryable
{
    public function __construct()
    {
        parent::__construct(new ArrayQueryProvider());
    }
}
{% endhighlight %}

Finally, we can see the action:

{% highlight php startinline %}
$MyQueryableArray = new ArrayQueryable();

echo $MyQueryableArray
        ->Where(function ($Number)  { return $Number > 5; })
        ->Select(function ($Number)  { return $Number * 10; })
        ->Sum();
//Echos: 400
{% endhighlight %}

This is an extremely basic, useless and naive implementation of the query provider, but nevertheless it does exactly as we wanted. 

Hopefully, this helps to illustrate the capabilities of Pinq and its built-in expression language. Its ability and to provide a seamless integration with PHP and external data sources.