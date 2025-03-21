<?php
namespace Apie\CommonValueObjects;

use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Generator;
use PHPUnit\Framework\TestCase;

class SafeHtmlTest extends TestCase
{
    use TestWithOpenapiSchema;
    use TestWithFaker;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_schema_generator()
    {
        $this->runOpenapiSchemaTestForCreation(
            SafeHtml::class,
            'SafeHtml-post',
            [
                'type' => 'string',
                'format' => 'safehtml',
            ]
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_refuses_large_html()
    {
        $this->expectException(InvalidStringForValueObjectException::class);
        new SafeHtml(str_repeat('aaa', 20 * 1024 * 1024));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidHTMLProvider')]
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sanitizes_html(string $expected, string $input)
    {
        $testItem = new SafeHtml($input);
        $this->assertEquals($expected, $testItem->toNative());
    }

    public static function invalidHTMLProvider(): Generator
    {
        yield 'malformed HTML' => [
            'this is a test<div>hi</div>',
            'this is a test<div>hi'
        ];
        yield 'script tag' => [
            'test',
            '<script>alert("hi");</script>test'
        ];
        yield 'img onerror XSS' => [
            '<img />',
            '<img src="error" onerror="alert(\'hi\');" />'
        ];
        yield 'external urls open in new window' => [
            '<a href="https://www.google.com" rel="noopener noreferrer" target="_blank">google.com</a>',
            '<a href="https://www.google.com">google.com</a>',
        ];
        yield 'script + text truncating' => [
            '',
            '<script>alert("HI");//' . str_repeat('a', 70000) . PHP_EOL . '</script>'
        ];
        yield 'underline text' => [
            '<span style="text-decoration-line:underline">Underline text</span>',
            '<span style="text-decoration-line:  underline;">Underline text</span>'
        ];
        yield 'striked text' => [
            '<span style="text-decoration-line:line-through">striked text</span>',
            '<span style="text-decoration-line:  line-through;">striked text</span>',
        ];
        yield 'colored text' => [
            '<span style="background-color: #424242;color: #111">striked text</span>',
            '<span style="background-color: #424242; color: #111">striked text</span>',
        ];
        yield 'unknown style' => [
            '<span>test</span>',
            '<span style="border: solid 1px">test</span>',
        ];
        yield 'Non breaking space' => [
            '<div>&nbsp;</div>',
            '<div>&nbsp;</div>'
        ];
        yield 'Exploitable wrong html' => [
            '<div></div>',
            '<div onload="xss()"></div>',
        ];
        yield 'Youtube movie' => [
            '<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/v1_EaU9YLaM?si&#61;edEJMdykcIsZp-Tl" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
            '<iframe width="560" height="315" src="https://www.youtube.com/embed/v1_EaU9YLaM?si=edEJMdykcIsZp-Tl" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
        ];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_indexes_html()
    {
        $testItem = new SafeHtml('<div>Test</div><p>Paragraph</p><h1>Header is important, right?</h1>');
        $this->assertEquals(
            [
                'test' => 1,
                'paragraph' => 1,
                'header' => 1,
                'is' => 1,
                'important' => 1,
                'right' => 1,
            ],
            $testItem->provideIndexes()
        );
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(SafeHtml::class);
    }
}
