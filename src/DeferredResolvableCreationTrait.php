<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\StubbleResolvable\ResolvableInterface;

trait DeferredResolvableCreationTrait
{
    private ?ResolvableInterface $resolvable = null;

    abstract protected function createResolvable(): ResolvableInterface;

    public function getTemplate(): string
    {
        return $this->getResolvable()->getTemplate();
    }

    /**
     * @return array<string, string|ResolvableInterface>
     */
    public function getContext(): array
    {
        return $this->getResolvable()->getContext();
    }

    private function getResolvable(): ResolvableInterface
    {
        if (null === $this->resolvable) {
            $this->resolvable = $this->createResolvable();
        }

        return $this->resolvable;
    }
}
