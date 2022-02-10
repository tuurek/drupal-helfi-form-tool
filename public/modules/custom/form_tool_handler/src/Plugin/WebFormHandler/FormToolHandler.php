<?php

namespace Drupal\form_tool_handler\Plugin\WebformHandler;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionConditionsValidatorInterface;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "form_tool_handler",
 *   label = @Translation("form_tool webform handler"),
 *   category = @Translation("Helfi"),
 *   description = @Translation("Handles all form tools submissions"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
final class FormToolHandler extends WebformHandlerBase {

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
   * Constructor.
   *
   * @param array $configuration
   *   Configuration.
   * @param string $plugin_id
   *   Plugin id.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger factory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager.
   * @param \Drupal\webform\WebformSubmissionConditionsValidatorInterface $conditions_validator
   *   Conditions validator.
   * @param \Drupal\helfi_atv\AtvService $atvService
   *   Atv service.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection.
   * @param \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData $helsinkiProfiiliUserData
   *   Helsinki profiili.
   */
  public function __construct(
    array $configuration,
          $plugin_id,
          $plugin_definition,
    LoggerChannelFactoryInterface $logger_factory,
    ConfigFactoryInterface $config_factory,
    EntityTypeManagerInterface $entity_type_manager,
    WebformSubmissionConditionsValidatorInterface $conditions_validator,
    AtvService $atvService,
    Connection $connection,
    HelsinkiProfiiliUserData $helsinkiProfiiliUserData
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->loggerFactory = $logger_factory->get('custom_webform_handler');
    $this->configFactory = $config_factory;
    $this->conditionsValidator = $conditions_validator;
    $this->entityTypeManager = $entity_type_manager;
    $this->appEnv = getenv('APP_ENV') ?: 'default';
    $this->atvService = $atvService;
    $this->connection = $connection;
    $this->helsinkiProfiiliUserData = $helsinkiProfiiliUserData;
  }

  /**
   * Static creator.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Container.
   * @param array $configuration
   *   Plugin config.
   * @param string $plugin_id
   *   Plugin name.
   * @param mixed $plugin_definition
   *   Plugin definition.
   *
   * @return \Drupal\Core\Plugin\ContainerFactoryPluginInterface|WebformHandlerBase|WebformHandlerInterface|static
   *   This plugin object.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('logger.factory'),
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('webform_submission.conditions_validator'),
      $container->get('helfi_atv.atv_service'),
      $container->get('database'),
      $container->get('helfi_helsinki_profiili.userdata')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access(
    WebformSubmissionInterface $webform_submission,
    $operation,
  AccountInterface $account = NULL
  ): AccessResultInterface {
    $retval = parent::access($webform_submission, $operation, $account);

    return $retval;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {

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
    $result = $this->connection->query("SELECT document_uuid FROM {form_tool} WHERE submission_uuid = :submission_uuid", [
      ':submission_uuid' => $submission_uuid,
    ]);
    $data = $result->fetchObject();

    return $data == FALSE;
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
   * @throws \Drupal\helfi_atv\AtvDocumentNotFoundException
   * @throws \Drupal\helfi_atv\AtvFailedToConnectException
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    parent::confirmForm($form, $form_state, $webform_submission);

    if ($this->isNewSubmission($webform_submission->uuid())) {

      /** @var \Drupal\webform\WebformSubmissionForm $webformSubmissionForm */
      $webformSubmissionForm = $form_state->getFormObject();

      $thirdPartySettings = $webformSubmissionForm->getWebform()->getThirdPartySettings('form_tool_webform_parameters');

      // HEL-{esitiedoista-lyhenne}-{webform-juokseva-id}.
      $formToolSubmissionId = 'HEL-' . strtoupper($thirdPartySettings['form_code']) . '-' . sprintf('%08d', $webform_submission->id());

      $documentValues = [
        'service' => 'lomaketyokalu-' . $this->appEnv,
        'type' => strtoupper($thirdPartySettings['form_code']),
        'status' => 'DRAFT',
        // @todo Not sure about this data hash
        'transaction_id' => $webform_submission->getDataHash(),
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

      $atvDocument = $this->atvService->createDocument($documentValues);
      $atvDocument->setContent($this->submittedFormData);

      $newDocument = $this->atvService->postDocument($atvDocument);

      $result = $this->connection->insert('form_tool')
        ->fields([
          'submission_uuid' => $webform_submission->uuid(),
          'document_uuid' => $newDocument->getId(),
          'form_tool_id' => $formToolSubmissionId,
        ])
        ->execute();
    }
    else {
      $this->messenger()
        ->addWarning('Webform already submitted, data is not saved');
    }

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

  }

  /**
   * {@inheritdoc}
   */
  public function postLoad(WebformSubmissionInterface $webform_submission) {
    if (!$this->isNewSubmission($webform_submission->uuid())) {
      $this->messenger()->addWarning('No data is saved after initial submission.');
    }
  }

}
