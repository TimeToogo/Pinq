<?php

use \Pinq\Expressions as O;

class Func
{
    private function __construct() {}

    public function Test()
    {
        Func::Is('Test')->Or()->GreaterThan(5);
    }
    
    final public static function Is($Value)
    {
        return new \Pinq\Parsing\FunctionExpressionTree(
                function ($I) use ($Value) { return $I === $Value; },
                ['I' => null], 
                [O\Expression::ReturnExpression(
                    O\Expression::BinaryOperation(
                            O\Expression::Variable(O\Expression::Value('I')), 
                            O\Operators\Binary::Identity, 
                            O\Expression::Value($Value)))
                ]);
    }
}
