<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\SingleLineComment;

class SingleLineCommentTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $content = 'comment content';
        $comment = new \webignition\BasilCompilableSource\SingleLineComment($content);

        $this->assertSame($content, $comment->getContent());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(\webignition\BasilCompilableSource\SingleLineComment $comment, string $expectedString)
    {
        $this->assertSame($expectedString, $comment->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'comment' => new \webignition\BasilCompilableSource\SingleLineComment(''),
                'expectedString' => '// ',
            ],
            'non-empty' => [
                'comment' => new \webignition\BasilCompilableSource\SingleLineComment('non-empty'),
                'expectedString' => '// non-empty',
            ],
        ];
    }
}
