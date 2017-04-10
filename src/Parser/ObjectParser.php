<?php
namespace MetaHydrator\Parser;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\Exception\ParsingException;
use MetaHydrator\Handler\HydratingHandlerInterface;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Validator\ValidatorInterface;
use Mouf\Hydrator\Hydrator;
use Mouf\Hydrator\TdbmHydrator;

/**
 * A custom class parser based on the MetaHydrator behaviour
 *
 * Class ObjectParser
 * @package MetaHydrator\Parser
 */
class ObjectParser extends MetaHydrator implements ParserInterface
{
    /** @var string */
    private $className;

    /** @var mixed */
    private $errorMessage;

    /**
     * ObjectParser constructor.
     * @param string $className
     * @param HydratingHandlerInterface[] $handlers
     * @param ValidatorInterface[] $validators
     * @param mixed $errorMessage
     * @param Hydrator $simpleHydrator
     */
    public function __construct($className, $handlers = [], $validators = [], $errorMessage = "", $simpleHydrator = null)
    {
        parent::__construct($handlers, $validators, $simpleHydrator);
        $this->className = $className;
        $this->errorMessage = $errorMessage;
    }


    /**
     * @param $rawValue
     * @return mixed
     *
     * @throws ParsingException
     */
    public function parse($rawValue)
    {
        if ($rawValue === null) {
            return null;
        }
        if (!is_array($rawValue)) {
            throw new ParsingException($this->errorMessage);
        }

        try {
            return $this->hydrateNewObject($rawValue, $this->className);
        } catch (HydratingException $exception) {
            throw new ParsingException($exception->getErrorsMap());
        }
    }
}