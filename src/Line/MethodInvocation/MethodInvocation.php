<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class MethodInvocation implements MethodInvocationInterface
{
    public const ARGUMENT_FORMAT_INLINE = 'inline';
    public const ARGUMENT_FORMAT_STACKED = 'stacked';

    private const RENDER_PATTERN = '%s(%s)';

    private $methodName;

    /**
     * @var ExpressionInterface[]
     */
    private $arguments = [];
    private $argumentFormat;
    private $castTo;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     * @param string|null $castTo
     */
    public function __construct(
        string $methodName,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE,
        ?string $castTo = null
    ) {
        $this->methodName = $methodName;
        $this->argumentFormat = $argumentFormat;
        $this->castTo = $castTo;
        $this->arguments = array_filter($arguments, function ($argument) {
            return $argument instanceof ExpressionInterface;
        });

        $this->metadata = new Metadata();
        foreach ($this->arguments as $expression) {
            $this->metadata = $this->metadata->merge($expression->getMetadata());
        }
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgumentFormat(): string
    {
        return $this->argumentFormat;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function getCastTo(): ?string
    {
        return $this->castTo;
    }

    public function render(): string
    {
        return $this->renderCastTo() . $this->renderWithoutCasting();
    }

    protected function renderCastTo(): string
    {
        return null === $this->castTo ? '' : '(' . $this->castTo . ') ';
    }

    protected function renderWithoutCasting(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->getMethodName(),
            $this->createArgumentsString()
        );
    }

    private function createArgumentsString(): string
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

        if (self::ARGUMENT_FORMAT_STACKED === $this->getArgumentFormat()) {
            array_walk($renderedArguments, function (&$argument) {
                $argument = '    ' . $argument;
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
}
