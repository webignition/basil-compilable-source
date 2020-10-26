<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Annotation\ParameterAnnotation;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\DocBlock\DocBlock;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class MethodDefinition implements MethodDefinitionInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE_WITHOUT_DOCBLOCK = <<<'EOD'
{{ signature }}
{
{{ body }}
}
EOD;

    private const RENDER_TEMPLATE_WITH_DOCBLOCK = <<<'EOD'
{{ docblock }}
{{ signature }}
{
{{ body }}
}
EOD;

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PROTECTED = 'protected';
    public const VISIBILITY_PRIVATE = 'private';

    private string $visibility;

    private ?string $returnType;
    private string $name;
    private BodyInterface $body;

    /**
     * @var string[]
     */
    private array $arguments;
    private bool $isStatic;
    private ?DocBlock $docblock;

    /**
     * @param string $name
     * @param BodyInterface $body
     * @param string[] $arguments
     */
    public function __construct(string $name, BodyInterface $body, array $arguments = [])
    {
        $this->visibility = self::VISIBILITY_PUBLIC;
        $this->returnType = null;
        $this->name = $name;
        $this->body = $body;
        $this->arguments = $arguments;
        $this->isStatic = false;
        $this->docblock = $this->createDocBlock($arguments);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    public function getArguments(): array
    {
        return $this->arguments;
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

    public function getDocBlock(): ?DocBlock
    {
        return $this->docblock;
    }

    private function renderBody(): string
    {
        $lines = $this->body->render();
        $lines = $this->indent($lines);
        return rtrim($lines, "\n");
    }

    public function withDocBlock(DocBlock $docBlock): self
    {
        $new = clone $this;
        $new->docblock = $docBlock;

        return $new;
    }

    public function getTemplate(): string
    {
        if (null === $this->docblock) {
            return self::RENDER_TEMPLATE_WITHOUT_DOCBLOCK;
        }

        return self::RENDER_TEMPLATE_WITH_DOCBLOCK;
    }

    public function getContext(): array
    {
        return [
            'docblock' => $this->docblock instanceof DocBlock ? $this->docblock : '',
            'signature' => $this->createSignature(),
            'body' => $this->renderBody(),
        ];
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

    /**
     * @param string[] $arguments
     *
     * @return DocBlock|null
     */
    private function createDocBlock(array $arguments): ?DocBlock
    {
        if (0 === count($arguments)) {
            return null;
        }

        $lines = [];
        foreach ($arguments as $argument) {
            $lines[] = new ParameterAnnotation('string', new VariableName($argument));
        }

        return new DocBlock($lines);
    }
}
