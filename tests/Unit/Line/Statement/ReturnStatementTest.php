<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\ResolvablePlaceholder;

class ReturnStatementTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $statement = new ReturnStatement(
            ResolvablePlaceholder::createDependency('DEPENDENCY')
        );

        $this->assertSame('return {{ DEPENDENCY }};', $statement->render());
    }
}
