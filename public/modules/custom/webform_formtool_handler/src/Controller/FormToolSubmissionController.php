<?php

namespace Drupal\webform_formtool_handler\Controller;

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
      $entity = FormToolWebformHandler::submissionObjectAndDataFromFormId($submission_id);

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

    return [
      '#theme' => 'submission_print',
      '#id' => $submission_id,
      '#submission' => $pre_render,
      '#form' => $formTitle,
    ];
  }

  /**
   * Checks access for a specific request.
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

    // Parameters from the route and/or request as needed.
    return AccessResult::allowedIf(
    // Not sure if this haspermission is required when we check this
    // manually in method.
    // $account->hasPermission($operation . ' own webform submission') &&.
      self::singleSubmissionAccess(
        $account,
        $operation,
        $submission_id
      ));
  }

  /**
   * Check access to single submission via saved details to local DB.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   User account.
   * @param string $operation
   *   Operation we check access against.
   * @param string $submission_id
   *   Submission id from ATV.
   *
   * @return bool
   *   Access status
   */
  public static function singleSubmissionAccess(AccountInterface $account, string $operation, string $submission_id): bool {

    $accountMail = $account->getEmail();
    $accountRoles = $account->getRoles();

    $result = \Drupal::service('database')
      ->query("SELECT submission_uuid,document_uuid,admin_owner,admin_roles,user_uuid FROM {form_tool_map} WHERE form_tool_id = :form_tool_id", [
        ':form_tool_id' => $submission_id,
      ]);

    $data = $result->fetchObject();

    // Admin owner user can access this submission.
    if ($data->admin_owner == $accountMail) {
      return TRUE;
    }

    // And everybody with admin role can access.
    foreach ($accountRoles as $role) {
      if (str_contains($data->admin_roles, $role)) {
        return TRUE;
      }
    }

    $helProfiiliData = \Drupal::service('helfi_helsinki_profiili.userdata');
    $userData = $helProfiiliData->getUserData();

    if (!$userData) {
      return FALSE;
    }

    // User can access their own submission.
    if ($data->user_uuid == $userData["sub"]) {
      return TRUE;
    }

    // others, no access.
    return FALSE;
  }

}
