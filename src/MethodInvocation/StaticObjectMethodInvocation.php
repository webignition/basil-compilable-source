<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocation extends AbstractMethodInvocationEncapsulator implements
    StaticObjectMethodInvocationInterface
{
    private const RENDER_TEMPLATE = '{{ object }}::{{ method_invocation }}';

    private StaticObject $staticObject;

    public function __construct(
        StaticObject $staticObject,
        string $methodName,
        ?MethodArgumentsInterface $arguments = null
    ) {
        parent::__construct($methodName, $arguments);

        $this->staticObject = $staticObject;
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'object' => $this->staticObject->render(),
                'method_invocation' => $this->invocation->render(),
            ]
        );
    }

    protected function getAdditionalMetadata(): MetadataInterface
    {
        return $this->staticObject->getMetadata();
    }

    public function getStaticObject(): StaticObject
    {
        return $this->staticObject;
    }
}
