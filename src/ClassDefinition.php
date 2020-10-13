<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClassDefinition implements ClassDefinitionInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = <<<'EOD'
{{ dependencies }}

{{ signature }}
{{{body}}}
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

    protected function getRenderTemplate(): string
    {
        $template = self::RENDER_TEMPLATE;

        if ('' === $this->getClassDependencies()->render()) {
            $template = str_replace('{{ dependencies }}', '', $template);
            $template = ltrim($template);
        }

        return $template;
    }

    protected function getRenderContext(): array
    {
        return [
            'dependencies' => $this->getClassDependencies()->render(),
            'signature' => $this->signature->render(),
            'body' => $this->renderBody(),
        ];
    }

    private function getClassDependencies(): ClassDependencyCollection
    {
        $classDependencies = $this->getMetadata()->getClassDependencies();
        $baseClass = $this->signature->getBaseClass();

        if ($baseClass instanceof ClassName) {
            $classDependencies = $classDependencies->merge(new ClassDependencyCollection([
                $baseClass,
            ]));
        }

        return $classDependencies;
    }

    private function renderBody(): string
    {
        $body = $this->body->render();

        if ('' !== $body) {
            $body = "\n" . $this->indent($body) . "\n";
        }

        return $body;
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
