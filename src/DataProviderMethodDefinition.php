<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Line\ArrayExpression;
use webignition\BasilCompilableSource\Line\Statement\ReturnStatement;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class DataProviderMethodDefinition extends MethodDefinition implements DataProviderMethodDefinitionInterface
{
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

        parent::__construct($name, new CodeBlock([
            new ReturnStatement(
                new ArrayExpression($data)
            ),
        ]));
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata();
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
