<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\StubbleResolvable\ResolvableInterface;

trait DeferredResolvableCreationTrait
{
    private ?ResolvableInterface $resolvable = null;

    public function getTemplate(): string
    {
        return $this->getResolvable()->getTemplate();
    }

    /**
     * @return array<string, ResolvableInterface|string>
     */
    public function getContext(): array
    {
        return $this->getResolvable()->getContext();
    }

    abstract protected function createResolvable(): ResolvableInterface;

    private function getResolvable(): ResolvableInterface
    {
        if (null === $this->resolvable) {
            $this->resolvable = $this->createResolvable();
        }

        return $this->resolvable;
    }
}
