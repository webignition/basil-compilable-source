<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class MethodInvocation implements MethodInvocationInterface
{
    public const ARGUMENT_FORMAT_INLINE = 'inline';
    public const ARGUMENT_FORMAT_STACKED = 'stacked';

    private const RENDER_PATTERN = '%s(%s)';

    private $methodName;
    private $arguments = [];
    private $argumentFormat;

    /**
     * @param string $methodName
     * @param string[] $arguments
     * @param string $argumentFormat
     */
    public function __construct(
        string $methodName,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE
    ) {
        $this->methodName = $methodName;
        $this->arguments = $arguments;
        $this->argumentFormat = $argumentFormat;
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
        return new Metadata();
    }

    public function render(): string
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

        $hasArguments = count($arguments) > 0;

        if (!$hasArguments) {
            return '';
        }

        $argumentPrefix = '';
        $join = ', ';
        $stringSuffix = '';

        if (self::ARGUMENT_FORMAT_STACKED === $this->getArgumentFormat()) {
            array_walk($arguments, function (&$argument) {
                $argument = '    ' . $argument;
            });

            $argumentPrefix = "\n";
            $join = ',';
            $stringSuffix = "\n";
        }

        array_walk($arguments, function (&$argument) use ($argumentPrefix) {
            $argument = $argumentPrefix . $argument;
        });

        return implode($join, $arguments) . $stringSuffix;
    }
}
