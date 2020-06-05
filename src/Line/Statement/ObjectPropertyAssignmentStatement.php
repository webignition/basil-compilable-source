<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\ObjectPropertyAccessExpression;

class ObjectPropertyAssignmentStatement extends AssignmentStatement
{
    public function __construct(ObjectPropertyAccessExpression $accessExpression, ExpressionInterface $valueExpression)
    {
        parent::__construct(
            new ObjectPropertyAccessExpression(
                $accessExpression->getObjectPlaceholder(),
                $accessExpression->getProperty()
            ),
            $valueExpression
        );
    }
}
