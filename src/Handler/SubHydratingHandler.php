<?php
namespace MetaHydrator\Handler;

use MetaHydrator\Exception\HydratingException;
use MetaHydrator\MetaHydrator;
use MetaHydrator\Reflection\Getter;
use MetaHydrator\Reflection\GetterInterface;
use MetaHydrator\Validator\ValidatorInterface;
use Mouf\Hydrator\Hydrator;

/**
 * An implementation of HydratingHandlerInterface aiming to manage partial edition of sub-objects
 *
 * Class SubHydratingHandler
 * @package MetaHydrator\Handler
 */
class SubHydratingHandler extends MetaHydrator implements HydratingHandlerInterface
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $className;

    /** @var mixed */
    protected $errorMessage;

    /** @var GetterInterface */
    protected $getter;

    /**
     * SubHydratingHandler constructor.
     * @param string $key
     * @param string $className
     * @param HydratingHandlerInterface[] $handlers
     * @param ValidatorInterface[] $validators
     * @param mixed $errorMessage
     * @param Hydrator $simpleHydrator
     * @param GetterInterface $getter
     */
    public function __construct(string $key, string $className, $handlers, array $validators = [], $errorMessage = null, Hydrator $simpleHydrator = null, GetterInterface $getter = null)
    {
        parent::__construct($handlers, $validators, $simpleHydrator);
        $this->key = $key;
        $this->className = $className;
        $this->errorMessage = $errorMessage;
        $this->getter = $getter ?? $this->defaultGetter();
    }

    /**
     * @param array $data
     * @param array $targetData
     * @param $object
     *
     * @throws HydratingException
     */
    public function handle(array $data, array &$targetData, $object = null)
    {
        $subData = $this->getSubData($data, $object);
        if ($subData === null) {
            return;
        }
        try {
            $subObject = $this->getSubObject($object);
            if ($subObject !== null) {
                $this->hydrateObject($subData, $subObject);
            } else {
                $targetData[$this->key] = $this->hydrateNewObject($subData, $this->className);
            }
        } catch (HydratingException $exception) {
            throw new HydratingException([$this->key => $exception->getErrorsMap()]);
        }
    }

    /**
     * @param $object
     * @return mixed
     */
    protected function getSubObject($object)
    {
        if ($object === null) {
            return null;
        }
        try {
            return $this->getter->get($object, $this->key);
        } catch (\ReflectionException $exception) {
            return null;
        }
    }

    /**
     * @param $data
     * @param $object
     * @return array
     * @throws HydratingException
     */
    protected function getSubData($data, $object)
    {
        if (array_key_exists($this->key, $data)) {
            $subData = $data[$this->key];
            if (!is_array($subData)) {
                throw new HydratingException([$this->key => $this->errorMessage]);
            } else {
                return $subData;
            }
        } else if ($object === null) {
            return [];
        } else {
            return null;
        }
    }

    /**
     * @return GetterInterface
     */
    private function defaultGetter()
    {
        return new Getter(false);
    }
}
