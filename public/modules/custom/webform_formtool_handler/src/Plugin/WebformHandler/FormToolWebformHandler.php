<?php

namespace Drupal\webform_formtool_handler\Plugin\WebformHandler;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform formtool handler.
 *
 * @WebformHandler(
 *   id = "formtool_webform_handler",
 *   label = @Translation("Formtool Webform handler"),
 *   category = @Translation("Helfi"),
 *   description = @Translation("Webform handler form formtool."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_IGNORED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_REQUIRED,
 * )
 */
class FormToolWebformHandler extends WebformHandlerBase {

  /**
   * Access to configuration.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Conditions validator.
   *
   * @var \Drupal\webform\WebformSubmissionConditionsValidatorInterface
   */
  protected $conditionsValidator;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Access to ATV.
   *
   * @var \Drupal\helfi_atv\AtvService
   */
  protected AtvService $atvService;

  /**
   * Access to helsinki profile data.
   *
   * @var \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData
   */
  protected HelsinkiProfiiliUserData $helsinkiProfiiliUserData;

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected Connection $connection;

  /**
   * Form data saved because the data in saved submission is not preserved.
   *
   * @var array
   *   Holds submitted data for processing in confirmForm.
   *
   * When we want to delete all submitted data before saving
   * submission to database. This way we can still use webform functionality
   * while not saving any sensitive data to local drupal.
   */
  private array $submittedFormData = [];

  /**
   * App environment variable.
   *
   * @var string
   */
  protected string $appEnv;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->loggerFactory = $container->get('logger.factory');
    $instance->configFactory = $container->get('config.factory');
    $instance->conditionsValidator = $container->get('webform_submission.conditions_validator');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->appEnv = getenv('APP_ENV') ?: 'default';

    /** @var \Drupal\helfi_atv\AtvService atvService */
    $instance->atvService = $container->get('helfi_atv.atv_service');

    /** @var \Drupal\Core\Database\Connection connection */
    $instance->connection = $container->get('database');

    /** @var \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData helsinkiProfiiliUserData */
    $instance->helsinkiProfiiliUserData = $container->get('helfi_helsinki_profiili.userdata');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'debug' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {

    // Development.
    $form['development'] = [
      '#type' => 'details',
      '#title' => $this->t('Development settings'),
    ];
    $form['development']['debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable debugging'),
      '#description' => $this->t('If checked, every handler method invoked will be displayed onscreen to all users.'),
      '#return_value' => TRUE,
      '#default_value' => $this->configuration['debug'],
    ];

    return $this->setSettingsParents($form);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['debug'] = (bool) $form_state->getValue('debug');
  }

  /**
   * {@inheritdoc}
   */
  public function alterElements(array &$elements, WebformInterface $webform) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function overrideSettings(array &$settings, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function alterForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $retval = parent::validateForm($form, $form_state, $webform_submission);

    $errors = $form_state->getErrors();

  }

  /**
   * Is submission new?
   *
   * @param string $submission_uuid
   *   Submission id.
   *
   * @return bool
   *   If submission is new
   */
  protected function isNewSubmission($submission_uuid): bool {
    $result = $this->connection->query("SELECT document_uuid FROM {form_tool_map} WHERE submission_uuid = :submission_uuid", [
      ':submission_uuid' => $submission_uuid,
    ]);
    $data = $result->fetchObject();

    return $data == FALSE;
  }

  /**
   * Return Application environment shortcode.
   *
   * @return string
   *   Shortcode from current environment.
   */
  public static function getAppEnv(): string {
    $appEnv = getenv('APP_ENV');

    if ($appEnv == 'development') {
      $appParam = 'DEV';
    }
    else {
      if ($appEnv == 'production') {
        $appParam = '';
      }
      else {
        if ($appEnv == 'testing') {
          $appParam = 'TEST';
        }
        else {
          if ($appEnv == 'staging') {
            $appParam = 'STAGE';
          }
          else {
            $appParam = 'LOCAL';
          }
        }
      }
    }
    return $appParam;
  }

  /**
   * HEL-{esitiedoista-lyhenne}-{webform-juokseva-id}.
   *
   * @param \Drupal\webform\Entity\WebformSubmission $submission
   *   Webform data.
   * @param array $thirdPartySettings
   *   Settings from form config.
   *
   * @return string
   *   Generated number.
   */
  public static function createSubmissionId(WebformSubmission $submission, array $thirdPartySettings): string {

    $appParam = self::getAppEnv();

    return 'HEL-' . strtoupper($thirdPartySettings['form_code']) . '-' . sprintf('%08d', $submission->id()) . '-' . $appParam;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * Confirm form callback.
   *
   * @param array $form
   *   Form details.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   * @param \Drupal\webform\WebformSubmissionInterface $webform_submission
   *   Form submission object.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    parent::confirmForm($form, $form_state, $webform_submission);

    if ($this->isNewSubmission($webform_submission->uuid())) {

      /** @var \Drupal\webform\WebformSubmissionForm $webformSubmissionForm */
      $webformSubmissionForm = $form_state->getFormObject();

      $thirdPartySettings = $webformSubmissionForm->getWebform()
        ->getThirdPartySettings('form_tool_webform_parameters');

      $formToolSubmissionId = $this->createSubmissionId($webform_submission, $thirdPartySettings);

      $documentValues = [
        'service' => 'lomaketyokalu-' . $this->appEnv,
        'type' => strtoupper($thirdPartySettings['form_code']),
        'status' => 'DRAFT',
        // @todo Not sure about this data hash
        //   'transaction_id' => md5($webform_submission->getChangedTime()),
        'transaction_id' => $formToolSubmissionId,
        'business_id' => '',
        'tos_function_id' => 'f917d43aab76420bb2ec53f6684da7f7',
        'tos_record_id' => '89837a682b5d410e861f8f3688154163',
        'draft' => TRUE,
        'metadata' => [
          'form_tool_id' => $formToolSubmissionId,
        ],
      ];

      $helsinkiProfiili = $this->helsinkiProfiiliUserData->getUserData();
      if (isset($helsinkiProfiili['sub'])) {
        $documentValues['user_id'] = $helsinkiProfiili['sub'];
      }

      try {
        $atvDocument = $this->atvService->createDocument($documentValues);

        $atvDocument->setContent($this->submittedFormData);

        $newDocument = $this->atvService->postDocument($atvDocument);

        $result = $this->connection->insert('form_tool_map')
          ->fields([
            'submission_uuid' => $webform_submission->uuid(),
            'document_uuid' => $newDocument->getId(),
            'form_tool_id' => $formToolSubmissionId,
          ])
          ->execute();

        $url = Url::fromRoute(
          'webform_formtool_handler.view_submission',
          ['id' => $formToolSubmissionId],
          [
            'attributes' => [
              'data-drupal-selector' => 'form-submitted-ok',
            ],
          ]
        );

        $completionUrl = Url::fromRoute(
          'entity.form_tool_share.completion',
          ['submissionId' => $formToolSubmissionId]
        );

        $t_args = [
          '@number' => $formToolSubmissionId,
          '@link' => Link::fromTextAndUrl('here', $url)->toString(),
        ];

        $msg = $this->t(
          'Form submission (@number) saved, see submitted data from @link',
          $t_args
          );

        $this->messenger()
          ->addStatus($msg);

        $this->log('info', 'Form submission (@number) saved, see submitted data from @link', $t_args);

        $form_state->setRedirectUrl($completionUrl);

        // If (isset($thirdPartySettings["email_notify"]) &&
        // !empty($thirdPartySettings["email_notify"])) {
        // $mailManager = \Drupal::service('plugin.manager.mail');
        // $module = 'webform_formtool_handler';
        // $key = 'submission_email_notify';
        // $to = $thirdPartySettings["email_notify"];
        //
        // $url = Url::fromRoute(
        // 'webform_formtool_handler.view_submission',
        // ['id' => $formToolSubmissionId],
        // [
        // 'attributes' => [
        // 'data-drupal-selector' => 'form-submitted-ok',
        // ],
        // ]
        // );
        //
        // $params['message'] = $this->t(
        // 'Form submission (@number) saved,
        // see application status from @link',
        // [
        // '@number' => $formToolSubmissionId,
        // '@link' => Link::fromTextAndUrl('here', $url)->toString(),
        // ]);
        //
        // $params['form_title'] = $webForm->get('title');
        // $langcode = \Drupal::currentUser()->getPreferredLangcode();
        // $send = TRUE;
        //
        // $result = $mailManager->mail($module, $key, $to, $langcode, $params,
        // NULL, $send);
        //
        // if ($result['result'] !== TRUE) {
        // $this->messenger()->addStatus(t('There was a problem sending your
        // message and it was not sent.'), 'error');
        // }
        // else {
        // $this->messenger()->addStatus(t('Your message has been sent.'));
        // }
        // }.
      }
      catch (\Exception $e) {
        $this->log('error', $e->getMessage(), []);
      }
    }
    else {
      $this->messenger()
        ->addWarning('Webform already submitted, data is not saved');
    }

  }

  /**
   * {@inheritdoc}
   */
  public function preCreate(array &$values) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postCreate(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postLoad(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function preDelete(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postDelete(WebformSubmissionInterface $webform_submission) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {

    // Get data from form submission
    // and set it to class private variable.
    $this->submittedFormData = $webform_submission->getData();
    // Save no data locally.
    $webform_submission->setData([]);

    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    $this->debug(__FUNCTION__, $update ? 'update' : 'insert');
  }

  /**
   * {@inheritdoc}
   */
  public function preprocessConfirmation(array &$variables) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function createHandler() {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function updateHandler() {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteHandler() {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function createElement($key, array $element) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function updateElement($key, array $element, array $original_element) {
    $this->debug(__FUNCTION__);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteElement($key, array $element) {
    $this->debug(__FUNCTION__);
  }

  /**
   * Display the invoked plugin method to end user.
   *
   * @param string $method_name
   *   The invoked method name.
   * @param string $context1
   *   Additional parameter passed to the invoked method name.
   */
  protected function debug($method_name, $context1 = NULL) {
    if (!empty($this->configuration['debug'])) {
      $t_args = [
        '@id' => $this->getHandlerId(),
        '@class_name' => get_class($this),
        '@method_name' => $method_name,
        '@context1' => $context1,
      ];
      $this->messenger()->addWarning($this->t('Invoked @id: @class_name:@method_name @context1', $t_args), TRUE);
    }
  }

  /**
   * Logs with an arbitrary level.
   *
   * @param mixed $level
   *   Level of logging.
   * @param string $msg
   *   Message.
   * @param mixed[] $t_args
   *   Arguments passed to message.
   */
  protected function log($level, $msg, array $t_args) {
    $this->getLogger('webform_formtool_handler')->log($level, $msg . ', ' . json_encode($t_args));
  }

}
