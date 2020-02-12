<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\VariablePlaceholder;

class ReturnStatementTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $statement = new ReturnStatement(
            VariablePlaceholder::createDependency('DEPENDENCY')
        );

        $this->assertSame('return {{ DEPENDENCY }};', $statement->render());
    }
}
