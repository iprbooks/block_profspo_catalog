<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('profspo_catalog/org_id', get_string('org_id', 'block_profspo_catalog'), "", null, PARAM_INT));
    $settings->add(new admin_setting_configtext('profspo_catalog/org_token', get_string('org_token', 'block_profspo_catalog'), "", null));
    $settings->add(new admin_setting_configtext('profspo_catalog/user_email', get_string('user_email', 'block_profspo_catalog'), "", null));
    $settings->add(new admin_setting_configtext('profspo_catalog/user_pass', get_string('user_pass', 'block_profspo_catalog'), "", null));
}