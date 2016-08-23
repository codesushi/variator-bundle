<?php

declare(strict_types=1);

namespace Coshi\VariatorBundle;

use Coshi\Variator\VariationFactory as BaseFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class VariationFactory extends BaseFactory implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
    * {@inheritdoc}
    */
    protected function getResolver()
    {
        return $this->container->get('coshi.variator_bundle.config_resolver');
    }
}
