<?php
$conf['x_frame_options'] = '';
$config['x_frame_options'] = '';
$settings['x_frame_options'] = '';


/**
 * Enable local development services.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';


/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';

/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;


/**
 * @file
 * An example of Drupal 9 development environment configuration file.
 */
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['page'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';


$settings['file_public_base_url'] = 'https://hel-fi-drupal-grant-applications.docker.so/sites/default/files';
$settings['skip_permissions_hardening'] = TRUE;
$settings['class_loader_auto_detect'] = FALSE;

$settings['twig_debug'] = TRUE;

$config['system.performance']['css']['preprocess'] = 0;
$config['system.performance']['js']['preprocess'] = 0;
$config['system.logging']['error_level'] = 'some';

putenv('AVUSTUS2_ENDPOINT=');
putenv('AVUSTUS2_USERNAME=');
putenv('AVUSTUS2_PASSWORD=');

putenv('AVUSTUS2_LIITE_ENDPOINT=');

putenv('BACKEND_MODE=');

putenv('YJDH_USERNAME=');
putenv('YJDH_PASSWD=');
putenv('YJDH_ENDPOINT=');

putenv('YRTTI_USERNAME=');
putenv('YRTTI_PASSWD=');
putenv('YRTTI_ENDPOINT=');

putenv('USERINFO_ENDPOINT=');
putenv('USERINFO_PROFILE_ENDPOINT=');

putenv('ATV_API_KEY=');
putenv('ATV_BASE_URL=');


$settings['file_private_path'] = 'sites/default/files/private';

putenv('ATV_SCHEMA_PATH=');
putenv('APP_ENV=');

putenv('TUNNISTAMO_CLIENT_ID=');
putenv('TUNNISTAMO_CLIENT_SECRET=');

putenv('FORM_TOOL_TOKEN=');

$config['openid_connect.client.tunnistamo']['settings']['client_id'] = '';
$config['openid_connect.client.tunnistamo']['settings']['client_secret'] = '';
$config['openid_connect.client.tunnistamo']['settings']['scopes'] = '';
$config['openid_connect.client.tunnistamo']['settings']['environment_url'] = '';
$config['openid_connect.client.tunnistamo']['settings']['is_production'] = false;


$config['openid_connect.client.tunnistamoadmin']['settings']['client_id'] = '';
$config['openid_connect.client.tunnistamoadmin']['settings']['client_secret'] = '';
$config['openid_connect.client.tunnistamoadmin']['settings']['scopes'] = '';
$config['openid_connect.client.tunnistamoadmin']['settings']['environment_url'] = '';
$config['openid_connect.client.tunnistamoadmin']['settings']['is_production'] = false;