<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\SingleLineComment;
use webignition\BasilCompilableSource\Tests\Services\ObjectReflector;

class SingleLineCommentTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $content = 'comment content';
        $comment = new SingleLineComment($content);

        $this->assertSame($content, ObjectReflector::getProperty($comment, 'content'));
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(SingleLineComment $comment, string $expectedString)
    {
        $this->assertSame($expectedString, $comment->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'comment' => new SingleLineComment(''),
                'expectedString' => '// ',
            ],
            'non-empty' => [
                'comment' => new SingleLineComment('non-empty'),
                'expectedString' => '// non-empty',
            ],
        ];
    }
}
