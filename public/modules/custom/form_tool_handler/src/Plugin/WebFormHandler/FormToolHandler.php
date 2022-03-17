<?php

namespace Drupal\form_tool_handler\Plugin\WebformHandler;

use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\Plugin\WebformHandlerInterface;
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
 *   cardinality =
 *   \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results =
 *   \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
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
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): WebformHandlerBase|WebformHandlerInterface|ContainerFactoryPluginInterface|static {
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
  // Public function access(WebformSubmissionInterface $webform_submission,
  // $operation, AccountInterface $account = NULL):
  // AccessResultNeutral|AccessResultInterface {
  // return parent::access($webform_submission, $operation, $account);
  // }.

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
  public function validateForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission) {
    $retval = parent::validateForm($form, $form_state, $webform_submission);

    $errors = $form_state->getErrors();

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

    $webForm = $webform_submission->getWebform();

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

        $result = $this->connection->insert('form_tool')
          ->fields([
            'submission_uuid' => $webform_submission->uuid(),
            'document_uuid' => $newDocument->getId(),
            'form_tool_id' => $formToolSubmissionId,
          ])
          ->execute();

        $url = Url::fromRoute(
          'form_tool_handler.view_submission',
          ['id' => $formToolSubmissionId],
          [
            'attributes' => [
              'data-drupal-selector' => 'form-submitted-ok',
            ],
          ]
        );

        $msg = $this->t(
          'Form submission (@number) saved,
                          see submitted data from @link',
          [
            '@number' => $formToolSubmissionId,
            '@link' => Link::fromTextAndUrl('here', $url)->toString(),
          ]);

        $this->messenger()
          ->addWarning($msg);

        // If (isset($thirdPartySettings["email_notify"]) &&
        // !empty($thirdPartySettings["email_notify"])) {
        // $mailManager = \Drupal::service('plugin.manager.mail');
        // $module = 'form_tool_handler';
        // $key = 'submission_email_notify';
        // $to = $thirdPartySettings["email_notify"];
        //
        // $url = Url::fromRoute(
        // 'form_tool_handler.view_submission',
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
        $this->getLogger('form_tool_handler')->error($e->getMessage());
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
    // If (!$this->isNewSubmission($webform_submission->uuid())) {
    // $this->messenger()
    // ->addWarning('Submitted data, no edits are possible.');
    // }.
  }

}
