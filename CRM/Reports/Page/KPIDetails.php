<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Page_KPIDetails extends CRM_Core_Page {

  public function run() {
    $kpiHelper = new CRM_BlauweClusterKPI();

    // get the year from the query string
    $year = CRM_Utils_Request::retrieve('year', 'Integer', $this, TRUE);

    // title
    CRM_Utils_System::setTitle('KPI Details: ' . $year);

    // C1
    $items = $kpiHelper->getC1Details($year, 'members');
    $this->assign('c1Members', $this->daoToListItems($items));
    $items = $kpiHelper->getC1Details($year, 'companies');
    $this->assign('c1Companies', $this->daoToListItems($items));
    $items = $kpiHelper->getC1Details($year, 'collaborations');
    $this->assign('c1Collaborations', $this->daoToListItems($items));

    // C2
    $items = $kpiHelper->getC2Details($year);
    $this->assign('c2Events', $this->daoToListItems($items));

    // C3
    $items = $kpiHelper->getC3Details($year);
    $this->assign('c3Contacts', $this->daoToListItems($items));

    // C4
    $items = $kpiHelper->getC4($year, 'details');
    $this->assign('c4Collaborations', $this->daoToListItems($items));

    // C5
    $items = $kpiHelper->getC5($year, 'details');
    $this->assign('c5Companies', $this->daoToListItems($items));

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
