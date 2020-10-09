<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class UseExpression extends AbstractExpression
{
    use RenderFromTemplateTrait;

    private const RENDER_PATTERN = 'use {{ class_name }}';

    private ClassName $className;

    public function __construct(ClassName $className)
    {
        parent::__construct(null);

        $this->className = $className;
    }

    protected function getRenderTemplate(): string
    {
        return self::RENDER_PATTERN;
    }

    protected function getRenderContext(): array
    {
        return [
            'class_name' => $this->renderClassName(),
        ];
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
