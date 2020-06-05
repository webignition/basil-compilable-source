<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependencyInterface;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;

class ObjectPropertyAccessExpression extends AbstractExpression
{
    private const RENDER_PATTERN = '%s->%s';

    private VariablePlaceholderInterface $objectPlaceholder;
    private string $property;

    public function __construct(VariablePlaceholderInterface $objectPlaceholder, string $property)
    {
        parent::__construct();

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
        $metadata = parent::getMetadata();

        if ($this->objectPlaceholder instanceof VariableDependencyInterface) {
            $metadata = $metadata->merge($this->objectPlaceholder->getMetadata());
        }

        return $metadata;
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
