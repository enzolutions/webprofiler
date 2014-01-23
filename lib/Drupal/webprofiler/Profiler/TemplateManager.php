<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drupal\webprofiler\Profiler;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpKernel\Profiler\Profile;

/**
 * Profiler Templates Manager
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Artur Wielogórski <wodor@wodor.net>
 */
class TemplateManager {
  protected $twig;
  protected $twigLoader;
  protected $templates;
  protected $profiler;

  /**
   * Constructor.
   *
   * @param Profiler $profiler
   * @param \Twig_Environment $twig
   * @param \Twig_Loader_Filesystem $twigLoader
   * @param array $templates
   */
  public function __construct(Profiler $profiler, \Twig_Environment $twig, \Twig_Loader_Filesystem $twigLoader, array $templates) {
    $this->profiler = $profiler;
    $this->twig = $twig;
    $this->twigLoader = $twigLoader;
    $this->templates = $templates;
  }

  /**
   * Gets the template name for a given panel.
   *
   * @param Profile $profile
   * @param string $panel
   *
   * @return mixed
   *
   * @throws NotFoundHttpException
   */
  public function getName(Profile $profile, $panel) {
    $templates = $this->getNames($profile);

    if (!isset($templates[$panel])) {
      throw new NotFoundHttpException(sprintf('Panel "%s" is not registered in profiler or is not present in viewed profile.', $panel));
    }

    return $templates[$panel];
  }

  /**
   * Gets the templates for a given profile.
   *
   * @param Profile $profile
   *
   * @return array
   */
  public function getTemplates(Profile $profile) {
    $templates = $this->getNames($profile);
    foreach ($templates as $name => $template) {
      $templates[$name] = $this->twig->loadTemplate($template);
    }

    return $templates;
  }

  /**
   * Gets template names of templates that are present in the viewed profile.
   *
   * @param Profile $profile
   *
   * @return array
   *
   * @throws \UnexpectedValueException
   */
  protected function getNames(Profile $profile) {
    $templates = array();

    // TODO remove this when https://drupal.org/node/2143557 comes in.
    $this->twigLoader->addPath(drupal_get_path('module', 'webprofiler') . '/templates', 'webprofiler');

    foreach ($this->templates as $arguments) {
      if (NULL === $arguments) {
        continue;
      }

      list($name, $template) = $arguments;

      if (!$this->profiler->has($name) || !$profile->hasCollector($name)) {
        continue;
      }

      if ('.html.twig' === substr($template, -10)) {
        $template = substr($template, 0, -10);
      }

      if (!$this->templateExists($template . '.html.twig')) {
        throw new \UnexpectedValueException(sprintf('The profiler template "%s.html.twig" for data collector "%s" does not exist.', $template, $name));
      }

      $templates[$name] = $template . '.html.twig';
    }

    return $templates;
  }

  // TODO to be removed when the minimum required version of Twig is >= 2.0
  protected function templateExists($template) {
    $loader = $this->twig->getLoader();
    if ($loader instanceof \Twig_ExistsLoaderInterface) {
      return $loader->exists($template);
    }

    try {
      $loader->getSource($template);

      return TRUE;
    } catch (\Twig_Error_Loader $e) {
    }

    return FALSE;
  }
}