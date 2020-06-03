<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\FunctionBodyInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;

interface CodeBlockInterface extends BlockInterface, HasMetadataInterface, FunctionBodyInterface
{
}
