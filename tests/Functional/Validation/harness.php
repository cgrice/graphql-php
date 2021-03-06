<?php

namespace Digia\GraphQL\Test\Functional\Validation;

use Digia\GraphQL\Schema\Schema;
use Digia\GraphQL\Type\Definition\EnumType;
use Digia\GraphQL\Type\Definition\InputObjectType;
use Digia\GraphQL\Type\Definition\InterfaceType;
use Digia\GraphQL\Type\Definition\ObjectType;
use Digia\GraphQL\Type\Definition\ScalarType;
use Digia\GraphQL\Type\Definition\UnionType;
use function Digia\GraphQL\Type\Boolean;
use function Digia\GraphQL\Type\newDirective;
use function Digia\GraphQL\Type\newEnumType;
use function Digia\GraphQL\Type\Float;
use function Digia\GraphQL\Type\ID;
use function Digia\GraphQL\Type\newInputObjectType;
use function Digia\GraphQL\Type\Int;
use function Digia\GraphQL\Type\newInterfaceType;
use function Digia\GraphQL\Type\newList;
use function Digia\GraphQL\Type\newNonNull;
use function Digia\GraphQL\Type\newObjectType;
use function Digia\GraphQL\Type\newScalarType;
use function Digia\GraphQL\Type\newSchema;
use function Digia\GraphQL\Type\String;
use function Digia\GraphQL\Type\newUnionType;

function Being(): InterfaceType
{
    static $instance = null;
    return $instance ??
        $instance = newInterfaceType([
            'name'   => 'Being',
            'fields' => function () {
                return [
                    'name' => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                ];
            },
        ]);
}

function Pet(): InterfaceType
{
    static $instance = null;
    return $instance ??
        $instance = newInterfaceType([
            'name'   => 'Pet',
            'fields' => function () {
                return [
                    'name' => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                ];
            },
        ]);
}

function Canine(): InterfaceType
{
    static $instance = null;
    return $instance ??
        $instance = newInterfaceType([
            'name'   => 'Canine',
            'fields' => function () {
                return [
                    'name' => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                ];
            },
        ]);
}

function DogCommand(): EnumType
{
    static $instance = null;
    return $instance ??
        $instance = newEnumType([
            'name'   => 'DogCommand',
            'values' => [
                'SIT'  => ['value' => 0],
                'HEEL' => ['value' => 1],
                'DOWN' => ['value' => 2],
            ],
        ]);
}

function Dog(): ObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newObjectType([
            'name'       => 'Dog',
            'fields'     => function () {
                return [
                    'name'            => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                    'nickname'        => ['type' => String()],
                    'barkVolume'      => ['type' => Int()],
                    'barks'           => ['type' => Boolean()],
                    'doesKnowCommand' => [
                        'type' => Boolean(),
                        'args' => [
                            'dogCommand' => ['type' => DogCommand()],
                        ],
                    ],
                    'isHouseTrained'  => [
                        'type' => Boolean(),
                        'args' => [
                            'atOtherHomes' => [
                                'type'         => Boolean(),
                                'defaultValue' => true,
                            ],
                        ],
                    ],
                    'isAtLocation'    => [
                        'type' => Boolean(),
                        'args' => ['x' => ['type' => Int()], 'y' => ['type' => Int()]],
                    ],
                ];
            },
            'interfaces' => [Being(), Pet(), Canine()],
        ]);
}

function Cat(): ObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newObjectType([
            'name'       => 'Cat',
            'fields'     => function () {
                return [
                    'name'       => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                    'nickname'   => ['type' => String()],
                    'meows'      => ['type' => Boolean()],
                    'meowVolume' => ['type' => Int()],
                    'furColor'   => ['type' => FurColor()],
                ];
            },
            'interfaces' => [Being(), Pet()],
        ]);
}

function CatOrDog(): UnionType
{
    static $instance = null;
    return $instance ??
        $instance = newUnionType([
            'name'  => 'CatOrDog',
            'types' => [Cat(), Dog()],
        ]);
}

function Intelligent(): InterfaceType
{
    static $instance = null;
    return $instance ??
        $instance = newInterfaceType([
            'name'   => 'Intelligent',
            'fields' => [
                'iq' => ['type' => Int()],
            ],
        ]);
}

