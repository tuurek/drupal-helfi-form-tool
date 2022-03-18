<?php

/**
 * @file
 * Views hooks file.
 */

/**
 * Implements hook_views_data_alter().
 */
function webform_formtool_handler_views_data_alter(array &$data) {
  $data['webform_submission']['webform_formtool_handler_wf_submission_link'] = [
    'title' => t('WF Submisson link'),
    'group' => t('Content'),
    'field' => [
      'title' => t('WF Submission link'),
      'help' => t('Generate link to submiossin controller'),
      'id' => 'webform_formtool_handler_wf_submission_link',
    ],
  ];
}
