<?php

class CRM_BlauweClusterKPI {
  /*
   * C1
   */
  public function getC1($year, $section, $justCount = FALSE, $c1Bis = FALSE) {
    if ($justCount) {
      $fields = 'distinct c.display_name';
    }
    else {
      if ($section == 'members') {
        $fields = 'distinct c.display_name';
      }
      elseif ($section == 'collaborations') {
        // name of the company with it's cases
        $fields = "concat(c.display_name, ' (', GROUP_CONCAT(cs.subject SEPARATOR ', '), ')')";
      }
      else {
        // name of the company with it's events
        $fields = "concat(c.display_name, ' (', GROUP_CONCAT(e.title SEPARATOR ', '), ')')";
      }
    }

    if ($c1Bis) {
      $bis = ' AND ci.grootte_27 in (1,2) ';
    }
    else {
      $bis = ' ';
    }

    $sqlMembers = "
      select
        $fields as item
      from
        civicrm_contact c
      inner join
        civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
      inner join
        civicrm_membership m on m.contact_id = c.id
      where
        c.contact_type = 'Organization'
      and
        c.is_deleted = 0
      and
        ci.publiek_of_privaat__50 = 2 $bis
      and
        year(m.start_date) <= $year and year(m.end_date) >= $year
    ";

    $sqlCompanies = "
      select
        $fields as item
      from
        civicrm_contact c
      inner join
        civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
      inner join
        civicrm_participant p on p.contact_id = c.id
      inner join
        civicrm_event e on e.id = p.event_id
      where
        c.contact_type = 'Organization'
      and
        c.is_deleted = 0
      and
        year(e.start_date) = $year
      and
        p.status_id in (1, 2)
      and
        ci.publiek_of_privaat__50 = 2 $bis
      group by
        c.id
      having
        count(p.id) >= 2
    ";

    $sqlCollaborations = "
      select
        $fields as item
      from
        civicrm_case cs
      inner join
        civicrm_relationship r on r.case_id = cs.id
      inner join
        civicrm_relationship_type rt on r.relationship_type_id = rt.id
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      inner join
        civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
      where
        c.contact_type = 'Organization'
      and
        c.is_deleted = 0
      and
        cs.is_deleted = 0
      and
        rt.label_a_b = 'Betrokken organisatie'
      and
        ci.publiek_of_privaat__50 = 2 $bis
      and
        year(cs.start_date) <= $year and ifnull(year(cs.end_date), 3000) >= $year
      and
        cs.case_type_id in (3, 4, 5)
      group by
        c.id
    ";

    if ($justCount) {
      $sql = "$sqlMembers UNION $sqlCompanies UNION $sqlCollaborations";

      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao->N;
    }
    else {
      if ($section == 'members') {
        $dao = CRM_Core_DAO::executeQuery($sqlMembers . " order by c.sort_name");
      }
      elseif ($section == 'companies') {
        $dao = CRM_Core_DAO::executeQuery($sqlCompanies . " order by c.sort_name");
      }
      elseif ($section == 'collaborations') {
        $dao = CRM_Core_DAO::executeQuery($sqlCollaborations . " order by c.sort_name");
      }

      return $dao;
    }
  }

  /*
   * C2: aantal netwerkevents
   */
  public function getC2($year, $justCount) {
    $sql = "
      select
        concat(DATE_FORMAT(start_date, '%d/%m/%Y'), ' - ', e.title) item
      from
        civicrm_event e
      inner join
        civicrm_option_value et on e.event_type_id = et.value and et.option_group_id = 15
      where
        e.start_date between '$year-01-01 00:00:00' and '$year-12-31 23:59:59'
      and
        et.label = 'Netwerkevent'
      order by
        1
    ";

    if ($justCount) {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao->N;
    }
    else {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao;
    }
  }

