<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\EmptyLine;

class EmptyLineTest extends AbstractResolvableTest
{
    public function testRender()
    {
        $this->assertRenderResolvable('', new EmptyLine());
    }
}
