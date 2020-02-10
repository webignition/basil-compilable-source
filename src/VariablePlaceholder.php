<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class VariablePlaceholder extends AbstractStringLine implements ExpressionInterface
{
    public const TYPE_DEPENDENCY = 'dependency';
    public const TYPE_EXPORT = 'export';

    private $type;

    public function __construct(string $content, string $type)
    {
        parent::__construct($content);

        $this->type = self::isAllowedType($type) ? $type : self::TYPE_EXPORT;
    }

    public static function isAllowedType(string $type): bool
    {
        return in_array(
            $type,
            [
                self::TYPE_DEPENDENCY,
                self::TYPE_EXPORT,
            ]
        );
    }

    public static function createDependency(string $content): VariablePlaceholder
    {
        return new VariablePlaceholder($content, self::TYPE_DEPENDENCY);
    }

    public static function createExport(string $content): VariablePlaceholder
    {
        return new VariablePlaceholder($content, self::TYPE_EXPORT);
    }

    public function getType(): string
    {
        return $this->type;
    }

    protected function getRenderPattern(): string
    {
        return '{{ %s }}';
    }

    public function getMetadata(): MetadataInterface
    {
        $placeholderCollection = VariablePlaceholderCollection::create($this->type);
        $placeholderCollection->add($this);

        $componentKey = $this->type === self::TYPE_DEPENDENCY
            ? Metadata::KEY_VARIABLE_DEPENDENCIES
            : Metadata::KEY_VARIABLE_EXPORTS;

        return new Metadata([
            $componentKey => $placeholderCollection,
        ]);
    }
}
