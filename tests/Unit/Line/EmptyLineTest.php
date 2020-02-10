<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\EmptyLine;

class EmptyLineTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $this->assertSame('', (new EmptyLine())->render());
    }
}
