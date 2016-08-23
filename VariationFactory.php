<?php

declare(strict_types=1);

namespace Codesushi\VariatorBundle;

use Codesushi\Variator\VariationFactory as BaseFactory;
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
        return $this->container->get('codesushi.variator_bundle.config_resolver');
    }
}
