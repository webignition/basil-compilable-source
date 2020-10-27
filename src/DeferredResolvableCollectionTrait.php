<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\StubbleResolvable\ResolvableCollectionInterface;

trait DeferredResolvableCollectionTrait
{
    use DeferredResolvableCreationTrait;

    public function count(): int
    {
        $resolvable = $this->getResolvable();

        return $resolvable instanceof ResolvableCollectionInterface
            ? count($resolvable)
            : 1;
    }

    public function getIndexForItem($item): ?int
    {
        $resolvable = $this->getResolvable();

        return $resolvable instanceof ResolvableCollectionInterface
            ? $resolvable->getIndexForItem($item)
            : null;
    }
}
