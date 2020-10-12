<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Statement;

use webignition\BasilCompilableSource\Statement\ReturnStatement;
use webignition\BasilCompilableSource\VariableDependency;

class ReturnStatementTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $statement = ReturnStatement::createFromExpression(
            new VariableDependency('DEPENDENCY')
        );

        $this->assertSame('return {{ DEPENDENCY }};', $statement->render());
    }
}
