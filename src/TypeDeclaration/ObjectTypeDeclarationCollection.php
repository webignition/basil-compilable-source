<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\TypeDeclaration;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;
use webignition\StubbleResolvable\ResolvableWithoutContext;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ObjectTypeDeclarationCollection implements TypeDeclarationCollectionInterface, ResolvableProviderInterface
{
    use RenderTrait;

    /**
     * @var ObjectTypeDeclaration[]
     */
    private array $declarations;

    /**
     * @param ObjectTypeDeclaration[] $declarations
     */
    public function __construct(array $declarations)
    {
        $this->declarations = array_filter($declarations, function ($item) {
            return $item instanceof ObjectTypeDeclaration;
        });
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->declarations as $declaration) {
            if ($declaration instanceof TypeDeclarationInterface) {
                $metadata = $metadata->merge($declaration->getMetadata());
            }
        }

        return $metadata;
    }

    public function getResolvable(): ResolvableInterface
    {
        $resolvableDeclarations = [];
        foreach ($this->declarations as $declaration) {
            $resolvableDeclarations[] = new ResolvedTemplateMutatorResolvable(
                new ResolvableWithoutContext((string) $declaration),
                function (string $resolvedTemplate) {
                    return $this->declarationResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return new ResolvedTemplateMutatorResolvable(
            ResolvableCollection::create($resolvableDeclarations),
            function (string $resolvedTemplate) {
                return $this->resolvedTemplateMutator($resolvedTemplate);
            }
        );
    }

    private function resolvedTemplateMutator(string $resolvedTemplate): string
    {
        $parts = explode(' | ', $resolvedTemplate);
        $parts = array_filter($parts);

        $namespaceSeparator = '\\';
        usort($parts, function (string $a, string $b) use ($namespaceSeparator) {
            $a = ltrim($a, $namespaceSeparator);
            $b = ltrim($b, $namespaceSeparator);

            if ($a === $b) {
                return 0;
            }

            return $a < $b ? -1 : 1;
        });

        $resolvedTemplate = implode(' | ', $parts);

        return trim($resolvedTemplate, '| ');
    }

    private function declarationResolvedTemplateMutator(string $resolvedTemplate): string
    {
        return $resolvedTemplate . ' | ';
    }
}
