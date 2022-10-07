<?php

namespace Drupal\webform_formtool_handler\Plugin\WebformHandler;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityRepository;
use Drupal\Core\Form\FormStateInterface;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Drupal\webform\Entity\Webform;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\webform_formtool_handler\Controller\FormToolSubmissionController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
   * @var \Drupal\Core\Entity\EntityRepository
   */
  protected EntityRepository $entityRepository;

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

    $instance->entityRepository = $container->get('entity.repository');

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
  public function alterForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    parent::alterForm($form, $form_state, $webform_submission); // TODO: Change the autogenerated stub
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

    parent::validateForm($form, $form_state, $webform_submission);
    $values = $form_state->getValues();

    if (isset($values['profile_fields'])) {
      if (empty($values['profile_fields'])) {
        $form_state->setErrorByName('profile_fields', 'Profile fields must be set.');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(WebformSubmissionInterface $webform_submission) {
    $this->submittedFormData = $webform_submission->getData();

    // Because our submission storage is not used (WHY!?!?!?!?)
    // we need to empty this manually.
    $webform_submission->setData([]);
    // don't save ip address.
    $webform_submission->remote_addr->value = '';
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

      /** @var \Drupal\webform\WebformSubmissionForm $webFormSubmissionForm */
      $webFormSubmissionForm = $form_state->getFormObject();

      $webform = Webform::load($form['#webform_id']);

      // Get all the system languages.
      $langcodes = \Drupal::languageManager()->getLanguages();
      $langcodesList = array_keys($langcodes);

      $translatedHumanReadableTypes = [];

      foreach ($langcodesList as $langcode) {

        $translation = $this->entityRepository->getTranslationFromContext($webform, $langcode);
        $label = $translation->label();
        $translatedHumanReadableTypes[$langcode] = $label;
      }

      $thirdPartySettings = $webFormSubmissionForm->getWebform()
        ->getThirdPartySettings('form_tool_webform_parameters');

      $formToolSubmissionId = $this->createSubmissionId($webform_submission, $thirdPartySettings);

      $documentValues = [
        'service' => 'lomaketyokalu-' . $this->appEnv,
        'type' => strtoupper($thirdPartySettings['form_code']),
        'human_readable_type' => $translatedHumanReadableTypes,
        'status' => 'SUBMITTED',
        'transaction_id' => $formToolSubmissionId,
        'business_id' => '',
        'tos_function_id' => $thirdPartySettings['tos_function_id'],
        'tos_record_id' => $thirdPartySettings['tos_record_id'],
        'draft' => FALSE,
        'metadata' => [
          'form_tool_id' => $formToolSubmissionId,
          'sector' => $thirdPartySettings['sector'],
          'ad_group' => $thirdPartySettings['ad_group'],
          'owner' => $thirdPartySettings['owner']
        ],
      ];

//      $helsinkiProfiili = $this->helsinkiProfiiliUserData->getUserData();
//      if (isset($helsinkiProfiili['sub'])) {
//        $documentValues['user_id'] = $helsinkiProfiili['sub'];
//      }

      try {
        $atvDocument = $this->atvService->createDocument($documentValues);

        $atvDocument->setContent($this->submittedFormData);

        $newDocument = $this->atvService->postDocument($atvDocument);

        $this->connection->insert('form_tool_map')
          ->fields([
            'submission_uuid' => $webform_submission->uuid(),
            'document_uuid' => $newDocument->getId(),
            'user_uuid' => $newDocument->getUserId(),
            'form_tool_id' => $formToolSubmissionId,
            'admin_owner' => $thirdPartySettings["owner"],
            'admin_roles' => implode(',', array_keys($thirdPartySettings["roles"]))
          ])
          ->execute();

        $form_state->setRedirect(
          'entity.form_tool_share.completion',
          ['submission_id' => $formToolSubmissionId]
        );

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
        $form_state->setRedirect('entity.form_tool_share.error');
      }
    }
    else {
      $this->messenger()
        ->addWarning('Webform already submitted, data is not saved');
    }
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
    $this->getLogger('webform_formtool_handler')->log($level, $msg);
  }

  /**
   * Load submission object from database via generated form id.
   *
   * @param string $id
   *   Form submission id.
   *
   * @return \Drupal\webform\Entity\WebformSubmission|null
   *   Loaded object or null if not found.
   *
   * @throws \Drupal\Core\TempStore\TempStoreException
   * @throws \Drupal\helfi_atv\AtvDocumentNotFoundException
   * @throws \Drupal\helfi_atv\AtvFailedToConnectException
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public static function submissionObjectAndDataFromFormId(string $id): ?WebformSubmission {

    /** @var \Drupal\Core\Session\AccountInterface $account */
    $account = \Drupal::currentUser();

    // check access for user.
    $access = FormToolSubmissionController::singleSubmissionAccess($account, 'view', $id);

    if ($access !== TRUE) {
      throw new AccessDeniedException('Access denied');
    }

    $result = \Drupal::service('database')->query("SELECT submission_uuid,document_uuid FROM {form_tool_map} WHERE form_tool_id = :form_tool_id", [
      ':form_tool_id' => $id,
    ]);
    $data = $result->fetchObject();

    if ($data == FALSE) {
      throw new NotFoundHttpException();
    }

    /** @var \Drupal\helfi_atv\AtvService $atvService */
    $atvService = \Drupal::service('helfi_atv.atv_service');

    /** @var \Drupal\webform\Entity\WebformSubmission $entity */
    $entity = \Drupal::service('entity.repository')
      ->loadEntityByUuid('webform_submission', $data->submission_uuid);

    if (!$entity) {
      throw new NotFoundHttpException('Form submission not found');
    }

    /** \Drupal\helfi_atv\AtvDocument $document */
    $documents = $atvService->searchDocuments(
      [
        'transaction_id' => $id
      ]
    );

    $document = reset($documents);

    $documentContent = $document->getContent();
    $entity->setData($documentContent);

    return $entity;
  }

}
