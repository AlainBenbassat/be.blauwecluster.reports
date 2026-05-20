<?php

class CRM_BlauweClusterKPI2025 {

  private $kpiCodeAndDescription = [
    'DBC1' => 'Aantal betalende (private) leden',
    'DBC2' => 'Aantal geassocieerde leden - partners',
    'DBC3' => 'Aantal actoren binnen de cluster',
    'CPI1' => 'Aantal actieve actoren binnen de cluster',
    'CPI2' => 'Aantal actieve KMO\'s binnen de cluster',
    'CPI3' => 'Aantal projecten dat kan leiden tot (of gericht is op) innovatie',
    'CPI4' => 'Aantal internationale samenwerkingsverbanden via clusterinitiatieven',
    'CPI5' => 'Aandeel van clusterprojecten die leiden tot innovatie met een bijdrage aan maatschappelijke uitdagingen t.o.v. alle clusterprojecten',
    'CPI6' => 'Aantal unieke ondernemingen die kennis verkrijgen via andere activiteiten dan samenwerkingsprojecten',
    'CPI7' => 'Aantal unieke ondernemingen betrokken in samenwerkingsinitiatieven met minstens 3 ondernemingen',
    'CPI7Bis' => 'Het aandeel van KMO\'s betrokken in samenwerkingsinitiatieven tussen minstens 3 ondernemingen',
    'CPI8' => 'Deelname in aantal goedgekeurde interclusterprojecten per jaar',
    'CPI9' => 'Investeringen',
    'CPI10' => 'Omzetgroei',
    'CPI11' => 'Tewerkstellingsgroei',
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
      ->addWhere('membership_type_id:label', 'IN', [
        'Premium',
        'Strategisch',
        'Standaard',
        'Verkennend'
      ])
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
    $actors = $this->getActors($year, TRUE);

    if ($justCount) {
      return count($actors);
    }

    $listItems = '';
    foreach ($actors as $actor) {
      $listItems .= '<li>' . $actor . '</li>';
    }

    return [count($actors), $listItems];
  }

  public function getCPI1(int $year, bool $justCount = TRUE) {
    $activeActors = [];
    $actors = $this->getActors($year, FALSE);

    foreach ($actors as $actorId => $actor) {
      $projects = $this->getClusterProjects($actorId, $year);
      $otherProjects = $this->getOtherProjects($actorId, $year);
      $events = $this->getEvents($actorId, $year);
      $gathering = $this->getGatherings($actorId, $year);

      $details = $this->formatCpiDetails($projects, $otherProjects, $events, $gathering);

      if ($details) {
        $activeActors[] = $actor . $details;
      }
    }

    if ($justCount) {
      return count($activeActors);
    }

    $listItems = '';
    foreach ($activeActors as $actor) {
      $listItems .= '<li>' . $actor . '</li>';
    }

    return [count($activeActors), $listItems];
  }

  public function getCPI2(int $year, bool $justCount = TRUE) {
    $activeActors = [];
    $actors = $this->getKMOs();

    foreach ($actors as $actorId => $actor) {
      $projects = $this->getClusterProjects($actorId, $year);
      $otherProjects = $this->getOtherProjects($actorId, $year);
      $events = $this->getEvents($actorId, $year);
      $gathering = $this->getGatherings($actorId, $year);

      $details = $this->formatCpiDetails($projects, $otherProjects, $events, $gathering);

      if ($details) {
        $activeActors[] = $actor . $details;
      }
    }

    if ($justCount) {
      return count($activeActors);
    }

    $listItems = '';
    foreach ($activeActors as $actor) {
      $listItems .= '<li>' . $actor . '</li>';
    }

    return [count($activeActors), $listItems];
  }

  public function getCPI3(int $year, bool $justCount = TRUE) {
    $caseTypes = '4, 10, 11'; // projects, int. projects, intercluster projects

    $sql = "
      select
        ca.subject,
        ca.start_date,
        ca.end_date
      from
        civicrm_case ca
      inner join
        civicrm_value_extra_info_do_14 ei on ei.entity_id = ca.id
      where
        ca.case_type_id in ($caseTypes)
        and ei.jaar_goedkeuring_beslissing_73 = $year
        and ca.is_deleted = 0
      order by
        ca.subject
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    $cases = [];
    while ($dao->fetch()) {
      $cases[] = $dao->subject . ' (' . $dao->start_date . ' - ' . $dao->end_date . ')';
    }

    if ($justCount) {
      return count($cases);
    }

    $listItems = '';
    foreach ($cases as $case) {
      $listItems .= '<li>' . $case . '</li>';
    }

    return [count($cases), $listItems];
  }

