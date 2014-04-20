---
layout: default
title:  Expressions
---

What are expressions?
=====================

*You should only need to read this if you are thinking about [implementing an `IQueryProvider`](query-provider.html)*

An expression represents a piece of code. 

For example `5 + 3 - 2`, this can be represented as the following:

```
          BinaryOperation
          |           |           |
        Left   Operator   Right
         /            |             \
        5            +          BinaryOperation
                                   |           |           |
                                 Left   Operator  Right
                                 /             |              \
                                3             -                2
```

This is a pretty trivial example, but all your PHP code could be represented
as one huge expression tree.

Supported Expressions:
======================

 - **`ArrayExpression`**
    - `[1, 4, 5 => 4]`
    - `array('foo', 'bar', 'baz')`

 - **`AssignmentExpression`**
    - `$I = 5`
    - `$I += 5`
    - `$I /= 5`...

 - **`BinaryOperationExpression`** 
    - `3 + 5`
    - `3 - 5`
    - `'foo' . 'bar'`...

 - **`CastExpression`** 
    - `(int)$I`
    - `(string)$I`...

 - **`ClosureExpression`** 
    - `function ($I) {...}`

 - **`EmptyExpression`** 
    - `empty($I)`

 - **`FieldExpression`** 
    - `$I->Field`

 - **`FunctionCallExpression`** 
    - `strlen($I)`

 - **`IndexExpression`** 
    - `$I[3]`

 - **`InvocationExpression`** 
    - `$I()`

 - **`IssetExpression`** 
    - `isset($I)`
 - **`MethodCallExpression`** 
    - `$I->Method()`

 - **`NewExpression`** 
    - `new \stdClass()`
    - `new \DateTime()`

 - **`ParameterExpression`**
    - `function ($I)`
    - `function (\stdClass &$I = null)`

 - **`ReturnExpression`** 
    - `return $I`

 - **`StaticMethodCallExpression`** 
    - `Object::Method()`

 - **`SubQueryExpression`**
    - `$Traversable->Where(function ($I) { return $I > 5; })->Average()`

 - **`TernaryExpression`** 
    - `$I === true ? 1 : -1`

 - **`ThrowExpression`** 
    - `throw $I`

 - **`UnaryOperationExpression`** 
    - `-$I`
    - `$I++`
    - `!$I`
    - `--$I`
    - `~$I`...

 - **`ValueExpression`** 
    - `4`
    - `5.5`
    - `'test'`
    - `null`
    - `true`...

 - **`VariableExpression`** 
    - `$I`

The Expression class
====================
This class is the base for all expression classes. All expressions provide the following API:

 - `Simplify` - Simplifies the expression if possible
 - `Traverse` - Passes itself to the respective method on the supplied `ExpressionWalker`
 - `Compile` - Compiles to a string of valid PHP code
 - `__clone` - Expressions support `clone` and should create a deep clone of the entire expression tree

It also contains a set of static factory methods for all the concrete expression types.


Representing a function
=======================

When querying an implementation of `IQueryable` or `IRepository`, all supplied functions will be
converted to instances of the `FunctionExpressionTree` class. This class hold information about the
parameters and body of the function. It has the following API:

 - `GetCompiledFunction` - Gets the compiled function, this can be invoked just as any other function
 - `GetExpressions` - Returns the body expressions of the function
 - `GetFirstResolvedReturnValueExpression` - Returns the first return value expression, it resolves all variables in the expression tree to that containing only the parameters and constant values
 - `GetParameterExpressions` - Returns the parameter expressions
 - `Simplify` - Simplify all that can be simplified, `3 + 5` -> `8`
 - `HasUnresolvedVariables` - If the function contains variables that are not defined
 - `GetUnresolvedVariables` - Returns the names of all unresolved variables
 - `ResolveVariables` - Resolves the variables by name to their respective values
 - `Walk` - Walk the body expressions with the supplied `ExpressionWalker`

Interpreting an expression tree
===============================

To interprete an expression tree, the are is a class, `ExpressionVisitor`, this extends the 
`ExpressionWalker`, this class contains a set of overrides methods, one for each type of expression.
This is designed to traverse an expression tree, visiting and handling every type of expression.
Extending this class provides a versatile way to interprete an expression tree, this will become 
necessary, when [implementing an `IQueryProvider`](query-provider.html).
