<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

class ObjectConstructor extends MethodInvocation
{
    private const RENDER_PATTERN = 'new %s';

    private ClassName $class;

    public function __construct(ClassName $class, ?MethodArgumentsInterface $arguments = null)
    {
        parent::__construct($class->renderClassName(), $arguments);

        $this->class = $class;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = parent::getMetadata();

        return $metadata->merge(
            new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $this->class,
                ]),
            ])
        );
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            parent::renderWithoutErrorSuppression()
        );
    }
}