  public function getCPI4(int $year, bool $justCount = TRUE) {
    $commonMarketResearchProjects = $this->getCommonMarketResearchProjects($year);
    $internationalProjectsWithActors = $this->getInternationalProjectsWithActors($year);
    $collaborationProjects = $this->getCollaborationProjects($year);
    $internationalProjectsWithDBC = $this->getInternationalProjectsWithDBC($year);
    $internationalEvents = $this->getInternationalEvents($year);

    $internationalActionsTemp = array_replace(
      $commonMarketResearchProjects,
      $internationalProjectsWithActors,
      $collaborationProjects,
      $internationalProjectsWithDBC,
      $internationalEvents,
    );

    $internationalActions = array_unique($internationalActionsTemp);

    if ($justCount) {
      return count($internationalActions);
    }

    $listItems = '';
    foreach ($internationalActions as $internationalAction) {
      $listItems .= '<li>' . $internationalAction . '</li>';
    }

    return [count($internationalActions), $listItems];
  }

  public function getCPI5(int $year, bool $justCount = TRUE) {}

  public function getCPI6(int $year, bool $justCount = TRUE) {
    // = old KPI 3
    $oldKpiHelper = new CRM_BlauweClusterKPI();
    $mixedData = $oldKpiHelper->getC3($year, $justCount);
    if ($justCount) {
      return $mixedData;
    }
    else {
      $listItems = '';
      while ($mixedData->fetch()) {
        $listItems .= '<li>' . $mixedData->item . '</li>';
      }

      return [$mixedData->N, $listItems];
    }
  }

  public function getCPI7(int $year, bool $justCount = TRUE) {
    // = old KPI 5
    $oldKpiHelper = new CRM_BlauweClusterKPI();
    $mixedData = $oldKpiHelper->getC5($year, $justCount, FALSE);
    if ($justCount) {
      return $mixedData;
    }
    else {
      $listItems = '';
      while ($mixedData->fetch()) {
        $listItems .= '<li>' . $mixedData->item . '</li>';
      }

      return [$mixedData->N, $listItems];
    }
  }

  public function getCPI7Bis(int $year, bool $justCount = TRUE) {
    // = old KPI 5 bis
    $oldKpiHelper = new CRM_BlauweClusterKPI();
    $mixedData = $oldKpiHelper->getC5($year, $justCount, TRUE);
    if ($justCount) {
      return $mixedData;
    }
    else {
      $listItems = '';
      while ($mixedData->fetch()) {
        $listItems .= '<li>' . $mixedData->item . '</li>';
      }

      return [$mixedData->N, $listItems];
    }
  }

  public function getCPI8(int $year, bool $justCount = TRUE) {}

  public function getCPI9(int $year, bool $justCount = TRUE) {
    return 'monitoring';
  }

  public function getCPI10(int $year, bool $justCount = TRUE) {
    return 'monitoring';
  }

  public function getCPI11(int $year, bool $justCount = TRUE) {
    return 'monitoring';
  }

  private function getActors(int $year, bool $addDetails): array {
    static $cachedList = NULL;

    if (!empty($cachedList)) {
      return $cachedList;
    }

    $actorsBetalendLidbedrijf = $this->getActorsBetalendLidbedrijf($year, $addDetails);
    $actorsKennisinstelling = $this->getActorsKennisinstelling($year, $addDetails);
    $actorsNietLid = $this->getActorsNietLid($year, $addDetails);

    $actors = array_replace($actorsBetalendLidbedrijf, $actorsKennisinstelling, $actorsNietLid);
    natcasesort($actors);

    //$cachedList = $actors;
    return $actors;
  }

