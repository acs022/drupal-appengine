<?php

/**
 * @file
 * Definition of Drupal\Core\DrupalKernel.
 */

namespace Drupal\Core;

use Drupal\Core\DrupalBundle;
use Symfony\Component\HttpKernel\Kernel;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * The DrupalKernel class is the core of Drupal itself.
 */
class DrupalKernel extends Kernel {

    public function registerBundles()
    {
        $bundles = array(
            new DrupalBundle(),
        );
        $modules = array_keys(system_list('module_enabled'));
        foreach ($modules as $module) {
          $class = "\Drupal\{$module}\{$module}Bundle";
          if (class_exists($class)) {
            $bundles[] = new $class();
          }
        }
        return $bundles;
    }


    /**
     * Initializes the service container.
     */
    protected function initializeContainer()
    {
        $this->container = $this->buildContainer();
        $this->container->set('kernel', $this);
        drupal_container($this->container);
    }

    /**
     * Builds the service container.
     *
     * @return ContainerBuilder The compiled service container
     */
    protected function buildContainer()
    {
        $container = $this->getContainerBuilder();
        foreach ($this->bundles as $bundle) {
            $bundle->build($container);
        }
        $container->compile();
        return $container;
    }


    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     *
     * @return ContainerBuilder
     */
    protected function getContainerBuilder()
    {
        return new ContainerBuilder(new ParameterBag($this->getKernelParameters()));
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
      // We have to define this method because it's not defined in the base class,
      // but the LoaderInterface class is part of the config component, which we
      // are not using, so this is badness :-/ The alternative is to not extend
      // the base Kernel class and just implement the KernelInterface.
    }
}
