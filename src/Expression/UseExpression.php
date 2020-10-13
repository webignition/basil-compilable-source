<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class UseExpression extends AbstractExpression implements RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = 'use {{ class_name }}';

    private ClassName $className;

    public function __construct(ClassName $className)
    {
        parent::__construct(null);

        $this->className = $className;
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
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
