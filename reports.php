<?php

require_once 'reports.civix.php';
use CRM_Reports_ExtensionUtil as E;

function reports_civicrm_summaryActions(&$actions, $contactID) {
  // make sure the contact is an organization
  if ($contactID > 0) {
    $sql = "select contact_type from civicrm_contact where id = $contactID";
    $contactType = CRM_Core_DAO::singleValueQuery($sql);
    if ($contactType == 'Organization') {
      $config = CRM_Core_Config::singleton();

      // add link to info page
      $actions['otherActions']['bedrijfsprofiel'] = [
        'title' => 'Bedrijfsprofiel',
        'weight' => 999,
        'ref' => 'bedrijfsprofiel',
        'key' => 'bedrijfsprofiel',
        'href' =>  $config->userFrameworkBaseURL . 'bedrijfsprofiel_leden_details?id=' . $contactID,
      ];
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function reports_civicrm_config(&$config) {
  _reports_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function reports_civicrm_install() {
  _reports_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function reports_civicrm_enable() {
  _reports_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function reports_civicrm_navigationMenu(&$menu) {
  _reports_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _reports_civix_navigationMenu($menu);
} // */
