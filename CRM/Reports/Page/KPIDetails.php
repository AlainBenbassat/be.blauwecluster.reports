<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Page_KPIDetails extends CRM_Core_Page {

  public function run() {
    $kpiHelper = new CRM_BlauweClusterKPI();

    // get the year from the query string
    $year = CRM_Utils_Request::retrieve('year', 'Integer', $this, TRUE);

    // title
    CRM_Utils_System::setTitle('KPI Details: ' . $year);

    // C2
    $items = $kpiHelper->getC2Details($year);
    $this->assign('c2Events', $this->daoToListItems($items));

    parent::run();
  }

  public function daoToListItems($dao) {
    $list = '';
    while ($dao->fetch()) {
      $list .= '<li>' . $dao->item . '</li>';
    }

    return $list;
  }

}
