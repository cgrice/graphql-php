<?php

namespace Digia\GraphQL\Language\AST\Node;

use Digia\GraphQL\Language\AST\KindEnum;
use Digia\GraphQL\Language\AST\Node\Contract\ValueNodeInterface;

class ListValueNode extends AbstractNode implements ValueNodeInterface
{

    /**
     * @var string
     */
    protected $kind = KindEnum::LIST;

    /**
     * @var ValueNodeInterface[]
     */
    protected $values;
}