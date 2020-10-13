<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

class ClassSignature implements RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE_WITHOUT_BASE_CLASS = 'class {{ name }}';
    private const RENDER_TEMPLATE = self::RENDER_TEMPLATE_WITHOUT_BASE_CLASS . ' extends {{ base_class }}';

    private string $name;
    private ?ClassName $baseClass;

    public function __construct(string $name, ?ClassName $baseClass = null)
    {
        $this->name = $name;
        $this->baseClass = $baseClass;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBaseClass(): ?ClassName
    {
        return $this->baseClass;
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            $this->baseClass instanceof ClassName ? self::RENDER_TEMPLATE : self::RENDER_TEMPLATE_WITHOUT_BASE_CLASS,
            [
                'name' => $this->getName(),
                'base_class' => $this->baseClass instanceof ClassName ? $this->baseClass->renderClassName() : '',
            ]
        );
    }
}
