<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

class UseExpression extends AbstractExpression implements ResolvableProviderInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = 'use {{ class_name }}';

    private ClassName $className;

    public function __construct(ClassName $className)
    {
        parent::__construct(null);

        $this->className = $className;
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'class_name' => $this->renderClassName(),
            ]
        );
    }

    private function renderClassName(): string
    {
        $content = $this->className->getClassName();
        $alias = $this->className->getAlias();

        if (is_string($alias)) {
            $content .= ' as ' . $alias;
        }

        return $content;
    }
}
