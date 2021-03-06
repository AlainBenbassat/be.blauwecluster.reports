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
    $items = $kpiHelper->getC1($year, 'members', FALSE, FALSE);
    $this->assign('c1Members', $this->daoToListItems($items));
    $this->assign('c1MembersTotal', $items->N);
    $items = $kpiHelper->getC1($year, 'companies', FALSE, FALSE);
    $this->assign('c1Companies', $this->daoToListItems($items));
    $this->assign('c1CompaniesTotal', $items->N);
    $items = $kpiHelper->getC1($year, 'collaborations', FALSE, FALSE);
    $this->assign('c1Collaborations', $this->daoToListItems($items));
    $this->assign('c1CollaborationsTotal', $items->N);

    $this->assign('c1Total', $kpiHelper->getC1($year, 'all', TRUE, FALSE));

    // C1 bis
    $items = $kpiHelper->getC1($year, 'members', FALSE, TRUE);
    $this->assign('c1bisMembers', $this->daoToListItems($items));
    $this->assign('c1bisMembersTotal', $items->N);
    $items = $kpiHelper->getC1($year, 'companies', FALSE, TRUE);
    $this->assign('c1bisCompanies', $this->daoToListItems($items));
    $this->assign('c1bisCompaniesTotal', $items->N);
    $items = $kpiHelper->getC1($year, 'collaborations', FALSE, TRUE);
    $this->assign('c1bisCollaborations', $this->daoToListItems($items));
    $this->assign('c1bisCollaborationsTotal', $items->N);

    $this->assign('c1bisTotal', $kpiHelper->getC1($year, 'all', TRUE, TRUE));

    // C2
    $items = $kpiHelper->getC2($year, FALSE);
    $this->assign('c2Events', $this->daoToListItems($items));
    $this->assign('c2Total', $items->N);

    // C3
    $items = $kpiHelper->getC3($year, FALSE);
    $this->assign('c3Contacts', $this->daoToListItems($items));
    $this->assign('c3Total', $items->N);

    // C4
    $items = $kpiHelper->getC4($year, FALSE);
    $this->assign('c4Collaborations', $this->daoToListItems($items));
    $this->assign('c4Total', $items->N);

    // C5
    $items = $kpiHelper->getC5($year, FALSE, FALSE);
    $this->assign('c5Companies', $this->daoToListItems($items));
    $this->assign('c5Total', $items->N);
    $c5 = $items->N;

    // C5 bis
    $items = $kpiHelper->getC5($year, FALSE, TRUE);
    $this->assign('c5bisCompanies', $this->daoToListItems($items));
    $this->assign('c5bisTotal', round($items->N / $c5 * 100));

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
