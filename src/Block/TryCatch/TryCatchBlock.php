<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class TryCatchBlock implements BodyInterface
{
    private TryBlock $tryBlock;

    /**
     * @var CatchBlock[]
     */
    private array $catchBlocks;

    private MetadataInterface $metadata;

    public function __construct(TryBlock $tryBlock, CatchBlock ...$catchBlocks)
    {
        $this->tryBlock = $tryBlock;
        $this->catchBlocks = $catchBlocks;
        $this->metadata = $this->buildMetadata();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function render(): string
    {
        $renderedCatchBlocks = [];

        foreach ($this->catchBlocks as $catchBlock) {
            $renderedCatchBlocks[] = $catchBlock->render();
        }

        return trim(sprintf(
            '%s %s',
            $this->tryBlock->render(),
            implode(' ', $renderedCatchBlocks)
        ));
    }

    private function buildMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        $metadata = $metadata->merge($this->tryBlock->getMetadata());

        foreach ($this->catchBlocks as $catchBlock) {
            $metadata = $metadata->merge($catchBlock->getMetadata());
        }

        return $metadata;
    }
}
