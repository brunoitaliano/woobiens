<?php

namespace Drupal\httpswww\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HttpsWwwSettingsForm.
 *
 * @package Drupal\httpswww\Form
 */
class HttpsWwwSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'httpswww_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['httpswww.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('httpswww.settings');

    $this->messenger()->addWarning($this->t('Please read and understand the options before making any changes.'));

    $form['fieldset_1'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Redirect status'),
      '#description'   => $this->t('' .
        '&bull; Make sure your site is accessible for the options you select before enabling the module.<br>' .
        '&bull; If you don\'t have the "Bypass HTTPS/www redirects" permission and are on a different domain than what you select, you will be logged out when saving.<br>' .
        '&bull; Once enabled, avoid making any further changes as it may affect SEO.'
      ),
    ];

    $form['fieldset_1']['enabled'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Enable redirects'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['fieldset_2'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('URL prefix redirect'),
    ];

    $form['fieldset_2']['prefix'] = [
      '#type'          => 'radios',
      '#description'   => $this->t('' .
        'Users will be redirected corresponding to the selected option.<br>' .
        'Make sure your site is accessible for the option selected.'
      ),
      '#default_value' => $config->get('prefix') ?: 'no',
      '#options' => [
        'no'  => $this->t('Without WWW prefix'),
        'yes' => $this->t('With WWW prefix'),
      ],
    ];

    $form['fieldset_3'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('HTTP Secure (HTTPS) redirect'),
    ];

    $form['fieldset_3']['scheme'] = [
      '#type'          => 'radios',
      '#description'   => $this->t('' .
        'Before activating HTTPS support you must first configure your server with a valid SSL certificate.<br>' .
        'For more information, see <a href=":url">@url</a>.', [
        ':url' => 'https://www.drupal.org/https-information',
        '@url' => 'https://www.drupal.org/https-information',
      ]),
      '#default_value' => $config->get('scheme') ?: 'mixed',
      '#options' => [
        'mixed' => $this->t('Enable mixed mode (both HTTP and HTTPS is allowed).'),
        'https' => $this->t('Enable HTTPS and redirect all HTTP traffic (force HTTPS).'),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('httpswww.settings');
    $config->set('enabled', $form_state->getValue('enabled'));
    $config->set('prefix', $form_state->getValue('prefix'));
    $config->set('scheme', $form_state->getValue('scheme'));
    $config->save();
    $this->messenger()->addStatus($this->t('The configuration options have been saved.'));
  }

}
