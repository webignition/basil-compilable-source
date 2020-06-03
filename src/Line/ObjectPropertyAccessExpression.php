<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvableVariablePlaceholderInterface;

class ObjectPropertyAccessExpression extends AbstractExpression
{
    private const RENDER_PATTERN = '%s->%s';

    private ResolvableVariablePlaceholderInterface $objectPlaceholder;
    private string $property;

    public function __construct(ResolvableVariablePlaceholderInterface $objectPlaceholder, string $property)
    {
        parent::__construct();

        $this->objectPlaceholder = $objectPlaceholder;
        $this->property = $property;
    }

    public function getObjectPlaceholder(): ResolvableVariablePlaceholderInterface
    {
        return $this->objectPlaceholder;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getMetadata(): MetadataInterface
    {
        return parent::getMetadata()->merge($this->objectPlaceholder->getMetadata());
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->objectPlaceholder->render(),
            $this->property
        );
    }
}