  /*
   * C3: aantal unieke ondernemingen (i.e. custom field = Privaat)
   * die deelgenomen hebben aan events (uitgezonderd WAR en RvB)
   */
  public function getC3($year, $justCount) {
    $sql = "
      select
        concat(c.display_name, ' (', GROUP_CONCAT(e.title SEPARATOR ', '), ')') as item
      from
        civicrm_event e
      inner join
        civicrm_option_value et on e.event_type_id = et.value and et.option_group_id = 15
      inner join
        civicrm_participant p on p.event_id = e.id
      inner join
        civicrm_contact c on p.contact_id = c.id
      inner join
        civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
      where
        e.start_date between '$year-01-01 00:00:00' and '$year-12-31 23:59:59'
      and
        et.label not in('RvB', 'WAR')
      and
        p.status_id in (1, 2)
      and
        c.contact_type = 'Organization'
      and
        ci.publiek_of_privaat__50 = 2
      and
        c.is_deleted = 0
      group by
        c.id
      order by
        1
    ";

    if ($justCount) {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao->N;
    }
    else {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao;
    }
  }

  public function getC4($year, $justCount) {
    $sql = "
      select
        mcs.id case_id,
        mcs.subject,
        group_concat(mcs_contacts.id SEPARATOR ', ') org_ids,
        concat(
          DATE_FORMAT(mcs.start_date, '%d/%m/%Y'),
          ' - ',
          mcs.subject,
          ' (',
          GROUP_CONCAT(mcs_contacts.display_name SEPARATOR ', '),
          ')'
        ) item
      from
        civicrm_case mcs
      inner join
      (
        select
          cs.id case_id,
          c.id,
          c.display_name,
          ci.publiek_of_privaat__50,
          ci.grootte_27
        from
          civicrm_case cs
        inner join
          civicrm_case_contact cc on cs.id = cc.case_id
        inner join
           civicrm_contact c on c.id = cc.contact_id
        inner join
          civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
        where
          c.is_deleted = 0
        and
          ci.publiek_of_privaat__50 = 2
      union
        select
          r.case_id case_id,
          c.id,
          c.display_name,
          ci.publiek_of_privaat__50,
          ci.grootte_27
        from
          civicrm_relationship  r
        inner join
          civicrm_contact c on c.id = r.contact_id_b
        inner join
          civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
        where
          c.is_deleted = 0
        and
          ci.publiek_of_privaat__50 = 2
        and
          r.relationship_type_id = 19
      ) mcs_contacts
      on
        mcs_contacts.case_id = mcs.id
      where
        mcs.is_deleted = 0
      and
        year(mcs.start_date) <= $year
      and
        year(ifnull(mcs.end_date, '2999-12-31')) >= $year
      group by
        mcs.id,
        mcs.subject,
        mcs.start_date,
        mcs.end_date,
        mcs.status_id
      having
        count(mcs_contacts.id) >= 3
      order by
        mcs.start_date
    ";

    if ($justCount) {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao->N;
    }
    else {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao;
    }
  }

  public function getC5($year, $justCount, $c5Bis) {
    if ($c5Bis) {
      $enkelKMOs = ' AND ci.grootte_27 in (1,2) ';
    }
    else {
      $enkelKMOs = ' ';
    }

    // get the contact id's from the C4 query
    $contactIds = [];
    $dao = $this->getC4($year, FALSE);
    while ($dao->fetch()) {
      $contactIds[] = $dao->org_ids;
    }

    // build the SQL statement
    $ids = implode(',', $contactIds);
    $sql = "
      SELECT
        c.display_name item
      FROM
        civicrm_contact c
      inner join
        civicrm_value_organisatie_i_5 ci on c.id = ci.entity_id
      WHERE
        c.id in ($ids)
      and
        ci.publiek_of_privaat__50 = 2
      $enkelKMOs
      order by
        c.sort_name
    ";

    if ($justCount) {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao->N;
    }
    else {
      $dao = CRM_Core_DAO::executeQuery($sql);
      return $dao;
    }
  }
}
