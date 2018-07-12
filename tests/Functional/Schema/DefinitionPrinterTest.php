<?php

namespace Digia\GraphQL\Test\Functional\Schema;

use Digia\GraphQL\Schema\DefinitionPrinter;
use Digia\GraphQL\Test\TestCase;
use function Digia\GraphQL\buildSchema;
use function Digia\GraphQL\Language\dedent;

/**
 * Class DefinitionPrinterTest
 * @package Digia\GraphQL\Test\Functional\Schema
 */
class DefinitionPrinterTest extends TestCase
{

    /**
     * @param string $source
     *
     * @dataProvider printStringFieldsDataProvider
     *
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintStringFields(string $source): void
    {
        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @return array
     */
    public function printStringFieldsDataProvider(): array
    {
        return [
            // String field
            [
                dedent('
                type Query {
                  singleField: String
                }
              ')
            ],
            // [String] field
            [
                dedent('
                type Query {
                  singleField: [String]
                }
              ')
            ],
            // String! field
            [
                dedent('
                type Query {
                  singleField: String!
                }
              ')
            ],
            // [String]! field
            [
                dedent('
                type Query {
                  singleField: [String]!
                }
              ')
            ],
            // [String!] field
            [
                dedent('
                type Query {
                  singleField: [String!]
                }
              ')
            ],
            // [String!]! field
            [
                dedent('
                type Query {
                  singleField: [String!]!
                }
              ')
            ],
            // String field with Int argument
            [
                dedent('
                type Query {
                  singleField(argOne: Int): String
                }
                '),
            ],
            // String field with Int argument with default value
            [
                dedent('
                type Query {
                  singleField(argOne: Int = 2): String
                }
                '),
            ],
            // String field with String argument with default value
            // TODO: Make this data set work
//            [
//                dedent('
//                type Query {
//                  singleField(argOne: String = "tes\t de\fault"): String
//                }
//                '),
//            ],
            // String field with Int argument with default null
            // TODO: Make this data set work, needs https://github.com/digiaonline/graphql-php/issues/239
//            [
//                dedent('
//                type Query {
//                  singleField(argOne: Int = null): String
//                }
//                '),
//            ],
            // String field with Int! argument
            // TODO: Make this data set work
//            [
//                dedent('
//                type Query {
//                  singleField(argOne: Int!): String
//                }
//                '),
//            ],
            // String field with multiple arguments
            [
                dedent('
                type Query {
                  singleField(argOne: Int, argTwo: String): String
                }
                '),
            ],
            // String field with multiple arguments, first is default
            [
                dedent('
                type Query {
                  singleField(argOne: Int = 1, argTwo: String, argThree: Boolean): String
                }
                '),
            ],
            // String field with multiple arguments, second is default
            [
                dedent('
                type Query {
                  singleField(argOne: Int, argTwo: String = "foo", argThree: Boolean): String
                }
                '),
            ],
            // String field with multiple arguments, last is default
            [
                dedent('
                type Query {
                  singleField(argOne: Int, argTwo: String, argThree: Boolean = false): String
                }
                '),
            ],
        ];
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintObjectFields(): void
    {
        $source = dedent('
                type Foo {
                  str: String
                }
                
                type Query {
                  foo: Foo
                }
              ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintCustomRootQuery(): void
    {
        $source = dedent('
                schema {
                  query: CustomQueryType
                }
                
                type CustomQueryType {
                  bar: String
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintInterfaces(): void
    {
        // Single interface
        $source = dedent('
                type Bar implements Foo {
                  str: String
                }
                
                interface Foo {
                  str: String
                }
                
                type Query {
                  bar: Bar
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);

        // Multiple interfaces
        $source = dedent('
                interface Baaz {
                  int: Int
                }
                
                type Bar implements Foo & Baaz {
                  str: String
                  int: Int
                }
                
                interface Foo {
                  str: String
                }
                
                type Query {
                  bar: Bar
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintUnions(): void
    {
        $source = dedent('
                type Bar {
                  str: String
                }
                
                type Foo {
                  bool: Boolean
                }
                
                union MultipleUnion = Foo | Bar
                
                type Query {
                  single: SingleUnion
                  multiple: MultipleUnion
                }
                
                union SingleUnion = Foo
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintInputType(): void
    {
        $source = dedent('
                input InputType {
                  int: Int
                }
                
                type Query {
                  str(argOne: InputType): String
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintCustomScalar(): void
    {
        $source = dedent('
                scalar Odd
                
                type Query {
                  odd: Odd
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintEnum(): void
    {
        $source = dedent('
                type Query {
                  rgb: RGB
                }
                
                enum RGB {
                  RED
                  GREEN
                  BLUE
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     *
     * TODO: Add support for printing directives first
     */
    public function testPrintCustomDirectives(): void
    {
        $this->markTestIncomplete('Missing support for printing directives');

        $source = dedent('
                directive @customDirective on FIELD
                
                type Query {
                  field: String
                }
                ');

        $this->assertPrintedSchemaEqualsBuiltSchema($source);
    }

    // TODO: Add tests for description printing, it's currently quite broken so no point in writing the tests yet

    /**
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    public function testPrintIntrospectionSchema(): void
    {
        $this->markTestIncomplete('Broken because missing support for printing directives');

        $source = dedent('
                 """
                 Directs the executor to include this field or fragment only when the \`if\` argument is true.
                 """
                 directive @include(
                   """Included when true."""
                   if: Boolean!
                 ) on FIELD | FRAGMENT_SPREAD | INLINE_FRAGMENT
                 """
                 Directs the executor to skip this field or fragment when the \`if\` argument is true.
                 """
                 directive @skip(
                   """Skipped when true."""
                   if: Boolean!
                 ) on FIELD | FRAGMENT_SPREAD | INLINE_FRAGMENT
                 """Marks an element of a GraphQL schema as no longer supported."""
                 directive @deprecated(
                   """
                   Explains why this element was deprecated, usually also including a suggestion
                   for how to access supported similar data. Formatted in
                   [Markdown](https://daringfireball.net/projects/markdown/).
                   """
                   reason: String = "No longer supported"
                 ) on FIELD_DEFINITION | ENUM_VALUE
                 """
                 A Directive provides a way to describe alternate runtime execution and type validation behavior in a GraphQL document.
                 In some cases, you need to provide options to alter GraphQL\'s execution behavior
                 in ways field arguments will not suffice, such as conditionally including or
                 skipping a field. Directives provide this by describing additional information
                 to the executor.
                 """
                 type __Directive {
                   name: String!
                   description: String
                   locations: [__DirectiveLocation!]!
                   args: [__InputValue!]!
                 }
                 """
                 A Directive can be adjacent to many parts of the GraphQL language, a
                 __DirectiveLocation describes one such possible adjacencies.
                 """
                 enum __DirectiveLocation {
                   """Location adjacent to a query operation."""
                   QUERY
                   """Location adjacent to a mutation operation."""
                   MUTATION
                   """Location adjacent to a subscription operation."""
                   SUBSCRIPTION
                   """Location adjacent to a field."""
                   FIELD
                   """Location adjacent to a fragment definition."""
                   FRAGMENT_DEFINITION
                   """Location adjacent to a fragment spread."""
                   FRAGMENT_SPREAD
                   """Location adjacent to an inline fragment."""
                   INLINE_FRAGMENT
                   """Location adjacent to a schema definition."""
                   SCHEMA
                   """Location adjacent to a scalar definition."""
                   SCALAR
                   """Location adjacent to an object type definition."""
                   OBJECT
                   """Location adjacent to a field definition."""
                   FIELD_DEFINITION
                   """Location adjacent to an argument definition."""
                   ARGUMENT_DEFINITION
                   """Location adjacent to an interface definition."""
                   INTERFACE
                   """Location adjacent to a union definition."""
                   UNION
                   """Location adjacent to an enum definition."""
                   ENUM
                   """Location adjacent to an enum value definition."""
                   ENUM_VALUE
                   """Location adjacent to an input object type definition."""
                   INPUT_OBJECT
                   """Location adjacent to an input object field definition."""
                   INPUT_FIELD_DEFINITION
                 }
                 """
                 One possible value for a given Enum. Enum values are unique values, not a
                 placeholder for a string or numeric value. However an Enum value is returned in
                 a JSON response as a string.
                 """
                 type __EnumValue {
                   name: String!
                   description: String
                   isDeprecated: Boolean!
                   deprecationReason: String
                 }
                 """
                 Object and Interface types are described by a list of Fields, each of which has
                 a name, potentially a list of arguments, and a return type.
                 """
                 type __Field {
                   name: String!
                   description: String
                   args: [__InputValue!]!
                   type: __Type!
                   isDeprecated: Boolean!
                   deprecationReason: String
                 }
                 """
                 Arguments provided to Fields or Directives and the input fields of an
                 InputObject are represented as Input Values which describe their type and
                 optionally a default value.
                 """
                 type __InputValue {
                   name: String!
                   description: String
                   type: __Type!
                   """
                   A GraphQL-formatted string representing the default value for this input value.
                   """
                   defaultValue: String
                 }
                 """
                 A GraphQL Schema defines the capabilities of a GraphQL server. It exposes all
                 available types and directives on the server, as well as the entry points for
                 query, mutation, and subscription operations.
                 """
                 type __Schema {
                   """A list of all types supported by this server."""
                   types: [__Type!]!
                   """The type that query operations will be rooted at."""
                   queryType: __Type!
                   """
                   If this server supports mutation, the type that mutation operations will be rooted at.
                   """
                   mutationType: __Type
                   """
                   If this server support subscription, the type that subscription operations will be rooted at.
                   """
                   subscriptionType: __Type
                   """A list of all directives supported by this server."""
                   directives: [__Directive!]!
                 }
                 """
                 The fundamental unit of any GraphQL Schema is the type. There are many kinds of
                 types in GraphQL as represented by the \`__TypeKind\` enum.
                 Depending on the kind of a type, certain fields describe information about that
                 type. Scalar types provide no information beyond a name and description, while
                 Enum types provide their values. Object and Interface types provide the fields
                 they describe. Abstract types, Union and Interface, provide the Object types
                 possible at runtime. List and NonNull types compose other types.
                 """
                 type __Type {
                   kind: __TypeKind!
                   name: String
                   description: String
                   fields(includeDeprecated: Boolean = false): [__Field!]
                   interfaces: [__Type!]
                   possibleTypes: [__Type!]
                   enumValues(includeDeprecated: Boolean = false): [__EnumValue!]
                   inputFields: [__InputValue!]
                   ofType: __Type
                 }
                 """An enum describing what kind of type a given \`__Type\` is."""
                 enum __TypeKind {
                   """Indicates this type is a scalar."""
                   SCALAR
                   """
                   Indicates this type is an object. \`fields\` and \`interfaces\` are valid fields.
                   """
                   OBJECT
                   """
                   Indicates this type is an interface. \`fields\` and \`possibleTypes\` are valid fields.
                   """
                   INTERFACE
                   """Indicates this type is a union. \`possibleTypes\` is a valid field."""
                   UNION
                   """Indicates this type is an enum. \`enumValues\` is a valid field."""
                   ENUM
                   """
                   Indicates this type is an input object. \`inputFields\` is a valid field.
                   """
                   INPUT_OBJECT
                   """Indicates this type is a list. \`ofType\` is a valid field."""
                   LIST
                   """Indicates this type is a non-null. \`ofType\` is a valid field."""
                   NON_NULL
                 }
                ');

        $schema = buildSchema($source);

        $printer = new DefinitionPrinter();

        $this->assertEquals($source, $printer->printIntrospectionSchema($schema));
    }

    /**
     * @param string $source
     * @throws \Digia\GraphQL\Error\InvariantException
     * @throws \Digia\GraphQL\Error\PrintException
     */
    private function assertPrintedSchemaEqualsBuiltSchema(string $source): void
    {
        $schema = buildSchema($source);

        $printer = new DefinitionPrinter();

        $this->assertEquals($source, $printer->printSchema($schema));
    }
}
