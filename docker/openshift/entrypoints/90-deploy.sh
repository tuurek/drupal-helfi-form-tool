#!/bin/bash

cd /var/www/html/public && drush deploy

# allow translations to fail
cd /var/www/html/public && drush helfi:locale-import form_tool_contact_info || true
cd /var/www/html/public && drush helfi:locale-import form_tool_profile_data || true
cd /var/www/html/public && drush helfi:locale-import form_tool_webform_parameters || true
