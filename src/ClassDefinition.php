<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClassDefinition implements ClassDefinitionInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
%s

%s
{%s}
EOD;

    private ClassSignature $signature;
    private ClassBody $body;

    public function __construct(ClassSignature $signature, ClassBody $body)
    {
        $this->signature = $signature;
        $this->body = $body;
    }

    public function getSignature(): ClassSignature
    {
        return $this->signature;
    }

    public function getBody(): ClassBody
    {
        return $this->body;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->body->getMetadata();
    }

    public function render(): string
    {
        $classDependencies = $this->getMetadata()->getClassDependencies();
        $baseClass = $this->signature->getBaseClass();

        if ($baseClass instanceof ClassName) {
            $classDependencies = $classDependencies->merge(new ClassDependencyCollection([
                $baseClass,
            ]));
        }

        return trim(sprintf(
            self::RENDER_TEMPLATE,
            $classDependencies->render(),
            $this->signature->render(),
            $this->createClassBody()
        ));
    }

    private function createClassBody(): string
    {
        $renderedBody = $this->body->render();
        if ('' === $renderedBody) {
            return '';
        }

        return "\n" . $this->indent($this->body->render()) . "\n";
    }

    private function indent(string $content): string
    {
        $lines = explode("\n", $content);

        array_walk($lines, function (&$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        });

        return implode("\n", $lines);
    }
}
