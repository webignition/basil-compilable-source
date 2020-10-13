<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\VariableDependencyInterface;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;

class ObjectPropertyAccessExpression extends AbstractExpression
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '{{ object }}->{{ property }}';

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

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    /**
     * @return array<string, string>
     */
    protected function getRenderContext(): array
    {
        return [
            'object' => $this->objectPlaceholder->render(),
            'property' => $this->property,
        ];
    }
}
