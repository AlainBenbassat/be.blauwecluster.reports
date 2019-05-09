<?php
/**
 * Created by PhpStorm.
 * User: alain
 * Date: 12/04/19
 * Time: 13:09
 */

class CRM_BlauweClusterKPI {
  /*
   * C1
   */
  public function getC1($year) {
    /*
     * HOE C1's TELLEN?
     *
     * Ipv te werken met getCx en getCxDetails
     * misschien werken met de detail query en voor de count met
     *  select count(*) from (DETAILQUERY) as counter
     */
    return '';
  }

  public function getC1Details($year, $section) {
    $sql = '';

    if ($section == 'members') {
      $sql = "
        select
          concat(c.display_name, ' (', GROUP_CONCAT(e.title SEPARATOR ', '), ')') as item
        from
          civicrm_contact c
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
          exists (
            select
              m.id 
            from
              civicrm_membership m
            where
              m.contact_id = c.id
            and 
              year(m.start_date) <= $year and year(m.end_date) >= $year
          )
        group by
          c.id
        having
          count(p.id) >= 2
        order by
          c.sort_name
      ";
    }
    elseif ($section == 'companies') {
      $sql = "
        select
          concat(c.display_name, ' (', GROUP_CONCAT(e.title SEPARATOR ', '), ')') as item
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
          ci.publiek_of_privaat__50 = 2
        group by
          c.id
        having
          count(p.id) >= 2
        order by
          c.sort_name
      ";
    }
    elseif ($section == 'collaborations') {
      $sql = "
        select 
          concat(c.display_name, ' (', GROUP_CONCAT(cs.subject SEPARATOR ', '), ')') as item
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
          rt.label_a_b = 'Betrokken organisatie'
        and 
          ci.publiek_of_privaat__50 = 2 
        and 
          (year(cs.start_date) = $year or (year(cs.start_date) <= $year and cs.status_id = 1)) 
        group by
          c.id
        order by
          c.sort_name              
      ";
    }

    $dao = CRM_Core_DAO::executeQuery($sql);

    return $dao;
  }

  /*
   * C1 bis
   */
  public function getC1bis($year) {
    return '';
  }

  /*
   * C2: aantal netwerkevents
   */
  public function getC2($year) {
    $sql = "
      select
        count(e.id)
      from
        civicrm_event e
      inner join
        civicrm_option_value et on e.event_type_id = et.value and et.option_group_id = 15
      where
        e.start_date between '$year-01-01 00:00:00' and '$year-12-31 23:59:59'      
      and
        et.label = 'Netwerkevent' 
    ";

    $n = CRM_Core_DAO::singleValueQuery($sql);

    return $n;
  }

  public function getC2Details($year) {
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
        start_date
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);

    return $dao;
  }

  /*
   * C3: aantal unieke ondernemingen (i.e. custom field = Privaat)
   * die deelgenomen hebben aan events (uitgezonderd WAR en RvB)
   */
  public function getC3($year) {
    $sql = "
      select
        count(distinct c.id)
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
    ";

    $n = CRM_Core_DAO::singleValueQuery($sql);

    return $n;
  }

  public function getC3Details($year) {
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

    $dao = CRM_Core_DAO::executeQuery($sql);

    return $dao;
  }



}