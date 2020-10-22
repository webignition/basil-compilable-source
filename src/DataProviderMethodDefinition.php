<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\DocBlock\DocBlock;
use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Statement\Statement;

class DataProviderMethodDefinition extends MethodDefinition implements DataProviderMethodDefinitionInterface
{
    use HasMetadataTrait;

    /**
     * @var array<mixed>
     */
    private array $data;

    /**
     * @param string $name
     * @param array<mixed> $data
     */
    public function __construct(string $name, array $data)
    {
        $this->data = $data;

        parent::__construct($name, new Body([
            new Statement(
                new ReturnExpression(
                    ArrayExpression::fromDataSets($data)
                )
            ),
        ]));

        $this->metadata = new Metadata();
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getArguments(): array
    {
        return [];
    }

    public function getReturnType(): ?string
    {
        return 'array';
    }

    public function getVisibility(): string
    {
        return 'public';
    }

    public function getDocBlock(): ?DocBlock
    {
        return null;
    }

    public function isStatic(): bool
    {
        return false;
    }
}
