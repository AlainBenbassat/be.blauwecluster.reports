<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Page_KPIDetails extends CRM_Core_Page {

  public function run() {
    // get the year from the query string
    $year = CRM_Utils_Request::retrieve('year', 'Integer', $this, TRUE);

    // title
    CRM_Utils_System::setTitle('KPI Details: ' . $year);


    parent::run();
  }

}
