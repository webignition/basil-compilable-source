<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\Stubble\DeciderFactory;
use webignition\Stubble\UnresolvedVariableFinder;
use webignition\Stubble\VariableResolver;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

trait RenderTrait
{
    public function render(): string
    {
        if ($this instanceof ResolvableInterface || $this instanceof ResolvableProviderInterface) {
            $resolver = new VariableResolver(
                new UnresolvedVariableFinder([
                    DeciderFactory::createAllowAllDecider()
                ])
            );

            if ($this instanceof ResolvableInterface) {
                return $resolver->resolveAndIgnoreUnresolvedVariables($this);
            }

            if ($this instanceof ResolvableProviderInterface) {
                return $resolver->resolveAndIgnoreUnresolvedVariables($this->getResolvable());
            }
        }

        return (string) $this;
    }
}
