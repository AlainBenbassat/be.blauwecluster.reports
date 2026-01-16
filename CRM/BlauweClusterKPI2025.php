<?php

class CRM_BlauweClusterKPI2025 {
  private $kpiCodeAndDescription = [
    'DBC1'   => 'Aantal betalende (private) leden',
    'DBC2'   => 'Aantal geassocieerde leden - partners',
    'DBC3'   => 'Aantal actoren binnen de cluster',
    'CPI1'   => 'Aantal actieve actoren binnen de cluster',
    'CPI2'   => 'Aantal actieve KMO\'s binnen de cluster',
    'CPI3'   => 'Aantal projecten dat kan leiden tot (of gericht is op) innovatie',
    'CPI4'   => 'Aantal internationale samenwerkingsverbanden via clusterinitiatieven',
    'CPI5'   => 'Aandeel van clusterprojecten die leiden tot innovatie met een bijdrage aan maatschappelijke uitdagingen t.o.v. alle clusterprojecten',
    'CPI6'   => 'Aantal unieke ondernemingen die kennis verkrijgen via andere activiteiten dan samenwerkingsprojecten',
    'CPI7'   => 'Aantal unieke ondernemingen betrokken in samenwerkingsinitiatieven met minstens 3 ondernemingen',
    'CPI7Bis'=> 'Het aandeel van KMO\'s betrokken in samenwerkingsinitiatieven tussen minstens 3 ondernemingen',
    'CPI8'   => 'Deelname in aantal goedgekeurde interclusterprojecten per jaar',
    'CPI9'   => 'Investeringen',
    'CPI10'  => 'Omzetgroei',
    'CPI11'  => 'Tewerkstellingsgroei',
  ];

  public function __construct(
    private int $year1,
    private int $year2,
    private int $year3
  ) {}

