<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\Stubble\VariableResolver;

trait RenderFromTemplateTrait
{
    abstract protected function getRenderTemplate(): string;

    /**
     * @return array<string, string>
     */
    abstract protected function getRenderContext(): array;

    public function render(): string
    {
        return VariableResolver::resolveTemplateAndIgnoreUnresolvedVariables(
            $this->getRenderTemplate(),
            $this->getRenderContext()
        );
    }
}
