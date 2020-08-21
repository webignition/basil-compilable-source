<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\TypeDeclaration;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ObjectTypeDeclarationCollection implements TypeDeclarationCollectionInterface
{
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

    public function render(): string
    {
        $renderedDeclarations = [];
        foreach ($this->declarations as $declaration) {
            $renderedDeclarations[] = $declaration->render();
        }

        $namespaceSeparator = '\\';

        usort($renderedDeclarations, function (string $a, string $b) use ($namespaceSeparator) {
            $a = ltrim($a, $namespaceSeparator);
            $b = ltrim($b, $namespaceSeparator);

            if ($a === $b) {
                return 0;
            }

            return $a < $b ? -1 : 1;
        });

        return implode(' | ', $renderedDeclarations);
    }
}