  public function getKpiRow(string $kpi): array {
    // generate the name of the function to call, based on the kpi code
    $kpiFunctionName = 'get' . str_replace(' ', '', $kpi);

    return [
      'civicrm_contact_column1' => $kpi,
      'civicrm_contact_column2' => $this->kpiCodeAndDescription[$kpi],
      'civicrm_contact_column3' => $this->$kpiFunctionName($this->year1),
      'civicrm_contact_column4' => $this->$kpiFunctionName($this->year2),
      'civicrm_contact_column5' => $this->$kpiFunctionName($this->year3),
      'civicrm_contact_column6' => '',
      'civicrm_contact_column7' => '',
    ];
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

  public function getDBC2(int $year, bool $justCount = TRUE ) {
    $memberships = \Civi\Api4\Membership::get(FALSE)
      ->selectRowCount()
      ->addSelect('contact_id.display_name', 'start_date', 'end_date', 'membership_type_id:label')
      ->addWhere('start_date', '<=', "$year-12-31")
      ->addWhere('end_date', '>=', "$year-01-01")
      ->addWhere('membership_type_id:label', '=', 'Geassocieerd lid - Partner')
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

  public function getDBC3(int $year, bool $justCount = TRUE) {
    $result = [];

    $actorsBetalendLidbedrijf = $this->getActorsBetalendLidbedrijf($year);
    $actorsKennisinstelling = $this->getActorsKennisinstelling($year);
    $actorsNietLid = $this->getActorsNietLid($year);

    $actors = array_merge($actorsBetalendLidbedrijf, $actorsKennisinstelling, $actorsNietLid);

    if ($justCount) {
      return count($actors);
    }

    $listItems = '';
    sort($actors);
    foreach ($actors as $actor) {
      $listItems .= '<li>' . $actor . '</li>';
    }

    return [count($actors), $listItems];
  }

  public function getCPI1(int $year, bool $justCount = TRUE) {
  }

  public function getCPI2(int $year, bool $justCount = TRUE) {
  }

  public function getCPI3(int $year, bool $justCount = TRUE) {
  }

  public function getCPI4(int $year, bool $justCount = TRUE) {
  }

  public function getCPI5(int $year, bool $justCount = TRUE) {
  }

  public function getCPI6(int $year, bool $justCount = TRUE) {
  }

  public function getCPI7(int $year, bool $justCount = TRUE) {
  }

  public function getCPI7Bis(int $year, bool $justCount = TRUE) {
  }

  public function getCPI8(int $year, bool $justCount = TRUE) {
  }

  public function getCPI9(int $year, bool $justCount = TRUE) {
    return 'monitoring';
  }

  public function getCPI10(int $year, bool $justCount = TRUE) {
    return 'monitoring';
  }

  public function getCPI11(int $year, bool $justCount = TRUE) {
    return 'monitoring';
  }

  private function getActorsBetalendLidbedrijf(int $year): array {
    static $cachedList = null;

    if (!empty($cachedList)) {
      return $cachedList;
    }

    $memberships = \Civi\Api4\Membership::get(FALSE)
      ->addSelect('contact_id', 'contact_id.display_name', 'start_date', 'end_date', 'membership_type_id:label')
      ->addWhere('start_date', '<=', "$year-12-31")
      ->addWhere('end_date', '>=', "$year-01-01")
      ->addWhere('membership_type_id:label', 'IN', ['Premium', 'Strategisch', 'Standaard', 'Verkennend', 'Dochter van premium of strategisch lid'])
      ->addWhere('owner_membership_id', 'IS NULL')
      ->execute();

    $list = [];
    foreach ($memberships as $membership) {
      $list[$membership['contact_id']] = $membership['contact_id.display_name'] . ' (' . $membership['membership_type_id:label'] . ' lid van/tot: ' . $membership['start_date'] . ' - ' . $membership['end_date'] . ')';
    }

    $cachedList = $list;
    return $list;
  }

  private function getActorsKennisinstelling(int $year): array {
    static $cachedList = null;

    if (!empty($cachedList)) {
      return $cachedList;
    }

    $tagIdResearchAndDevelopment = 22;
    $contacts = \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id', 'display_name', 'membership.membership_type_id:label', 'membership.start_date', 'membership.end_date')
      ->addJoin('EntityTag AS entity_tag', 'INNER', ['id', '=', 'entity_tag.entity_id'], ['entity_tag.entity_table', '=', "'civicrm_contact'"], ['entity_tag.tag_id', '=', $tagIdResearchAndDevelopment])
      ->addJoin('Membership AS membership', 'INNER', ['membership.contact_id', '=', 'id'])
      ->addWhere('membership.start_date', '<=', "$year-12-31")
      ->addWhere('membership.end_date', '>=', "$year-01-01")
      ->addWhere('membership.membership_type_id:label', '=', 'Geassocieerd lid - Partner')
      ->addWhere('membership.owner_membership_id', 'IS NULL')
      ->addWhere('is_deleted', '=', FALSE)
      ->execute();

    $list = [];
    foreach ($contacts as $contact) {
      $list[$contact['id']] = $contact['display_name'] . ' (Kenmerk Research & Development + ' . $contact['membership.membership_type_id:label'] . ' van/tot: ' . $contact['membership.start_date'] . ' - ' . $contact['membership.end_date'] . ')';
    }

    $cachedList = $list;
    return $list;
  }

  private function getActorsNietLid(int $year): array {
    static $cachedList = null;

    if (!empty($cachedList)) {
      return $cachedList;
    }

    $sql = "
      select
        c.id,
        c.display_name
      from
        civicrm_contact c
      where
        c.contact_type = 'Organization'
      and
        c.is_deleted = 0
      having
        c.id not in (select m.contact_id from civicrm_membership m where m.start_date <= '$year-12-31' and m.end_date >= '$year-01-01')
      and
        c.id in (select p.contact_id from civicrm_participant p inner join civicrm_event e on e.id = p.event_id where year(e.start_date) = $year)
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    $list = [];
    while ($dao->fetch()) {
      $list[$dao->id] = $dao->display_name;
    }

    $cachedList = $list;
    return $list;
  }

  private function getPaidEventParticipation(int $year, int $actorId): array {
    $participants = \Civi\Api4\Participant::get(FALSE)
      ->addSelect('event_id.title')
      ->addWhere('Kosten.Betalend_', '=', TRUE)
      ->addWhere('contact_id', '=', $actorId)
      ->addWhere('event_id.start_date', '>=', "$year-01-01 00:06:00")
      ->addWhere('event_id.start_date', '<=', "$year-12-31 23:59:00")
      ->addOrderBy('event_id.start_date', 'ASC')
      ->execute();

    $list = [];
    foreach ($participants as $participant) {
      $list[] = $participant['event_id.title'];
    }

    return $list;
  }

  private function getFreeEventParticipation(int $year, int $actorId): array {
    $participants = \Civi\Api4\Participant::get(FALSE)
      ->addSelect('event_id.title')
      ->addWhere('Kosten.Betalend_', '=', FALSE)
      ->addWhere('contact_id', '=', $actorId)
      ->addWhere('event_id.start_date', '>=', "$year-01-01 00:06:00")
      ->addWhere('event_id.start_date', '<=', "$year-12-31 23:59:00")
      ->addOrderBy('event_id.start_date', 'ASC')
      ->execute();

    $list = [];
    foreach ($participants as $participant) {
      $list[] = $participant['event_id.title'];
    }

    return $list;
  }
}
