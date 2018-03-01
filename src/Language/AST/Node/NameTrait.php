<?php

namespace Digia\GraphQL\Language\AST\Node;

trait NameTrait
{

    /**
     * @var NameNode|null
     */
    protected $name;

    /**
     * @return NameNode|null
     */
    public function getName(): ?NameNode
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameValue(): string
    {
        return $this->name->getValue();
    }

    /**
     * @return array|null
     */
    public function getNameAsArray(): ?array
    {
        return null !== $this->name ? $this->name->toArray() : null;
    }
}