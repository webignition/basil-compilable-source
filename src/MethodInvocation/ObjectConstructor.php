<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

class ObjectConstructor implements InvocableInterface
{
    private const RENDER_PATTERN = 'new %s';

    private ClassName $class;
    private MethodInvocation $invocation;

    public function __construct(ClassName $class, ?MethodArgumentsInterface $arguments = null)
    {
        $this->class = $class;
        $this->invocation = new MethodInvocation($class->renderClassName(), $arguments);
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->invocation->getMetadata()->merge(
            new Metadata([
                Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                    $this->class,
                ]),
            ])
        );
    }

    public function getCall(): string
    {
        return $this->invocation->getCall();
    }

    public function getArguments(): MethodArgumentsInterface
    {
        return $this->invocation->getArguments();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->invocation->render()
        );
    }
}
