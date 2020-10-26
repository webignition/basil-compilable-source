<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Block\RenderableClassDependencyCollection;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ClassDefinition implements ClassDefinitionInterface, ResolvableInterface
{
    use DeferredResolvableCreationTrait;
    use RenderTrait;

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

    protected function createResolvable(): ResolvableInterface
    {
        $renderableClassNames = new RenderableClassDependencyCollection($this->getClassDependencies()->getClassNames());

        $template = self::RENDER_TEMPLATE;
        if ($renderableClassNames->isEmpty()) {
            $template = str_replace('{{ dependencies }}', '', $template);
            $template = ltrim($template);
        }

        return new Resolvable(
            $template,
            [
                'dependencies' => $renderableClassNames,
                'signature' => $this->signature,
                'body' => new ResolvedTemplateMutatorResolvable(
                    $this->body,
                    function (string $resolvedTemplate): string {
                        if ('' === $resolvedTemplate) {
                            return '';
                        }

                        $resolvedTemplate = $this->indent($resolvedTemplate);

                        return "\n" . rtrim($resolvedTemplate) . "\n";
                    }
                ),
            ]
        );
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
