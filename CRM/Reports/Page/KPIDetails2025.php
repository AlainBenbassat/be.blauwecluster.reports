<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Page_KPIDetails2025 extends CRM_Core_Page {

  public function run() {
    $year1 = 2025;
    $year2 = $year1 + 1;
    $year3 = $year1 + 2;

    $kpiHelper = new CRM_BlauweClusterKPI2025($year1, $year2, $year3);

    // get the year from the query string
    $year = CRM_Utils_Request::retrieve('year', 'Integer', $this, TRUE);

    // title
    CRM_Utils_System::setTitle('KPI Details: ' . $year);

    [$total, $items] = $kpiHelper->getDBC1($year, FALSE);
    $this->assign('dbc1Members', $items);
    $this->assign('dbc1MembersTotal', $total);

    [$total, $items] = $kpiHelper->getDBC2($year, FALSE);
    $this->assign('dbc2Members', $items);
    $this->assign('dbc2MembersTotal', $total);

    [$total, $items] = $kpiHelper->getDBC3($year, FALSE);
    $this->assign('dbc3Actors', $items);
    $this->assign('dbc3ActorsTotal', $total);

    parent::run();
  }

}
