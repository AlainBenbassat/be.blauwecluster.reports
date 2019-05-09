<?php
/**
 * Created by PhpStorm.
 * User: alain
 * Date: 12/04/19
 * Time: 13:09
 */

class CRM_BlauweClusterKPI {
  public function getC1($year) {
    return '';
  }

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
    ";

    $n = CRM_Core_DAO::singleValueQuery($sql);

    return $n;
  }

  public function getC3Details($year) {
    $sql = "
      select
        distinct c.display_name
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
      order by 
        1
    ";

    $dao = CRM_Core_DAO::executeQuery($sql);

    return $dao;
  }



}