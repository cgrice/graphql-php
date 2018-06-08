<?php

namespace Digia\GraphQL\Language\Node;

use Digia\GraphQL\Language\Location;

class ObjectValueNode extends AbstractNode implements ValueNodeInterface
{
    /**
     * @var ObjectFieldNode[]
     */
    protected $fields;

    /**
     * ObjectValueNode constructor.
     *
     * @param ObjectFieldNode[] $fields
     * @param Location|null     $location
     */
    public function __construct(array $fields, ?Location $location)
    {
        parent::__construct(NodeKindEnum::OBJECT, $location);

        $this->fields = $fields;
    }

    /**
     * @return ObjectFieldNode[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return array
     */
    public function getFieldsAST(): array
    {
        return \array_map(function (ObjectFieldNode $node) {
            return $node->toAST();
        }, $this->fields);
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toAST(): array
    {
        return [
            'kind'   => $this->kind,
            'fields' => $this->getFieldsAST(),
        ];
    }
}
