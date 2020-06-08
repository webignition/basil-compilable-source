<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;

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
