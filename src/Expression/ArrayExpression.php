<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

class ArrayExpression extends AbstractExpression
{
    /**
     * @var array<mixed>
     */
    private array $data = [];

    private const INDENT_SPACE_COUNT = 4;
    private const DEFAULT_INDENT_COUNT = 1;

    /**
     * @param array<mixed> $data
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $this->data = $data;
    }

    /**
     * @return array<mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function render(): string
    {
        return rtrim($this->convertArrayToString($this->data, self::DEFAULT_INDENT_COUNT), ',');
    }

    /**
     * @param array<mixed> $array
     * @param int $indentCount
     *
     * @return string
     */
    private function convertArrayToString(array $array, int $indentCount = self::DEFAULT_INDENT_COUNT): string
    {
        if (empty($array)) {
            return '[]';
        }

        $containerIndentCount = min($indentCount, $indentCount - 1);
        $containerIndent = str_repeat(' ', $containerIndentCount * self::INDENT_SPACE_COUNT);

        $bodyIndent = str_repeat(' ', $indentCount * self::INDENT_SPACE_COUNT);

        $containerTemplate =
            '[' . "\n"
            . '%s' . "\n"
            . $containerIndent . '],';

        $keyValueTemplate = $bodyIndent . "'%s' => %s";
        $keyValueStrings = [];

        foreach ($array as $key => $value) {
            $keyAsString = (string) $key;

            if (is_array($value)) {
                ksort($value);

                $valueAsString = $this->convertArrayToString($value, $indentCount + 1);
            } else {
                $valueAsString = "'" . ((string) $value) . "',";
            }

            $keyValueStrings[] = sprintf($keyValueTemplate, $keyAsString, $valueAsString);
        }

        return sprintf($containerTemplate, implode("\n", $keyValueStrings));
    }
}
