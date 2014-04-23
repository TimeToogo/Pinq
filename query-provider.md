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
        ->where(function ($number)  { return $number > 5; })
        ->select(function ($number)  { return $number * 10; })
        ->sum();
{% endhighlight %}

To implement a `IQueryProvider` one must understand the how the query will be represented, a query is represented in two parts:

 - **IScope** - This contains many `ISegment`, each segment represents one or more methods calls
    from the `IQueryable` implementation. For example the `->where(...)` would become a `Filter`
    segment and the proceeding `->select(...)` would become a `Select` segment. 
 
 - **IRequest** - This represents the actual data requested, in this case `->sum()`  so a `Sum`
    request, but it could be the underlying values (`->asArray()`), a different aggregate (`->count()`, ...) etc.

**Steps**

 - For the first step we need the a class that will evaluate the expression tree of a given 
   function against the array of values, this will extend the `ExpressionVisitor`
   class to traverse the expression tree:

{% highlight php startinline %}
use \Pinq\Expressions as O;
use \Pinq\FunctionExpressionTree;

/**
 * To keep it consice we will only implement the bare minimum needed to evaluate the requirements.
 * This is capable of evaluating a single binary operation involving > or *
 */
class ExpressionTreeEvaluator extends O\ExpressionVisitor
{
    private $array;
    private $mappedReturnedValues;
    
    public function __construct(array $array)
    {
        $this->array = $array;
    }
    
    /**
     * Nice static method to easily evaluate the return expression against the array
     */
    public static function evaluateReturn(FunctionExpressionTree $expressionTree, array $array) {
        $evaluator = new self($array);
        
        $evaluator->Walk($expressionTree->getFirstResolvedReturnValueExpression());
        
        return $evaluator->mappedReturnedValues;
    }
    
    protected function visitBinaryOperation(O\BinaryOperationExpression $expression)
    {
        //Ignore the left operand, assume it is the value parameter for simplicity
        
        $rightExpression = $expression->getRightOperandExpression();
        
        if(!($rightExpression instanceof O\ValueExpression)) {
            throw new \Exception('I need a constant value on the right side of the query expression');
        }
        
        $rightValue = $rightExpression->getValue();
        
        switch ($expression->getOperator()) {
            case O\Operators\Binary::GREATER_THAN:
                $this->mappedReturnedValues = array_map(function ($i) use ($rightValue) { return $i > $rightValue; }, $this->array);
                break;
            
            case O\Operators\Binary::MULTIPLICATION:
                $this->mappedReturnedValues = array_map(function ($i) use ($rightValue) { return $i * $rightValue; }, $this->array);
                break;

            default:
                throw new \Exception('I cannot do this operation: ' . $expression->getOperator());
        }
    }
}
{% endhighlight %}

 - For the second step we need the a class that will evaluate the query scope of the 
   supplied array, this extends the `SegmentVisitor` and will evaluate the 
   `->where(...)->select(...)` part of the query:

{% highlight php startinline %}
use \Pinq\Queries\Segments;

/**
 * This is also the bare minimum.
 * Evaluating only 'where' and 'select' and ignoring the rest
 */
class QueryScopeEvaluator extends Segments\SegmentVisitor
{
    private $array;
    
    public function __construct(array $array)
    {
        $this->array = $array;
    }
    
    public function getScopedArray()
    {
        return $this->array;
    }
    
    public function VisitFilter(Segments\Filter $query)
    {
        $mappedResults = ExpressionTreeEvaluator::EvaluateReturn($query->getFunctionExpressionTree(), $this->array);
        
        foreach($mappedResults as $Key => $Value) {
            //Remove any values that returned false from the function
            if(!$Value) {
                unset($this->array[$Key]);
            }
        }
    }
    
    public function VisitSelect(Segments\Select $query)
    {
        $mappedResults = ExpressionTreeEvaluator::EvaluateReturn($query->getFunctionExpressionTree(), $this->array);
        
        //Set the array to the projections from the function
        $this->array = $mappedResults;
    }
}
{% endhighlight %}`

 - Now to evaluate the `->sum()` aggregate value, extending the `RequestVisitor` class, 
   this is responsible for evaluating all the aggregates and retrieving the values:

{% highlight php startinline %}

use \Pinq\Queries\Requests;
use \Pinq\FunctionExpressionTree;

/**
 * This is also the bare minimum, evaluating only 'sum'
 */
class RequestEvaluator extends Requests\RequestVisitor
{
    private $array;
    
    public function __construct(array $array)
    {
        $this->array = $array;
    }
    
    public function visitSum(Requests\Sum $request)
    {
        if(count($this->array) === 0) {
            return null;
        }
        
        //BONUS: if average was called with a projection function we can evaluate that to
        $projectedValues = $this->evaluateProjection($request);
        
        $sum = 0;
        foreach($projectedValues as $value) {
            $sum += $value;
        }
        
        return $sum;
    }
    
    private function evaluateProjection(Requests\ProjectionRequest $request) 
    {
        if($request->hasFunctionExpressionTree()) {
            return ExpressionTreeEvaluator::evaluateReturn($request->getFunctionExpressionTree(), $this->array);
        }
        else {
            return $this->array;
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
    
    protected function loadRequestEvaluatorVisitor(Queries\IScope $scope)
    {
        //Evaluate the query scope: ->where(...)->select(...)
        $scopedEvaluator = new QueryScopeEvaluator($this->array);
        $scopedEvaluator->walk($scope);
        $scopedArray = $scopedEvaluator->getScopedArray();
        
        //Return the request evaluator, it will be called and evaluate the ->sum() query
        return new RequestEvaluator($scopedArray);
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
        ->where(function ($number)  { return $number > 5; })
        ->select(function ($number)  { return $number * 10; })
        ->sum();
//Echos: 400
{% endhighlight %}

This is an extremely basic, useless and naive implementation of the query provider, 
but nevertheless it does exactly as we wanted. 

Hopefully, this helps to illustrate the capabilities of Pinq and its built-in expression language and query infrastructure. 
The ability and to provide a seamless integration with PHP and external data sources.