<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\VariableDependency;

class ReturnStatementTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $statement = new ReturnStatement(
            new VariableDependency('DEPENDENCY')
        );

        $this->assertSame('return {{ DEPENDENCY }};', $statement->render());
    }
}
