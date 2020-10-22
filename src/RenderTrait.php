<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\Stubble\DeciderFactory;
use webignition\Stubble\UnresolvedVariableFinder;
use webignition\Stubble\VariableResolver;
use webignition\StubbleResolvable\ResolvableProviderInterface;

trait RenderTrait
{
    public function render(): string
    {
        if ($this instanceof ResolvableProviderInterface) {
            $resolvable = $this->getResolvable();

            $resolver = new VariableResolver(
                new UnresolvedVariableFinder([
                    DeciderFactory::createAllowAllDecider()
                ])
            );

            return $resolver->resolveAndIgnoreUnresolvedVariables($resolvable);
        }

        return (string) $this;
    }
}
