<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

class ObjectPropertyAccessExpression implements ExpressionInterface, ResolvableProviderInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = '{{ object }}->{{ property }}';

    private VariablePlaceholderInterface $objectPlaceholder;
    private string $property;

    public function __construct(VariablePlaceholderInterface $objectPlaceholder, string $property)
    {
        $this->objectPlaceholder = $objectPlaceholder;
        $this->property = $property;
    }

    public function getObjectPlaceholder(): VariablePlaceholderInterface
    {
        return $this->objectPlaceholder;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->objectPlaceholder->getMetadata();
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'object' => $this->objectPlaceholder->render(),
                'property' => $this->property,
            ]
        );
    }
}