function Human(): ObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newObjectType([
            'name'       => 'Human',
            'interfaces' => [Being(), Intelligent()],
            'fields'     => function () {
                return [
                    'name'      => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                    'pets'      => ['type' => newList(Pet())],
                    'relatives' => ['type' => newList(Human())],
                    'iq'        => ['type' => Int()],
                ];
            },
        ]);
}

function Alien(): ObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newObjectType([
            'name'       => 'Alien',
            'interfaces' => [Being(), Intelligent()],
            'fields'     => function () {
                return [
                    'iq'      => ['type' => Int()],
                    'name'    => [
                        'type' => String(),
                        'args' => ['surname' => ['type' => Boolean()]],
                    ],
                    'numEyes' => ['type' => Int()],
                ];
            },
        ]);
}

function DogOrHuman(): UnionType
{
    static $instance = null;
    return $instance ??
        $instance = newUnionType([
            'name'  => 'DogOrHuman',
            'types' => [Dog(), Human()],
        ]);
}

function HumanOrAlien(): UnionType
{
    static $instance = null;
    return $instance ??
        $instance = newUnionType([
            'name'  => 'HumanOrAlien',
            'types' => [Human(), Alien()],
        ]);
}

function FurColor(): EnumType
{
    static $instance = null;
    return $instance ??
        $instance = newEnumType([
            'name'   => 'FurColor',
            'values' => [
                'BROWN'   => ['value' => 0],
                'BLACK'   => ['value' => 1],
                'TAN'     => ['value' => 2],
                'SPOTTED' => ['value' => 3],
                'NO_FUR'  => ['value' => 4],
                'UNKNOWN' => ['value' => 5],
            ],
        ]);
}

function ComplexInput(): InputObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newInputObjectType([
            'name'   => 'ComplexInput',
            'fields' => [
                'requiredField'   => ['type' => newNonNull(Boolean())],
                'intField'        => ['type' => Int()],
                'stringField'     => ['type' => String()],
                'booleanField'    => ['type' => Boolean()],
                'stringListField' => ['type' => newList(String())],
            ],
        ]);
}

function ComplicatedArgs(): ObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newObjectType([
            'name'   => 'ComplicatedArgs',
            // TODO List
            // TODO Coercion
            // TODO NotNulls
            'fields' => function () {
                return [
                    'intArgField'               => [
                        'type' => String(),
                        'args' => ['intArg' => ['type' => Int()]],
                    ],
                    'nonNullIntArgField'        => [
                        'type' => String(),
                        'args' => ['nonNullIntArg' => ['type' => newNonNull(Int())]],
                    ],
                    'stringArgField'            => [
                        'type' => String(),
                        'args' => ['stringArg' => ['type' => String()]],
                    ],
                    'booleanArgField'           => [
                        'type' => String(),
                        'args' => ['booleanArg' => ['type' => Boolean()]],
                    ],
                    'enumArgField'              => [
                        'type' => String(),
                        'args' => ['enumArg' => ['type' => FurColor()]],
                    ],
                    'floatArgField'             => [
                        'type' => String(),
                        'args' => ['floatArg' => ['type' => Float()]],
                    ],
                    'idArgField'                => [
                        'type' => String(),
                        'args' => ['idArg' => ['type' => ID()]],
                    ],
                    'stringListArgField'        => [
                        'type' => String(),
                        'args' => ['stringListArg' => ['type' => newList(String())]],
                    ],
                    'stringListNonNullArgField' => [
                        'type' => String(),
                        'args' => ['stringListNonNullArg' => ['type' => newList(newNonNull(String()))]],
                    ],
                    'complexArgField'           => [
                        'type' => String(),
                        'args' => ['complexArg' => ['type' => ComplexInput()]],
                    ],
                    'multipleReqs'              => [
                        'type' => String(),
                        'args' => [
                            'req1' => ['type' => newNonNull(Int())],
                            'req2' => ['type' => newNonNull(Int())],
                        ],
                    ],
                    'multipleOpts'              => [
                        'type' => String(),
                        'args' => [
                            'opt1' => ['type' => Int(), 'defaultValue' => 0],
                            'opt2' => ['type' => Int(), 'defaultValue' => 0],
                        ],
                    ],
                    'multipleOptsAndReq'        => [
                        'type' => String(),
                        'args' => [
                            'req1' => ['type' => newNonNull(Int())],
                            'req2' => ['type' => newNonNull(Int())],
                            'opt1' => ['type' => Int(), 'defaultValue' => 0],
                            'opt2' => ['type' => Int(), 'defaultValue' => 0],
                        ],
                    ],
                ];
            },
        ]);
}

