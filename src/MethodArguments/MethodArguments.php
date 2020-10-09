<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodArguments;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\HasMetadataTrait;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;

class MethodArguments implements MethodArgumentsInterface, SourceInterface
{
    use HasMetadataTrait;

    public const FORMAT_INLINE = 'inline';
    public const FORMAT_STACKED = 'stacked';

    /**
     * @var ExpressionInterface[]
     */
    private array $arguments;
    private string $format;

    /**
     * @var MetadataInterface
     */
    private MetadataInterface $metadata;

    /**
     * @param ExpressionInterface[] $arguments
     * @param string $format
     */
    public function __construct(array $arguments = [], string $format = self::FORMAT_INLINE)
    {
        $this->arguments = array_filter($arguments, function ($argument) {
            return $argument instanceof ExpressionInterface;
        });
        $this->format = $format;

        $this->metadata = new Metadata();
        foreach ($this->arguments as $expression) {
            $this->metadata = $this->metadata->merge($expression->getMetadata());
        }
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function render(): string
    {
        $arguments = $this->getArguments();
        if ([] === $arguments) {
            return '';
        }

        $renderedArguments = array_map(
            function (ExpressionInterface $expression) {
                return $expression->render();
            },
            $arguments
        );

        $argumentPrefix = '';
        $join = ', ';
        $stringSuffix = '';

        if (self::FORMAT_STACKED === $this->getFormat()) {
            array_walk($renderedArguments, function (&$argument) {
                $argumentLines = explode("\n", $argument);
                array_walk($argumentLines, function (&$line) {
                    $line = $this->indentLine($line);
                });

                $argument = trim(implode("\n", $argumentLines));
                $argument = $this->indentLine($argument);
            });

            $argumentPrefix = "\n";
            $join = ',';
            $stringSuffix = "\n";
        }

        array_walk($renderedArguments, function (&$argument) use ($argumentPrefix) {
            $argument = $argumentPrefix . $argument;
        });

        return implode($join, $renderedArguments) . $stringSuffix;
    }

    private function indentLine(string $content): string
    {
        if ('' !== $content) {
            $content = '    ' . $content;
        }

        return $content;
    }
}
