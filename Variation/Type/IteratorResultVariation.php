<?php
declare(strict_types=1);

namespace Codesushi\VariatorBundle\Variation\Type;

use Codesushi\Variator\ConfigResolver;
use Codesushi\Variator\Variation\AbstractVariation;

class IteratorResultVariation extends AbstractVariation
{
    /**
     * @var \Iterator
     */
    protected $result;

    /**
     * @var string
     */
    protected $castType;

    /**
     * @var array
     */
    protected $callback;

    /**
     * @var bool
     */
    protected $chunked = false;

    /**
     * @var int
     */
    protected $key = 0;

    /**
     * @var int
     */
    protected $chunkSize;

    public function __construct(string $name, array $parameters, ConfigResolver $configResolver)
    {
        parent::__construct($name, $parameters, $configResolver);
        $this->chunked = isset($parameters['chunked']) && true === $parameters['chunked'];
        $this->callback = $configResolver->resolveCallback($parameters['callback']);

        if ($this->chunked) {
            $this->chunkSize = (int)$parameters['chunk_size'];
            $this->callback['arguments']['limit'] = $this->chunkSize;
            $this->callback['arguments']['offset'] = 0;
        }

        if (isset($parameters['cast_type'])) {
            $this->castType = $parameters['cast_type'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrent()
    {
        $value = $this->result->current()[0][$this->name];

        if (null !== $this->castType) {
            settype($value, $this->castType);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function doRewind()
    {
        $this->key = 0;
        $this->fetchData();
        ++$this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchData()
    {
        if ($this->chunked) {
            $this->callback['arguments']['offset'] = $this->key;
        }
        $this->result = $this->configResolver->call($this->callback);
        $this->result->next();
    }

    /**
     * {@inheritdoc}
     */
    public function pushForward()
    {
        $this->result->next();

        if ($this->isValid()) {
            ++$this->key;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getType() : string
    {
        return 'iteratorResult';
    }

    /**
     * {@inheritdoc}
     */
    public function isValid() : bool
    {
        if ($this->chunked && !$this->result->valid()) {
            $this->fetchData();
        }

        return $this->result->valid();
    }

    /**
     * {@inheritdoc}
     */
    public static function validateParameters(array $parameters) : bool
    {
        if (isset($parameters['chunked']) && true === $parameters['chunked']) {
            if (!isset($parameters['chunk_size'])) {
                throw new \InvalidArgumentException('Chunk size is required if chunked is set to true.');
            }

            if (!is_numeric($parameters['chunk_size']) || $parameters['chunk_size'] < 1) {
                throw new \InvalidArgumentException('Chunk size should be numeric and greater than 0.');
            }
        }

        return true;
    }

    public static function validateValue($value) : bool
    {
        return true;
    }
}
