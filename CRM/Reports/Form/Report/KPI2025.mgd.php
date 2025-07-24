<?php

// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
return [
  [
    'name' => 'CRM_Reports_Form_Report_KPI2025',
    'entity' => 'ReportTemplate',
    'params' => [
      'version' => 3,
      'label' => 'KPI 2025-2026-2027',
      'description' => 'KPI2025 (be.blauwecluster.reports)',
      'class_name' => 'CRM_Reports_Form_Report_KPI2025',
      'report_url' => 'be.blauwecluster.reports/kpi2025',
      'component' => 'CiviMember',
    ],
  ],
];
