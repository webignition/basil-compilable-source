<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

class ClassSignature
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

    protected function getRenderTemplate(): string
    {
        if (null === $this->baseClass) {
            return self::RENDER_TEMPLATE_WITHOUT_BASE_CLASS;
        }

        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        $context = [
            'name' => $this->getName(),
        ];

        if ($this->baseClass instanceof ClassName) {
            $context['base_class'] = $this->baseClass->renderClassName();
        }

        return $context;
    }
}