  private function getKMOs() {
    $list = [];

    $sql = "
      SELECT
        c.id,
        c.display_name
      from
        civicrm_contact c
      inner join
        civicrm_value_organisatie_i_5 i on i.entity_id = c.id
      where
        grootte_27 in (1, 2)
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[$dao->id] = $dao->display_name;
    }

    return $list;
  }

  private function getActorsBetalendLidbedrijf(int $year, bool $addDetails): array {
    $memberships = \Civi\Api4\Membership::get(FALSE)
      ->addSelect('contact_id', 'contact_id.display_name', 'start_date', 'end_date', 'membership_type_id:label')
      ->addWhere('start_date', '<=', "$year-12-31")
      ->addWhere('end_date', '>=', "$year-01-01")
      ->addWhere('membership_type_id:label', 'IN', [
        'Premium',
        'Strategisch',
        'Standaard',
        'Verkennend',
        'Dochter van premium of strategisch lid'
      ])
      ->addWhere('owner_membership_id', 'IS NULL')
      ->execute();

    $list = [];
    foreach ($memberships as $membership) {
      if ($addDetails) {
        $list[$membership['contact_id']] = $membership['contact_id.display_name'] . ' (' . $membership['membership_type_id:label'] . ' lid van/tot: ' . $membership['start_date'] . ' - ' . $membership['end_date'] . ')';
      }
      else {
        $list[$membership['contact_id']] = $membership['contact_id.display_name'];
      }
    }

    return $list;
  }

  private function getActorsKennisinstelling(int $year, bool $addDetails): array {
    $tagIdResearchAndDevelopment = 22;
    $contacts = \Civi\Api4\Contact::get(FALSE)
      ->addSelect('id', 'display_name', 'membership.membership_type_id:label', 'membership.start_date', 'membership.end_date')
      ->addJoin('EntityTag AS entity_tag', 'INNER', [
        'id',
        '=',
        'entity_tag.entity_id'
      ], [
        'entity_tag.entity_table',
        '=',
        "'civicrm_contact'"
      ], ['entity_tag.tag_id', '=', $tagIdResearchAndDevelopment])
      ->addJoin('Membership AS membership', 'INNER', [
        'membership.contact_id',
        '=',
        'id'
      ])
      ->addWhere('membership.start_date', '<=', "$year-12-31")
      ->addWhere('membership.end_date', '>=', "$year-01-01")
      ->addWhere('membership.membership_type_id:label', '=', 'Geassocieerd lid - Partner')
      ->addWhere('membership.owner_membership_id', 'IS NULL')
      ->addWhere('is_deleted', '=', FALSE)
      ->execute();

    $list = [];
    foreach ($contacts as $contact) {
      if ($addDetails) {
        $list[$contact['id']] = $contact['display_name'] . ' (Kenmerk Research & Development + ' . $contact['membership.membership_type_id:label'] . ' van/tot: ' . $contact['membership.start_date'] . ' - ' . $contact['membership.end_date'] . ')';
      }
      else {
        $list[$contact['id']] = $contact['display_name'];
      }
    }

    return $list;
  }

  private function getActorsNietLid(int $year, bool $addDetails): array {
    $sql = "
      select
        c.id,
        c.display_name,
        count(e.id) aantal_evenementen,
        GROUP_CONCAT(concat(e.start_date, ' ', e.title)) evenementen,
        sum(ifnull(k.betalend__72, 0)) aantal_betalend
      from
        civicrm_contact c
      inner join
        civicrm_participant p on p.contact_id = c.id
      inner join
        civicrm_event e on e.id = p.event_id
      left outer JOIN
        civicrm_value_kosten_13 k on p.id = k.entity_id
      where
        c.contact_type = 'Organization'
      AND
        c.id <> 1
      and
        c.is_deleted = 0
      and
        c.id not in (select m.contact_id from civicrm_membership m where m.start_date <= '$year-12-31' and m.end_date >= '$year-01-01')
      and
        year(e.start_date) = $year
      and
        p.status_id in (1, 2)
      group by
        c.id
      having
        count(e.id) > 1 or sum(ifnull(k.betalend__72, 0)) >= 1
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    $list = [];
    while ($dao->fetch()) {
      if ($addDetails) {
        $list[$dao->id] = $dao->display_name . ' (Geen lid - aantal evenementen: ' . $dao->aantal_evenementen . ', waarvan ' . $dao->aantal_betalend . ' betalend - ' . $dao->evenementen . ')';
      }
      else {
        $list[$dao->id] = $dao->display_name;
      }
    }

    return $list;
  }

  private function getClusterProjects(int $contactId, int $year) {
    $caseTypes = '4, 10, 11'; // 4 = project, 10 = internationaal project, 11 = intercluster project
    $relTypeBetrokkenOrganisatie = 19;
    $list = [];

    $sql = "
      select
        distinct ca.subject cases
      from
        civicrm_case ca
      inner join
        civicrm_relationship r on r.case_id = ca.id
      where
        ca.case_type_id  in ($caseTypes)
        and ifnull(year(ca.start_date), '1000') <= $year
        and ifnull(year(ca.end_date), '3000') >= $year
        and relationship_type_id = $relTypeBetrokkenOrganisatie
        and ifnull(year(r.start_date), '1000') <= $year
        and ifnull(year(r.end_date), '3000') >= $year
        and (r.contact_id_a = $contactId or r.contact_id_b = $contactId)
        and ca.is_deleted = 0
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->cases;
    }

    return $list;
  }

  private function getOtherProjects(int $contactId, int $year) {
    $caseTypes = '5, 8, 9'; // cascase fin. = 9, demonstratie = 5, opleiding = 8
    $relTypeBetrokkenOrganisatie = 19;
    $list = [];

    $sql = "
      select
        distinct ca.subject cases
      from
        civicrm_case ca
      inner join
        civicrm_relationship r on r.case_id = ca.id
      where
        ca.case_type_id in ($caseTypes)
        and ifnull(year(ca.start_date), '1000') <= $year
        and ifnull(year(ca.end_date), '3000') >= $year
        and relationship_type_id = $relTypeBetrokkenOrganisatie
        and ifnull(year(r.start_date), '1000') <= $year
        and ifnull(year(r.end_date), '3000') >= $year
        and (r.contact_id_a = $contactId or r.contact_id_b = $contactId)
        and ca.is_deleted = 0
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->cases;
    }

    return $list;
  }

  private function getEvents(int $contactId, int $year) {
    $list = [];

    $sql = "
      select
        concat(DATE_FORMAT(e.start_date, '%d/%m/%Y'), ' ', e.title) evenementen
      from
        civicrm_participant p
      inner join
        civicrm_event e on e.id = p.event_id
      where
        year(e.start_date) = $year
      and
        p.status_id in (1, 2)
      and
        p.contact_id = $contactId
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->evenementen;
    }

    return $list;
  }

  private function getInternationalEvents(int $year) {
    $list = [];

    $sql = "
      select
        concat(DATE_FORMAT(e.start_date, '%d/%m/%Y'), ' ', e.title) evenementen
      from
        civicrm_event e
      where
        year(e.start_date) = $year
      and
        e.event_type_id = 11
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->evenementen;
    }

    return $list;
  }

  private function formatCpiDetails($projects, $otherProjects, $events, $gatherings) {
    $count = 0;
    $details = '';

    if ($projects) {
      $details .= 'Innovatieve clusterprojecten: ' . implode(',', $projects);
      $count += count($projects);
    }

    if ($otherProjects) {
      if ($details) {
        $details .= ', ';
      }
      $details .= 'Andere clusterprojecten: ' . implode(',', $otherProjects);
      $count += count($otherProjects);
    }

    if ($events) {
      if ($details) {
        $details .= ', ';
      }
      $details .= 'Evenementen: ' . implode(', ', $events);
      $count += count($events);
    }

    if ($gatherings) {
      if ($details) {
        $details .= ', ';
      }
      $details .= 'Bijeenkomsten: ' . implode(', ', $gatherings);
      $count += count($gatherings);
    }

    if ($count >= 2) {
      return " ($details)";
    }
    else {
      return '';
    }
  }

  private function getGatherings(int $contactId, int $year) {
    $list = [];

    $activities = \Civi\Api4\Activity::get(FALSE)
      ->addWhere('activity_type_id', '=', 1)
      ->addWhere('activity_date_time', '>=', "$year-01-01")
      ->addWhere('activity_date_time', '<=', "$year-12-31")
      ->addWhere('target_contact_id', '=', $contactId)
      ->addOrderBy('activity_date_time', 'ASC')
      ->execute();
    foreach ($activities as $activity) {
      $list[] = substr($activity['activity_date_time'], 0, 10);
    }

    return $list;
  }

  private function getInternationalProjectsWithActors(int $year): array {
    $list = [];
    $caseTypes = '10'; //  10 = internationaal project
    $relTypeBetrokkenOrganisatie = 19;
    $actorIds = array_keys($this->getActors($year, FALSE));
    if (empty($actorIds)) {
      return $list;
    }

    $sql = "
      select
        distinct ca.subject cases, group_concat(distinct c.display_name) orgs
      from
        civicrm_case ca
      inner join
        civicrm_relationship r on r.case_id = ca.id
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      inner join
        civicrm_value_extra_info_do_14 ei on ei.entity_id = ca.id
      where
        ca.case_type_id  in ($caseTypes)
        and relationship_type_id = $relTypeBetrokkenOrganisatie
        and ei.jaar_goedkeuring_beslissing_73 = $year
        and ca.is_deleted = 0
        and r.contact_id_b in (" . implode(',', $actorIds) . ")
      group by
        ca.id
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->cases . ' (' . $dao->orgs . ')';
    }

    return $list;
  }

  private function getInternationalProjectsWithDBC(int $year): array {
    $list = [];
    $caseTypes = '10'; // 10 = internationaal project
    $relTypeBetrokkenOrganisatie = 19;
    $actorIds = [1];

    $sql = "
      select
        distinct ca.subject cases, group_concat(distinct c.display_name) orgs
      from
        civicrm_case ca
      inner join
        civicrm_relationship r on r.case_id = ca.id
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      inner join
        civicrm_value_extra_info_do_14 ei on ei.entity_id = ca.id
      where
        ca.case_type_id  in ($caseTypes)
        and relationship_type_id = $relTypeBetrokkenOrganisatie
        and ei.jaar_goedkeuring_beslissing_73 = $year
        and ca.is_deleted = 0
        and r.contact_id_b in (" . implode(',', $actorIds) . ")
      group by
        ca.id
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->cases . ' (' . $dao->orgs . ')';
    }

    return $list;
  }

  private function getCommonMarketResearchProjects(int $year): array {
    $list = [];
    $caseTypes = '3'; // 3 = gemeenschappelijke marktverkenning
    $relTypeBetrokkenOrganisatie = 19;
    $actorIds = array_keys($this->getActors($year, FALSE));
    if (empty($actorIds)) {
      return $list;
    }

    $sql = "
      select
        distinct ca.subject cases, group_concat(distinct c.display_name) orgs
      from
        civicrm_case ca
      inner join
        civicrm_relationship r on r.case_id = ca.id
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      where
        ca.case_type_id  in ($caseTypes)
        and ifnull(year(ca.start_date), '1000') = $year
        and relationship_type_id = $relTypeBetrokkenOrganisatie
        and ca.is_deleted = 0
        and r.contact_id_b in (" . implode(',', $actorIds) . ")
      group by
        ca.id
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->cases . ' (' . $dao->orgs . ')';
    }

    return $list;
  }

  private function getCollaborationProjects(int $year): array {
    $list = [];
    $caseTypes = '6'; // 6 = samenwerkingsovereenkomst
    $relTypeBetrokkenOrganisatie = 19;
    $actorIds = [1];

    $sql = "
      select
        distinct ca.subject cases, group_concat(distinct c.display_name) orgs
      from
        civicrm_case ca
      inner join
        civicrm_relationship r on r.case_id = ca.id
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      where
        ca.case_type_id  in ($caseTypes)
        and ifnull(year(ca.start_date), '1000') = $year
        and relationship_type_id = $relTypeBetrokkenOrganisatie
        and ca.is_deleted = 0
        and r.contact_id_b in (" . implode(',', $actorIds) . ")
      group by
        ca.id
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $list[] = $dao->cases . ' (' . $dao->orgs . ')';
    }

    return $list;
  }


}
