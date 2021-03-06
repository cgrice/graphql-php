<?php

namespace Digia\GraphQL\Test\Unit\Util;

use Digia\GraphQL\Error\ConversionException;
use Digia\GraphQL\Language\Node\EnumValueNode;
use Digia\GraphQL\Language\Node\IntValueNode;
use Digia\GraphQL\Language\Node\ListValueNode;
use Digia\GraphQL\Language\Node\NameNode;
use Digia\GraphQL\Language\Node\NullValueNode;
use Digia\GraphQL\Language\Node\ObjectFieldNode;
use Digia\GraphQL\Language\Node\ObjectValueNode;
use Digia\GraphQL\Language\Node\StringValueNode;
use Digia\GraphQL\Language\Node\VariableNode;
use Digia\GraphQL\Test\TestCase;
use Digia\GraphQL\Util\ValueASTConverter;
use Digia\GraphQL\Util\ValueHelper;
use function Digia\GraphQL\Type\Int;
use function Digia\GraphQL\Type\newEnumType;
use function Digia\GraphQL\Type\newInputObjectType;
use function Digia\GraphQL\Type\newList;
use function Digia\GraphQL\Type\newNonNull;
use function Digia\GraphQL\Type\String;

class ValueASTConverterTest extends TestCase
{
    /**
     * @var ValueASTConverter
     */
    protected $converter;

    public function setUp()
    {
        $this->converter = new ValueASTConverter();
    }

    public function testConvertNull()
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Node is not defined.');
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->converter->convert(null, String());
    }

    public function testConvertNonNullWithStringValue()
    {
        $node = new StringValueNode('foo', false, null);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals('foo', $this->converter->convert($node, newNonNull(String())));
    }

    public function testConvertNonNullWithNullValue()
    {
        $node = new NullValueNode(null);
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Cannot convert non-null values from null value node');
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(null, $this->converter->convert($node, newNonNull(String())));
    }

    public function testConvertValidListOfStrings()
    {
        $node = new ListValueNode(
            [
                new StringValueNode('A', false, null),
                new StringValueNode('B', false, null),
                new StringValueNode('C', false, null),
            ],
            null
        );
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(['A', 'B', 'C'], $this->converter->convert($node, newList(String())));
    }

    public function testConvertListWithMissingVariableValue()
    {
        $node = new ListValueNode(
            [
                new VariableNode(new NameNode('$a', null), null),
                new VariableNode(new NameNode('$b', null), null),
                new VariableNode(new NameNode('$c', null), null),
            ],
            null
        );
        // Null-able inputs in a variable can be omitted
        $variables = ['$a' => 'A', '$c' => 'C'];
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(['A', null, 'C'], $this->converter->convert($node, newList(String()), $variables));
    }

    public function testConvertValidInputObject()
    {
        $node = new ObjectValueNode(
            [new ObjectFieldNode(new NameNode('a', null), new IntValueNode(1, null), null)],
            null
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $type = newInputObjectType([
            'name'   => 'InputObject',
            'fields' => [
                'a' => ['type' => Int()],
            ],
        ]);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(['a' => 1], $this->converter->convert($node, $type));
    }

    public function testConvertInputObjectWithNodeOfInvalidType()
    {
        $node = new StringValueNode(null, false, null);

        /** @noinspection PhpUnhandledExceptionInspection */
        $type = newInputObjectType([
            'name'   => 'InputObject',
            'fields' => [
                'a' => ['type' => Int()],
            ],
        ]);

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Input object values can only be converted form object value nodes.');
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(1, $this->converter->convert($node, $type));
    }

    public function testConvertInputObjectWithMissingNonNullField()
    {
        $node = new ObjectValueNode(
            [new ObjectFieldNode(new NameNode('a', null), new IntValueNode(1, null), null)],
            null
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $type = newInputObjectType([
            'name'   => 'InputObject',
            'fields' => [
                'a' => ['type' => Int()],
                'b' => ['type' => newNonNull(String())],
            ],
        ]);

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Cannot convert input object value for missing non-null field.');
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(['a' => 1], $this->converter->convert($node, $type));
    }

    public function testConvertEnumWithIntValue()
    {
        $node = new EnumValueNode('FOO', null);
        /** @noinspection PhpUnhandledExceptionInspection */
        $type = newEnumType([
            'name'   => 'EnumType',
            'values' => [
                'FOO' => ['value' => 1],
            ],
        ]);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(1, $this->converter->convert($node, $type));
    }

    public function testConvertEnumWithNodeOfInvalidType()
    {
        $node = new StringValueNode(null, false, null);
        $type = newEnumType([
            'name' => 'EnumType',
        ]);

        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Enum values can only be converted from enum value nodes.');
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(1, $this->converter->convert($node, $type));
    }

    public function testConvertEnumWithMissingValue()
    {
        $node = new EnumValueNode('FOO', null);
        /** @noinspection PhpUnhandledExceptionInspection */
        $type = newEnumType([
            'name'   => 'EnumType',
            'values' => [
                'BAR' => ['value' => 'foo'],
            ],
        ]);
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage('Cannot convert enum value for missing value "FOO".');
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals(1, $this->converter->convert($node, $type));
    }

    public function testConvertValidScalar()
    {
        $node = new StringValueNode('foo', false, null);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals('foo', $this->converter->convert($node, String()));
    }

    public function testConvertInvalidScalar()
    {
        $node = new StringValueNode(null, false, null);

        $this->expectException(ConversionException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->assertEquals('foo', $this->converter->convert($node, String()));
    }
}
