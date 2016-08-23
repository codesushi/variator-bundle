<?php

namespace Codesushi\VariatorBundle\Tests;

use Codesushi\VariatorBundle\DependencyInjection\CodesushiVariatorExtension;
use Codesushi\Variator\Variation\VariationInterface;
use Codesushi\Variator\VariationsTreeBuilder;
use Codesushi\VariatorBundle\Tests\Fixtures\TestService;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestService
     */
    protected $service;

    /**
     * @var VariationsTreeBuilder
     */
    protected $variationsBuilder;

    public function setUp()
    {
        $container = new ContainerBuilder();
        $extension = new CodesushiVariatorExtension();
        $extension->load([], $container);
        $this->variationsBuilder = $container->get('codesushi.variator_bundle.builder');
        $this->service = new TestService();
        $container->set('simple_test_service', $this->service);
    }

    public function testSingleSimpleServiceCallbackWithArguments()
    {
        for ($i=0;$i<=2;$i++) {
            $variator = $this->variationsBuilder->build([
                'text' => [
                    'type' => 'callback',
                    'callback' => ['@simple_test_service', 'getSomeValuesByParameter', [$i]],
                ]
            ]);
            $expected = array_map(function ($value) {
                return ['text' => $value];
            }, $this->service->getSomeValuesByParameter($i));

            $this->runVariator($expected, $variator);
        }
    }

    private function runVariator(array $expected, VariationInterface $variation)
    {
        $keys = array_keys(reset($expected));

        foreach ($variation as $value) {
            ksort($value);

            foreach ($keys as $key) {
                static::assertArrayHasKey($key, $value);
            }
            static::assertTrue(in_array($value, $expected, true));
            unset($expected[array_search($value, $expected, true)]);
        }
        static::assertEmpty($expected);
    }
}
