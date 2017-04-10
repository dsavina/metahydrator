<?php
namespace MetaHydratorTest\Parser;

use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Parser\ArrayParser;
use MetaHydrator\Parser\IntParser;
use MetaHydrator\Validator\NotEmptyValidator;

class ArrayParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArrayParser */
    private $parser;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->parser = new ArrayParser(new IntParser(), [new NotEmptyValidator()], 'This is not an array');
    }

    public function testParseValidArray()
    {
        try {
            $parsed = $this->parser->parse([
                13,
                '1',
                0
            ]);
            self::assertTrue($parsed == [13, 1, 0]);
        } catch (ParsingException $exception) {
            self::assertFalse(true);
        }
    }

    public function testParseInvalidType()
    {
        try {
            $parsed = $this->parser->parse('WRONG TYPE');
            self::assertFalse(true);
        } catch (ParsingException $exception) {
            self::assertTrue(true);
            self::assertTrue($exception->getInnerError() === 'This is not an array');
        }
    }
}
