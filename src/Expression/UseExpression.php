<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\ClassName;

class UseExpression extends AbstractExpression
{
    private const RENDER_PATTERN = 'use %s';

    private ClassName $className;

    public function __construct(ClassName $className)
    {
        parent::__construct(null);

        $this->className = $className;
    }

    public function render(): string
    {
        $content = $this->className->getClassName();
        $alias = $this->className->getAlias();

        if (is_string($alias)) {
            $content .= ' as ' . $alias;
        }

        return sprintf(self::RENDER_PATTERN, $content);
    }
}
