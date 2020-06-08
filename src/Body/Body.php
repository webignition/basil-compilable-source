<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Body;

use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class Body implements BodyInterface
{
    /**
     * @var BodyContentInterface[]
     */
    private array $content;

    private MetadataInterface $metadata;

    /**
     * @param BodyContentInterface[] $content
     */
    public function __construct(array $content)
    {
        $this->content = $this->filterContent($content);
        $this->metadata = $this->buildMetadata();
    }

    public static function createEnclosingBody(BodyInterface $body): self
    {
        return new Body([
            new Statement(
                new ClosureExpression($body)
            ),
        ]);
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function render(): string
    {
        $renderedContent = [];

        foreach ($this->content as $item) {
            $renderedContent[] = $item->render();
        }

        return implode("\n", $renderedContent);
    }

    /**
     * @param BodyContentInterface[] $content
     *
     * @return BodyContentInterface[]
     */
    private function filterContent(array $content): array
    {
        $filteredContent = [];

        foreach ($content as $item) {
            if ($item instanceof BodyContentInterface) {
                $filteredContent[] = clone $item;
            }
        }

        return $filteredContent;
    }

    private function buildMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->content as $item) {
            if ($item instanceof HasMetadataInterface) {
                $metadata = $metadata->merge($item->getMetadata());
            }
        }

        return $metadata;
    }
}
