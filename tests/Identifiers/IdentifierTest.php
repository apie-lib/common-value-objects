<?php
namespace Apie\Tests\CommonValueObjects\Identifiers;

use Apie\CommonValueObjects\Identifiers\Identifier;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use PHPUnit\Framework\TestCase;

class IdentifierTest extends TestCase
{
    use TestWithOpenapiSchema;
    use TestWithFaker;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_many_names(string $expected, string $input)
    {
        $testItem = Identifier::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_many_names(string $expected, string $input)
    {
        $testItem = new Identifier($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['slug', 'slug'];
        yield ['short', 'short'];
        yield ['answer42', 'answer42'];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_identfiers(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new Identifier($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_identiifiers_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        Identifier::fromNative($input);
    }

    public function invalidProvider()
    {
        yield ['kebab-case-slug'];
        yield ['pascal_case_slug'];
        yield ['21jumpstreet'];
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(Identifier::class);
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            Identifier::class,
            'Identifier-post',
            [
                'type' => 'string',
                'format' => 'identifier',
                'pattern' => true,
            ]
        );
    }
}
