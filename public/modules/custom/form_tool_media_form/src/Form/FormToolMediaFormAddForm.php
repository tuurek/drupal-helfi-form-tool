<?php

declare(strict_types = 1);

namespace Drupal\form_tool_media_form\Form;

use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\media_library\Form\AddFormBase;

/**
 * {@inheritDoc}
 */
class FormToolMediaFormAddForm extends AddFormBase {

  /**
   * {@inheritDoc}
   */
  protected function buildInputElement(array $form, FormStateInterface $form_state) {
    $container = [
      '#type' => 'container',
    ];
    $container['form_tool_media_form_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Form embed URL'),
      '#description' => $this->t('Enter the form embed URL from @kartta or @palvelukartta.', [
        '@kartta' => Link::fromTextAndUrl('https://hel-fi-form-tool.docker.so/', Url::fromUri('https://hel-fi-form-tool.docker.so/', ['attributes' => ['target' => '_blank']]))->toString(),
        '@palvelukartta' => Link::fromTextAndUrl('https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/', Url::fromUri('https://nginx-lomaketyokalu-dev.agw.arodevtest.hel.fi/fi/', ['attributes' => ['target' => '_blank']]))->toString(),
      ]),
    ];

    $container['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#button_type' => 'primary',
      '#submit' => ['::addButtonSubmit'],
      '#ajax' => [
        'callback' => '::updateFormCallback',
        'wrapper' => 'media-library-wrapper',
        // @todo Remove when https://www.drupal.org/project/drupal/issues/2504115 is fixed.
        'url' => Url::fromRoute('media_library.ui'),
        'options' => [
          'query' => $this->getMediaLibraryState($form_state)->all() + [
            FormBuilderInterface::AJAX_FORM_REQUEST => TRUE,
          ],
        ],
      ],
    ];

    $form['container'] = $container;

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function addButtonSubmit(array $form, FormStateInterface $form_state) {
    $this->processInputValues([$form_state->getValue('form_tool_media_form_url')], $form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'media-hel-form-add-form';
  }

}
