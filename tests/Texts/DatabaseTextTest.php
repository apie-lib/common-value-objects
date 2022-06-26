<?php
namespace Apie\Tests\CommonValueObjects\Texts;

use Apie\CommonValueObjects\Texts\DatabaseText;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\TestCase;

class DatabaseTextTest extends TestCase
{
    use TestWithFaker;
    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = DatabaseText::fromNative($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function it_allows_all_strings_that_are_not_too_long(string $expected, string $input)
    {
        $testItem = new DatabaseText($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield ['', '    '];
        yield ['', str_repeat(' ', 70000)];
        yield ['test', 'test'];
        yield ['trimmed', '   trimmed   '];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_strings_that_are_too_long(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new DatabaseText($input);
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_strings_that_are_too_long_with_fromNative(string $input)
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        DatabaseText::fromNative($input);
    }

    public function invalidProvider()
    {
        yield [str_repeat('1', '70000')];
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(DatabaseText::class);
    }
}