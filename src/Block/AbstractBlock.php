<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\IndentTrait;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

abstract class AbstractBlock implements HasMetadataInterface, ResolvableInterface
{
    use IndentTrait;

    protected BodyInterface $body;

    public function __construct(BodyInterface $body)
    {
        $this->body = $body;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    protected function createResolvableBody(): ResolvableInterface
    {
        return new ResolvedTemplateMutatorResolvable(
            $this->body,
            function (string $resolvedTemplate): string {
                return rtrim($this->indent($resolvedTemplate));
            }
        );
    }
}
