<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Annotation;

use webignition\BasilCompilableSource\VariableName;

class ParameterAnnotation extends AbstractAnnotation implements AnnotationInterface
{
    public function __construct(string $type, VariableName $name)
    {
        parent::__construct('param', [$type, $name->render()]);
    }
}
