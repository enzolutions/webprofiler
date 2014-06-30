<?php

/**
 * @file
 * Contains \Drupal\webprofiler\Form\PurgeForm.
 */

namespace Drupal\webprofiler\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBase;
use Drupal\webprofiler\DataCollector\DatabaseDataCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

/**
 * Class ProfilesFilterForm
 */
class ProfilesFilterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'webprofiler_profiles_filter';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $form['ip'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('IP'),
      '#size' => 30,
      '#default_value' => $this->getRequest()->query->get('ip'),
    );

    $form['url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Url'),
      '#size' => 30,
      '#default_value' => $this->getRequest()->query->get('url'),
    );

    $form['method'] = array(
      '#type' => 'select',
      '#title' => $this->t('Method'),
      '#options' => array('GET' => 'GET', 'POST' => 'POST'),
      '#default_value' => $this->getRequest()->query->get('method'),
    );

    $limits = array(10, 50, 100);
    $form['limit'] = array(
      '#type' => 'select',
      '#title' => $this->t('Limit'),
      '#options' => array_combine($limits, $limits),
      '#default_value' => $this->getRequest()->query->get('limit'),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['filter'] = array(
      '#type' => 'submit',
      '#value' => t('Filter'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $ip = $form_state['values']['ip'];
    $url = $form_state['values']['url'];
    $method = $form_state['values']['method'];
    $limit = $form_state['values']['limit'];

    $form_state['redirect'] = array(
      'admin/config/development/profiler/list',
      array(
        'query' => array(
          'ip' => $ip,
          'url' => $url,
          'method' => $method,
          'limit' => $limit,
        )
      )
    );
  }
}