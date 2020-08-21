<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ObjectConstructor extends MethodInvocation
{
    private const RENDER_PATTERN = 'new %s';

    private ClassName $class;

    /**
     * @param ClassName $class
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     */
    public function __construct(
        ClassName $class,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE
    ) {
        parent::__construct($class->getClass(), $arguments, $argumentFormat);

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
