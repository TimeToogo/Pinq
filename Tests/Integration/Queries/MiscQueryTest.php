<?php

namespace Pinq\Tests\Integration\Queries;

use Pinq\Expressions as O;
use Pinq\Parsing;
use Pinq\Providers;
use Pinq\Queries as Q;
use Pinq\Tests\PinqTestCase;

class MiscQueryTest extends PinqTestCase
{
    protected function request()
    {
        return new Q\RequestQuery(new Q\Scope(new Q\SourceInfo(''), []), new Q\Requests\Values(Q\Requests\Values::AS_ARRAY));
    }

    protected function operation()
    {
        return new Q\OperationQuery(new Q\Scope(new Q\SourceInfo(''), []), new Q\Operations\Clear());
    }

    public function queries()
    {
        return [
                [$this->request()],
                [$this->operation()],
        ];
    }

    /**
     * @dataProvider queries
     */
    public function testUpdateScope(Q\IQuery $query)
    {
        $newScope = new Q\Scope(new Q\SourceInfo(''), []);

        $this->assertSame($query->updateScope($query->getScope()), $query);
        $this->assertSame($query->updateScope($newScope)->getScope(), $newScope);
    }

    public function testUpdateRequest()
    {
        $requestQuery = $this->request();
        $newRequest = new Q\Requests\First();

        $this->assertSame($requestQuery->updateRequest($requestQuery->getRequest()), $requestQuery);
        $this->assertSame($requestQuery->updateRequest($newRequest)->getRequest(), $newRequest);
    }

    public function testUpdateOperation()
    {
        $operationQuery = $this->operation();
        $newOperation = new Q\Operations\Clear();

        $this->assertSame($operationQuery->updateOperation($operationQuery->getOperation()), $operationQuery);
        $this->assertSame($operationQuery->updateOperation($newOperation)->getOperation(), $newOperation);
    }
}
