<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\Stubble\Resolvable;
use webignition\Stubble\ResolvableInterface;

class ObjectConstructor extends AbstractMethodInvocationEncapsulator
{
    private const RENDER_TEMPLATE = 'new {{ method_invocation }}';

    private ClassName $class;
    public function __construct(ClassName $class, ?MethodArgumentsInterface $arguments = null)
    {
        parent::__construct($class->renderClassName(), $arguments);

        $this->class = $class;
    }

    protected function getAdditionalMetadata(): MetadataInterface
    {
        return new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                $this->class,
            ]),
        ]);
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'method_invocation' => $this->invocation->render(),
            ]
        );
    }
}
