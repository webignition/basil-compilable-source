<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Line\AbstractExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class VariablePlaceholder extends AbstractExpression
{
    private const RENDER_PATTERN = '{{ %s }}';

    public const TYPE_DEPENDENCY = 'dependency';
    public const TYPE_EXPORT = 'export';

    private string $name;
    private string $type;

    public function __construct(string $name, string $type)
    {
        parent::__construct();

        $this->name = $name;
        $this->type = self::isAllowedType($type) ? $type : self::TYPE_EXPORT;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function render(): string
    {
        return sprintf(self::RENDER_PATTERN, $this->name);
    }
}
