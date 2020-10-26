<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\Stubble\DeciderFactory;
use webignition\Stubble\UnresolvedVariableFinder;
use webignition\Stubble\VariableResolver;
use webignition\StubbleResolvable\ResolvableInterface;

trait RenderTrait
{
    public function render(): string
    {
        if ($this instanceof ResolvableInterface) {
            $resolver = new VariableResolver(
                new UnresolvedVariableFinder([
                    DeciderFactory::createAllowAllDecider()
                ])
            );

            return $resolver->resolveAndIgnoreUnresolvedVariables($this);
        }

        return (string) $this;
    }
}
