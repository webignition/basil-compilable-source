<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\StubbleResolvable\ResolvableInterface;

interface ClassDefinitionInterface extends HasMetadataInterface, ResolvableInterface
{
    public function getSignature(): ClassSignature;

    public function getBody(): ClassBody;
}
