<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Factory;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\StaticObject;

abstract class AbstractMethodInvocationFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array<mixed>
     */
    protected function getArguments(): array
    {
        return [
            100,
            M_PI,
            'string without single quotes',
            'string with \'single\' quotes',
            true,
            false,
            new \stdClass(),
            new StaticObject('self'),
        ];
    }

    /**
     * @return ExpressionInterface[]
     */
    protected function getExpectedArguments(): array
    {
        return [
            new LiteralExpression('100'),
            new LiteralExpression((string) M_PI),
            new LiteralExpression('\'string without single quotes\''),
            new LiteralExpression('\'string with \\\'single\\\' quotes\''),
            new LiteralExpression('true'),
            new LiteralExpression('false'),
            new StaticObject('self'),
        ];
    }
}
