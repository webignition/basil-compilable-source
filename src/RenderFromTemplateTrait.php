<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\Stubble\VariableResolver;

trait RenderFromTemplateTrait
{
    public function render(): string
    {
        if ($this instanceof RenderableInterface) {
            $source = $this->getRenderSource();

            return VariableResolver::resolveTemplateAndIgnoreUnresolvedVariables(
                $source->getTemplate(),
                $source->getContext()
            );
        }

        return '';
    }
}
