<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\DocBlock;

use webignition\BasilCompilableSource\SourceInterface;
use webignition\BasilCompilableSource\VariableName;

class ParameterAnnotation implements SourceInterface
{
    private const RENDER_TEMPLATE = '@param %s %s';

    private string $type;
    private VariableName $name;

    public function __construct(string $type, VariableName $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function render(): string
    {
        return sprintf(self::RENDER_TEMPLATE, $this->type, $this->name->render());
    }
}
