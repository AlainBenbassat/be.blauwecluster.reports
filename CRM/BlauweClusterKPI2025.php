<?php

class CRM_BlauweClusterKPI2025 {
  public function __construct(
    private int $year1,
    private int $year2,
    private int $year3
  ) {}

  public function getRowDBC1(): array {
    $row = $this->createRow('DBC 1', 'Aantal betalende (private) leden');
    return $row;
  }

  public function getRowDBC2(): array {
    $row = $this->createRow('DBC 2', 'Aantal geassocieerde leden - partners');
    return $row;
  }

  public function getRowDBC3(): array {
    $row = $this->createRow('DBC 3', 'Aantal actoren binnen de cluster');
    return $row;
  }

  public function getRowCPI1(): array {
    $row = $this->createRow('CPI 1', 'Aantal actieve actoren binnen de cluster');
    return $row;
  }

  public function getRowCPI2(): array {
    $row = $this->createRow('CPI 2', 'Aantal actieve KMO\'s binnen de cluster');
    return $row;
  }

  public function getRowCPI3(): array {
    $row = $this->createRow('CPI 3', 'Aantal projecten dat kan leiden tot (of gericht is op) innovatie');
    return $row;
  }

  public function getRowCPI4(): array {
    $row = $this->createRow('CPI 4', 'Aantal internationale samenwerkingsverbanden via clusterinitiatieven');
    return $row;
  }

  public function getRowCPI5(): array {
    $row = $this->createRow('CPI 5', 'Aandeel van clusterprojecten die leiden tot innovatie met een bijdrage aan maatschappelijke uitdagingen t.o.v. alle clusterprojecten');
    return $row;
  }

  public function getRowCPI6(): array {
    $row = $this->createRow('CPI 6', 'Aantal unieke ondernemingen die kennis verkrijgen via andere activiteiten dan samenwerkingsprojecten');
    return $row;
  }

  public function getRowCPI7(): array {
    $row = $this->createRow('CPI 7', 'Aantal unieke ondernemingen betrokken in samenwerkingsinitiatieven met minstens 3 ondernemingen');
    return $row;
  }

  public function getRowCPI7Bis(): array {
    $row = $this->createRow('CPI 7 Bis', 'Het aandeel van KMO\'s betrokken in samenwerkingsinitiatieven tussen minstens 3 ondernemingen');
    return $row;
  }

  public function getRowCPI8(): array {
    $row = $this->createRow('CPI 8', 'Deelname in aantal goedgekeurde interclusterprojecten per jaar');
    return $row;
  }

  public function getRowCPI9(): array {
    $row = $this->createRow('CPI 9', 'Investeringen');
    return $row;
  }

  public function getRowCPI10(): array {
    $row = $this->createRow('CPI 10', 'Omzetgroei');
    return $row;
  }

  public function getRowCPI11(): array {
    $row = $this->createRow('CPI 11', 'Tewerkstellingsgroei');
    return $row;
  }

  public function getDBC1(int $year, bool $justCount = TRUE) {
    $memberships = \Civi\Api4\Membership::get(FALSE)
      ->selectRowCount()
      ->addSelect('contact_id.display_name', 'start_date', 'end_date', 'membership_type_id:label')
      ->addWhere('start_date', '<=', "$year-12-31")
      ->addWhere('end_date', '>=', "$year-01-01")
      ->addWhere('membership_type_id:label', 'IN', ['Premium', 'Strategisch', 'Standaard', 'Verkennend'])
      ->addWhere('owner_membership_id', 'IS NULL')
      ->addOrderBy('contact_id.sort_name', 'ASC')
      ->execute();

    if ($justCount) {
      return $memberships->countMatched();
    }

    $listItems = '';
    foreach ($memberships as $membership) {
       $listItems .= '<li>' . $membership['contact_id.display_name'] . ' (' . $membership['membership_type_id:label'] . ' lid van/tot: ' . $membership['start_date'] . ' - ' . $membership['end_date'] . ')</li>';
    }

    return [$memberships->countMatched(), $listItems];
  }

  public function getDBC2(int $year, bool $justCount = TRUE) {
    $memberships = \Civi\Api4\Membership::get(FALSE)
      ->selectRowCount()
      ->addSelect('contact_id.display_name', 'start_date', 'end_date', 'membership_type_id:label')
      ->addWhere('start_date', '<=', "$year-12-31")
      ->addWhere('end_date', '>=', "$year-01-01")
      ->addWhere('membership_type_id:label', 'IN', ['Geassocieerd lid - Partner', 'Geassocieerd lid - Niet-partner'])
      ->addWhere('owner_membership_id', 'IS NULL')
      ->addOrderBy('contact_id.sort_name', 'ASC')
      ->execute();

    if ($justCount) {
      return $memberships->countMatched();
    }

    $listItems = '';
    foreach ($memberships as $membership) {
      $listItems .= '<li>' . $membership['contact_id.display_name'] . ' (' . $membership['membership_type_id:label'] . ' lid van/tot: ' . $membership['start_date'] . ' - ' . $membership['end_date'] . ')</li>';
    }

    return [$memberships->countMatched(), $listItems];
  }

  public function getDBC3() {
  }

  public function getCPI1() {
  }

  public function getCPI2() {
  }

  public function getCPI3() {
  }

  public function getCPI4() {
  }

  public function getCPI5() {
  }

  public function getCPI6() {
  }

  public function getCPI7() {
  }

  public function getCPI7Bis() {
  }

  public function getCPI8() {
  }

  public function getCPI9() {
    return 'monitoring';
  }

  public function getCPI10() {
    return 'monitoring';
  }

  public function getCPI11() {
    return 'monitoring';
  }

  private function createRow($kpi, $description) {
    $kpiFunctionName = 'get' . str_replace(' ', '', $kpi);

    return [
      'civicrm_contact_column1' => $kpi,
      'civicrm_contact_column2' => $description,
      'civicrm_contact_column3' => $this->$kpiFunctionName($this->year1),
      'civicrm_contact_column4' => $this->$kpiFunctionName($this->year2),
      'civicrm_contact_column5' => $this->$kpiFunctionName($this->year3),
      'civicrm_contact_column6' => '',
      'civicrm_contact_column7' => '',
    ];
  }

}
