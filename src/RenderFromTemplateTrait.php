<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\Stubble\VariableResolver;

trait RenderFromTemplateTrait
{
    public function render(): string
    {
        if ($this instanceof RenderableInterface) {
            $renderSource = $this->getRenderSource();

            return VariableResolver::resolveTemplateAndIgnoreUnresolvedVariables(
                $renderSource->getTemplate(),
                $renderSource->getContext()
            );
        }

        return VariableResolver::resolveTemplateAndIgnoreUnresolvedVariables(
            $this->getRenderTemplate(),
            $this->getRenderContext()
        );
    }
}
