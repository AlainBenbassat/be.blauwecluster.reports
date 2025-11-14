<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Form_Report_KPI2025 extends CRM_Report_Form {

  private $year1 = 2025;
  private $year2;
  private $year3;

  public function __construct() {
    $this->year2 = $this->year1 + 1;
    $this->year3 = $this->year1 + 2;

    $this->_columns = [
      'civicrm_contact' => [
        'fields' => [
          'column1' => [
            'title' => 'CPI',
            'required' => TRUE,
            'dbAlias' => '1',
          ],
          'column2' => [
            'title' => 'Cluster Policy Indicator',
            'required' => TRUE,
            'dbAlias' => '1',
          ],
          'column3' => [
            'title' => $this->year1,
            'required' => TRUE,
            'dbAlias' => '1',
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column4' => [
            'title' => $this->year2,
            'required' => TRUE,
            'dbAlias' => '1',
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column5' => [
            'title' => $this->year3,
            'required' => TRUE,
            'dbAlias' => '1',
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column6' => [
            'title' => 'Cumulatief',
            'required' => TRUE,
            'dbAlias' => '1',
          ],
          'column7' => [
            'title' => 'Gemiddeld',
            'required' => TRUE,
            'dbAlias' => '1',
          ],
        ],
      ],
    ];

    parent::__construct();
  }

  public function preProcess() {
    $this->assign('reportTitle', "KPI's Blauwe Cluster");

    // hyperlinks to KPI detail page
    $linksToDetailsPage = 'Bekijk de details: ';
    $years = [$this->year1, $this->year2, $this->year3];
    foreach ($years as $year) {
      $url = CRM_Utils_System::url('civicrm/kpi-details-2025', 'reset=1&year=' . $year);
      $link = '<a href="' . $url . '">' . $year . '</a>';

      if ($year == $this->year1) {
        $linksToDetailsPage .= $link;
      }
      else {
        $linksToDetailsPage .= ', ' . $link;
      }
    }
    $this->assign('linksToDetailsPage', $linksToDetailsPage);

    parent::preProcess();
  }

  public function from() {
    $from = "
      FROM
        civicrm_contact {$this->_aliases['civicrm_contact']}
    ";

    $this->_from = $from;
  }

  public function where() {
    $this->_where = "WHERE id = 1 ";
  }

  public function alterDisplay(&$rows) {
    $kpiHelper = new CRM_BlauweClusterKPI2025($this->year1, $this->year2, $this->year3);

    $rows = [];

    $rows[] = $kpiHelper->getKpiRow('DBC1');
    $rows[] = $kpiHelper->getKpiRow('DBC2');
    $rows[] = $kpiHelper->getKpiRow('DBC3');
    $rows[] = $kpiHelper->getKpiRow('CPI1');
    $rows[] = $kpiHelper->getKpiRow('CPI2');
    $rows[] = $kpiHelper->getKpiRow('CPI3');
    $rows[] = $kpiHelper->getKpiRow('CPI4');
    $rows[] = $kpiHelper->getKpiRow('CPI5');
    $rows[] = $kpiHelper->getKpiRow('CPI6');
    $rows[] = $kpiHelper->getKpiRow('CPI7');
    $rows[] = $kpiHelper->getKpiRow('CPI7Bis');
    $rows[] = $kpiHelper->getKpiRow('CPI8');
    $rows[] = $kpiHelper->getKpiRow('CPI9');
    $rows[] = $kpiHelper->getKpiRow('CPI10');
    $rows[] = $kpiHelper->getKpiRow('CPI11');

  }

  public function rowPostProcess(&$row, $unit) {
    $valSum = '';
    $valAvg = '';

    // get the column values
    $val1 = $row['civicrm_contact_column2'];
    $val2 = $row['civicrm_contact_column3'];
    $val3 = $row['civicrm_contact_column4'];

    // calculate sum and average for numeric values
    if (is_numeric($val1) && is_numeric($val2) && is_numeric($val3)) {
      // calculate sum
      $valSum = $val1 + $val2 + $val3;

      // calculate average
      $valAvg = round($valSum / 3, 2);
    }

    // add unit to all row values
    if ($unit) {
      $row['civicrm_contact_column2'] .= $row['civicrm_contact_column2'] . ' ' . $unit;
      $row['civicrm_contact_column3'] .= $row['civicrm_contact_column3'] . ' ' . $unit;
      $row['civicrm_contact_column4'] .= $row['civicrm_contact_column4'] . ' ' . $unit;

      $valSum .= ' ' . $unit;
      $valAvg .= ' ' . $unit;
    }

    // fill in column 5
    $row['civicrm_contact_column5'] = "$valSum / $valAvg";
  }
}
