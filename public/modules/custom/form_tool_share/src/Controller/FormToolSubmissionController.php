<?php

namespace Drupal\form_tool_share\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Http\RequestStack;
use Drupal\Core\Session\AccountInterface;
use Drupal\helfi_atv\AtvService;
use Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData;
use Drupal\webform_formtool_handler\Plugin\WebformHandler\FormToolWebformHandler;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Form Tool Handler routes.
 */
class FormToolSubmissionController extends ControllerBase {

  /**
   * The helfi_atv service.
   *
   * @var \Drupal\helfi_atv\AtvService
   */
  protected AtvService $helfiAtv;

  /**
   * The helfi_helsinki_profiili service.
   *
   * @var \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData
   */
  protected HelsinkiProfiiliUserData $helfiHelsinkiProfiili;

  /**
   * Current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected AccountInterface $account;

  /**
   * The request service.
   *
   * @var \Drupal\Core\Http\RequestStack
   */
  protected RequestStack $request;

  /**
   * The controller constructor.
   *
   * @param \Drupal\helfi_atv\AtvService $helfi_atv
   *   The helfi_atv service.
   * @param \Drupal\helfi_helsinki_profiili\HelsinkiProfiiliUserData $helfi_helsinki_profiili
   *   The helfi_helsinki_profiili service.
   * @param \Drupal\Core\Http\RequestStack $request
   *   Request stack.
   */
  public function __construct(
    AtvService $helfi_atv,
    HelsinkiProfiiliUserData $helfi_helsinki_profiili,
    RequestStack $request
  ) {
    $this->helfiAtv = $helfi_atv;
    $this->helfiHelsinkiProfiili = $helfi_helsinki_profiili;
    $this->account = \Drupal::currentUser();
    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('helfi_atv.atv_service'),
      $container->get('helfi_helsinki_profiili.userdata'),
      $container->get('request_stack'),
    );
  }

  /**
   * Loads webform submission by the human readable format (HEL-XXX-XXX).
   *
   * Also makes access checks and returns data to be handled in template.
   *
   * Not that in this we should only parse form data itself, and add other
   * webform data only if needed.
   *
   * @param string $submission_id
   *   Submission id.
   *
   * @return array
   *   Render array for template.
   */
  public function build(string $submission_id): array {

    try {
      $entity = FormToolWebformHandler::submissionObjectAndDataFromFormId($submission_id, 'view');

      $view_builder = \Drupal::entityTypeManager()
        ->getViewBuilder('webform_submission');
      $pre_render = $view_builder->view($entity);

      $formTitle = $entity->getWebform()->get('title');
    }
    catch (AccessDeniedException $e) {
      throw new AccessDeniedHttpException($e->getMessage());
    }
    catch (\Exception $e) {
      throw new NotFoundHttpException($e->getMessage());
    }
    catch (GuzzleException $e) {
      throw new NotFoundHttpException('General error.');
    }

    $sector = $entity->getWebform()->getThirdPartySettings('form_tool_webform_parameters')['sector'];
    $address = $entity->getWebform()->getThirdPartySettings('form_tool_webform_parameters')['postal_address'];
    $submission_date = $entity->getFields()['created']->getValue()[0]['value'];

    return [
      '#theme' => 'submission_print',
      '#id' => $submission_id,
      '#submission' => $pre_render,
      '#submissionDate' => $submission_date,
      '#sector' => $sector,
      '#address' => $address,
      '#form' => $formTitle,
    ];
  }

  /**
   * Checks access for a specific request.
   *
   * We can check access without loading ATV document when we save user uuid
   * and other things to local db.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param string $submission_id
   *   Application number from Avus2 / ATV.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessByApplicationNumber(AccountInterface $account, string $submission_id): AccessResultInterface {

    $uri = $this->request->getCurrentRequest()->getUri();

    $operation = 'view';
    if (str_ends_with($uri, '/edit')) {
      $operation = 'edit';
    }

    $result = \Drupal::service('database')
      ->query("SELECT submission_uuid,document_uuid,admin_owner,admin_roles,user_uuid FROM {form_tool_map} WHERE form_tool_id = :form_tool_id", [
        ':form_tool_id' => $submission_id,
      ]);

    $data = $result->fetchObject();

    /** @var \Drupal\webform\Entity\WebformSubmission $entity */
    $submissionObject = \Drupal::service('entity.repository')
      ->loadEntityByUuid('webform_submission', $data->submission_uuid);

    // Parameters from the route and/or request as needed.
    return AccessResult::allowedIf($submissionObject->access($operation, $account));
  }

}
