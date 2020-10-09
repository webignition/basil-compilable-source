<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocation extends AbstractMethodInvocationEncapsulator implements
    StaticObjectMethodInvocationInterface
{
    private const RENDER_PATTERN = '%s::%s';

    private StaticObject $staticObject;

    public function __construct(
        StaticObject $staticObject,
        string $methodName,
        ?MethodArgumentsInterface $arguments = null
    ) {
        parent::__construct($methodName, $arguments);

        $this->staticObject = $staticObject;
    }

    protected function getAdditionalMetadata(): MetadataInterface
    {
        return $this->staticObject->getMetadata();
    }

    public function getStaticObject(): StaticObject
    {
        return $this->staticObject;
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->getStaticObject()->render(),
            $this->invocation->render()
        );
    }
}
