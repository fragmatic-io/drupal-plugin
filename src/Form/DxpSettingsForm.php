<?php

namespace Drupal\dxp_utilities\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure dxp settings.
 */
class DxpSettingsForm extends ConfigFormBase {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->configManager = $container->get('config.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'dxp_utilities.middleware.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'middleware_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dxp_utilities.middleware.settings');
    $form['dxp_scope'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DXP Scope'),
      '#size' => 100,
      '#required' => TRUE,
      '#default_value' => $config->get('dxp_scope'),
    ];

    // $form['dxp_url'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('DXP URL'),
    //   '#size' => 100,
    //   '#required' => TRUE,
    //   '#default_value' => $config->get('dxp_url'),
    // ];

    $form['dxp_middleware_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Tracker URL'),
      '#size' => 100,
      '#required' => TRUE,
      '#default_value' => $config->get('dxp_middleware_url'),
    ];

    // $form['dxp_dashboard_url'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('DXP Dashboard URL'),
    //   '#size' => 100,
    //   '#required' => TRUE,
    //   '#default_value' => $config->get('dxp_dashboard_url'),
    // ];

    // $form['dxp_tags'] = [
    //   '#type' => 'textarea',
    //   '#title' => $this->t('DXP Tags'),
    //   '#size' => 100,
    //   '#default_value' => $config->get('dxp_tags'),
    //   '#description' => $this->t('Add multiple tags with pipe symble ( | ), ex: Florida | Travel | Holiday'),
    // ];

    // $form['dxp_categories'] = [
    //   '#type' => 'textarea',
    //   '#title' => $this->t('DXP Categories'),
    //   '#size' => 100,
    //   '#default_value' => $config->get('dxp_categories'),
    //   '#description' => $this->t('Add multiple categories with pipe symble ( | ), ex: Florida | Travel | Holiday'),
    // ];

    // $form['dxp_session_expiry'] = [
    //   '#type' => 'number',
    //   '#title' => $this->t('DXP Session Expiry'),
    //   '#size' => 100,
    //   '#default_value' => $config->get('dxp_session_expiry'),
    // ];

    // $form['dxp_consent_cookie_name'] = [
    //   '#type' => 'textfield',
    //   '#title' => $this->t('DXP Consent Cookie Name'),
    //   '#size' => 100,
    //   '#default_value' => $config->get('dxp_consent_cookie_name'),
    // ];

    // $form['dxp_consent_continent'] = [
    //   '#type' => 'textarea',
    //   '#title' => $this->t('DXP Consent Continent'),
    //   '#size' => 100,
    //   '#default_value' => $config->get('dxp_consent_continent'),
    //   '#description' => $this->t('Add consent continent with pipe symble ( | ), ex: * to include all or Europe | Asia'),
    // ];

    // $form['dxp_timeout_in_milliseconds'] = [
    //   '#type' => 'number',
    //   '#title' => $this->t('DXP Timeout in Milliseconds'),
    //   '#size' => 100,
    //   '#default_value' => $config->get('dxp_timeout_in_milliseconds'),
    // ];

    $form['dxp_prod'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('DXP Prod'),
      '#size' => 100,
      '#default_value' => $config->get('dxp_prod'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('dxp_utilities.middleware.settings')
      ->set('dxp_scope', $form_state->getValue('dxp_scope'))
      // ->set('dxp_url', $form_state->getValue('dxp_url'))
      ->set('dxp_middleware_url', $form_state->getValue('dxp_middleware_url'))
      // ->set('dxp_dashboard_url', $form_state->getValue('dxp_dashboard_url'))
      // ->set('dxp_tags', $form_state->getValue('dxp_tags'))
      // ->set('dxp_categories', $form_state->getValue('dxp_categories'))
      // ->set('dxp_session_expiry', $form_state->getValue('dxp_session_expiry'))
      // ->set('dxp_consent_cookie_name', $form_state->getValue('dxp_consent_cookie_name'))
      // ->set('dxp_consent_continent', $form_state->getValue('dxp_consent_continent'))
      // ->set('dxp_timeout_in_milliseconds', $form_state->getValue('dxp_timeout_in_milliseconds'))
      ->set('dxp_prod', $form_state->getValue('dxp_prod'))
      ->save();
  }

}
