<?php
declare(strict_types=1);

namespace Coshi\VariatorBundle;

use Coshi\Variator\ConfigResolver as BaseResolver;
use Coshi\Variator\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ConfigResolver extends BaseResolver implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param array $config
     *
     * @return mixed
     *
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     * @throws InvalidConfigurationException
     */
    protected function resolveInstance(array $config)
    {
        $class = $config[0];

        if (class_exists($class)) {
            return parent::resolveInstance($config);
        }

        if (0 !== strpos($class, '@')) {
            throw new InvalidConfigurationException(sprintf('String "%s" doesn\'t match service definition', $class));
        }

        return $this->container->get(substr($class, 1));
    }
}
