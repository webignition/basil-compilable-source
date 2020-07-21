<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Expression\ClassDependency;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ObjectConstructor extends MethodInvocation
{
    private const RENDER_PATTERN = 'new %s';

    private ClassDependency $class;

    /**
     * @param ClassDependency $class
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     */
    public function __construct(
        ClassDependency $class,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE
    ) {
        parent::__construct($class->getClass(), $arguments, $argumentFormat);

        $this->class = $class;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = parent::getMetadata();

        $metadata = $metadata->merge(
            new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $this->class,
                ]),
            ])
        );

        return $metadata;
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            parent::renderWithoutErrorSuppression()
        );
    }
}
