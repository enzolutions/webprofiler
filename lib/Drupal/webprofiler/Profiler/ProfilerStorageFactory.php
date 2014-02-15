<?php

namespace Drupal\webprofiler\Profiler;

use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Profiler\ProfilerStorageInterface;

class ProfilerStorageFactory {

  /**
   * @param ConfigFactory $config
   * @param ContainerInterface $container
   *
   * @return ProfilerStorageInterface
   */
  final public static function getProfilerStorage(ConfigFactory $config, ContainerInterface $container) {
    $storage = $config->get('webprofiler.config')->get('storage');

    return $container->get($storage);
  }

}