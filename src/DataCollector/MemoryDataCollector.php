<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drupal\webprofiler\DataCollector;

use Drupal\webprofiler\DrupalDataCollectorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

/**
 * MemoryDataCollector
 */
class MemoryDataCollector extends DataCollector implements LateDataCollectorInterface, DrupalDataCollectorInterface {

  use StringTranslationTrait, DrupalDataCollectorTrait;

  /**
   * {@inheritdoc}
   */
  public function getMenu() {
    return NULL;
  }

  public function __construct() {
    $this->data = array(
      'memory' => 0,
      'memory_limit' => $this->convertToBytes(ini_get('memory_limit')),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Exception $exception = NULL) {
    $this->updateMemoryUsage();
  }

  /**
   * {@inheritdoc}
   */
  public function lateCollect() {
    $this->updateMemoryUsage();
  }

  /**
   * Gets the memory.
   *
   * @return integer The memory
   */
  public function getMemory() {
    return $this->data['memory'];
  }

  /**
   * Gets the PHP memory limit.
   *
   * @return integer The memory limit
   */
  public function getMemoryLimit() {
    return $this->data['memory_limit'];
  }

  /**
   * Updates the memory usage data.
   */
  public function updateMemoryUsage() {
    $this->data['memory'] = memory_get_peak_usage(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'memory';
  }

  private function convertToBytes($memoryLimit) {
    if ('-1' === $memoryLimit) {
      return -1;
    }

    $memoryLimit = strtolower($memoryLimit);
    $max = strtolower(ltrim($memoryLimit, '+'));
    if (0 === strpos($max, '0x')) {
      $max = intval($max, 16);
    }
    elseif (0 === strpos($max, '0')) {
      $max = intval($max, 8);
    }
    else {
      $max = intval($max);
    }

    switch (substr($memoryLimit, -1)) {
      case 't':
        $max *= 1024;
      case 'g':
        $max *= 1024;
      case 'm':
        $max *= 1024;
      case 'k':
        $max *= 1024;
    }

    return $max;
  }
}
