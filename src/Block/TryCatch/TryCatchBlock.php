<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block\TryCatch;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class TryCatchBlock implements BodyInterface, ResolvableProviderInterface
{
    use RenderTrait;

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

    public function getResolvable(): ResolvableInterface
    {
        $resolvableItems = [
            $this->tryBlock,
        ];

        foreach ($this->catchBlocks as $catchBlock) {
            $resolvableItems[] = new ResolvedTemplateMutatorResolvable(
                $catchBlock,
                function (string $resolvedTemplate) {
                    return $this->catchBlockResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return ResolvableCollection::create($resolvableItems);
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

    private function catchBlockResolvedTemplateMutator(string $resolvedTemplate): string
    {
        return ' ' . $resolvedTemplate;
    }
}
