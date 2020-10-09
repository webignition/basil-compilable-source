<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocation extends MethodInvocation implements StaticObjectMethodInvocationInterface
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

    public function getStaticObject(): StaticObject
    {
        return $this->staticObject;
    }

    public function getMetadata(): MetadataInterface
    {
        return parent::getMetadata()->merge($this->staticObject->getMetadata());
    }

    public function render(): string
    {
        $staticObject = $this->getStaticObject()->render();
        if ($this->suppressErrors === true) {
            $staticObject = '@' . $staticObject;
        }

        return sprintf(
            self::RENDER_PATTERN,
            $staticObject,
            parent::renderWithoutErrorSuppression()
        );
    }
}