function InvalidScalar(): ScalarType
{
    static $instance = null;
    return $instance ??
        $instance = newScalarType([
            'name'         => 'Invalid',
            'serialize'    => function ($value) {
                return $value;
            },
            'parseLiteral' => function ($node) {
                throw new \Exception(sprintf('Invalid scalar is always invalid: %s', $node->getValue()));
            },
            'parseValue'   => function ($value) {
                throw new \Exception(sprintf('Invalid scalar is always invalid: %s', $value));
            },
        ]);
}

function AnyScalar(): ScalarType
{
    static $instance = null;
    return $instance ??
        $instance = newScalarType([
            'name'         => 'Any',
            'serialize'    => function ($value) {
                return $value;
            },
            'parseLiteral' => function ($node) {
                return $node;
            },
            'parseValue'   => function ($value) {
                return $value;
            },
        ]);
}

function QueryRoot(): ObjectType
{
    static $instance = null;
    return $instance ??
        $instance = newObjectType([
            'name'   => 'QueryRoot',
            'fields' => function () {
                return [
                    'human'           => [
                        'args' => ['id' => ['type' => ID()]],
                        'type' => Human(),
                    ],
                    'alien'           => ['type' => Alien()],
                    'dog'             => ['type' => Dog()],
                    'cat'             => ['type' => Cat()],
                    'pet'             => ['type' => Pet()],
                    'catOrDog'        => ['type' => CatOrDog()],
                    'dogOrHuman'      => ['type' => DogOrHuman()],
                    'humanOrAlien'    => ['type' => HumanOrAlien()],
                    'complicatedArgs' => ['type' => ComplicatedArgs()],
                    'invalidArg'      => [
                        'args' => ['arg' => ['type' => InvalidScalar()]],
                        'type' => String(),
                    ],
                    'anyArg'          => [
                        'args' => ['arg' => ['type' => AnyScalar()]],
                        'type' => String(),
                    ],
                ];
            },
        ]);
}

/**
 * @return Schema
 */
function testSchema(): Schema
{
    return newSchema([
        'query'      => QueryRoot(),
        'types'      => [Cat(), Dog(), Human(), Alien()],
        'directives' => [
            IncludeDirective(),
            SkipDirective(),
            newDirective([
                'name'      => 'onQuery',
                'locations' => ['QUERY'],
            ]),
            newDirective([
                'name'      => 'onMutation',
                'locations' => ['MUTATION'],
            ]),
            newDirective([
                'name'      => 'onSubscription',
                'locations' => ['SUBSCRIPTION'],
            ]),
            newDirective([
                'name'      => 'onField',
                'locations' => ['FIELD'],
            ]),
            newDirective([
                'name'      => 'onFragmentDefinition',
                'locations' => ['FRAGMENT_DEFINITION'],
            ]),
            newDirective([
                'name'      => 'onFragmentSpread',
                'locations' => ['FRAGMENT_SPREAD'],
            ]),
            newDirective([
                'name'      => 'onInlineFragment',
                'locations' => ['INLINE_FRAGMENT'],
            ]),
            newDirective([
                'name'      => 'onSchema',
                'locations' => ['SCHEMA'],
            ]),
            newDirective([
                'name'      => 'onScalar',
                'locations' => ['SCALAR'],
            ]),
            newDirective([
                'name'      => 'onObject',
                'locations' => ['OBJECT'],
            ]),
            newDirective([
                'name'      => 'onFieldDefinition',
                'locations' => ['FIELD_DEFINITION'],
            ]),
            newDirective([
                'name'      => 'onArgumentDefinition',
                'locations' => ['ARGUMENT_DEFINITION'],
            ]),
            newDirective([
                'name'      => 'onInterface',
                'locations' => ['INTERFACE'],
            ]),
            newDirective([
                'name'      => 'onUnion',
                'locations' => ['UNION'],
            ]),
            newDirective([
                'name'      => 'onEnum',
                'locations' => ['ENUM'],
            ]),
            newDirective([
                'name'      => 'onEnumValue',
                'locations' => ['ENUM_VALUE'],
            ]),
            newDirective([
                'name'      => 'onInputObject',
                'locations' => ['INPUT_OBJECT'],
            ]),
            newDirective([
                'name'      => 'onInputFieldDefinition',
                'locations' => ['INPUT_FIELD_DEFINITION'],
            ]),
        ],
    ]);
}
