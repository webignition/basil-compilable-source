<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\BlockInterface;
use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class MethodDefinition implements MethodDefinitionInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
%s
{
%s
}
EOD;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    private string $visibility = self::VISIBILITY_PUBLIC;

    private ?string $returnType = null;
    private string $name;
    private CodeBlockInterface $codeBlock;

    /**
     * @var string[]
     */
    private array $arguments = [];
    private bool $isStatic = false;
    private ?DocBlock $docBlock = null;

    /**
     * @param string $name
     * @param CodeBlockInterface $codeBlock
     * @param string[] $arguments
     */
    public function __construct(string $name, CodeBlockInterface $codeBlock, array $arguments = [])
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
        $this->returnType = null;
        $this->name = $name;
        $this->codeBlock = $codeBlock;
        $this->arguments = $arguments;
        $this->isStatic = false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCodeBlock(): CodeBlockInterface
    {
        return $this->codeBlock;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function addLine(LineInterface $statement): void
    {
        $this->codeBlock->addLine($statement);
    }

    public function addLines(array $lines): void
    {
        $this->codeBlock->addLines($lines);
    }

    public function getLines(): array
    {
        return $this->codeBlock->getLines();
    }

    public function addBlock(BlockInterface $block): void
    {
        $this->addLines($block->getLines());
    }

    public function isEmpty(): bool
    {
        return $this->codeBlock->isEmpty();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->codeBlock->getMetadata();
    }

    public function setPublic(): void
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
    }

    public function setProtected(): void
    {
        $this->visibility = self::VISIBILITY_PROTECTED;
    }

    public function setPrivate(): void
    {
        $this->visibility = self::VISIBILITY_PRIVATE;
    }

    public function getReturnType(): ?string
    {
        return $this->returnType;
    }

    public function setReturnType(?string $returnType): void
    {
        $this->returnType = $returnType;
    }

    public function setStatic(): void
    {
        $this->isStatic = true;
    }

    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setDocBlock(DocBlock $docBlock): void
    {
        $this->docBlock = $docBlock;
    }

    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    public function render(): string
    {
        $signature = $this->createSignature();

        $lines = $this->codeBlock->render();
        $lines = $this->indent($lines);
        $lines = rtrim($lines, "\n");

        $content = sprintf(self::RENDER_TEMPLATE, $signature, $lines);

        if ($this->docBlock instanceof DocBlock) {
            $content = $this->docBlock->render() . "\n" . $content;
        }

        return $content;
    }

    private function createSignature(): string
    {
        $signature = $this->getVisibility() . ' ';

        if ($this->isStatic()) {
            $signature .= 'static ';
        }

        $arguments = $this->createSignatureArguments($this->getArguments());
        $signature .= 'function ' . $this->getName() . '(' . $arguments . ')';

        $returnType = $this->getReturnType();

        if (null !== $returnType) {
            $signature .= ': ' . $returnType;
        }

        return $signature;
    }

    /**
     * @param array<string, string> $argumentNames
     *
     * @return string
     */
    private function createSignatureArguments(array $argumentNames): string
    {
        $arguments = $argumentNames;

        array_walk($arguments, function (&$argument) {
            $argument = '$' . $argument;
        });

        return implode(', ', $arguments);
    }

    private function indent(string $content): string
    {
        if ('' === $content) {
            return '';
        }

        $lines = explode("\n", $content);

        array_walk($lines, function (&$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        });

        return implode("\n", $lines);
    }
}
