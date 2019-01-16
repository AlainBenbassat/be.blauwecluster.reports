<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Form_Report_KPI extends CRM_Report_Form {
  private $year1 = 2018;
  private $year2;
  private $year3;

  public function __construct() {
    $this->year2 = $this->year1 + 1;
    $this->year3 = $this->year1 + 2;

    $this->_columns = [
      'civicrm_contact' => [
        'fields' => [
          'column1' => [
            'title' => '',
            'required' => TRUE,
            'dbAlias' => '1',
          ],
          'column2' => [
            'title' => $this->year1,
            'required' => TRUE,
            'dbAlias' => '1',
          ],
          'column3' => [
            'title' => $this->year2,
            'required' => TRUE,
            'dbAlias' => '1',
          ],
          'column4' => [
            'title' => $this->year3,
            'required' => TRUE,
            'dbAlias' => '1',
          ],
        ],
      ],
    ];

    parent::__construct();
  }

  public function preProcess() {
    $this->assign('reportTitle', "KPI's Blauwe Cluster");
    parent::preProcess();
  }

  function postProcess() {
    $this->beginPostProcess();

    $sql = $this->buildQuery(TRUE);

    $rows = [];
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }


  public function select() {
    $select = $this->_columnHeaders = array();

    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (CRM_Utils_Array::value('required', $field) ||
            CRM_Utils_Array::value($fieldName, $this->_params['fields'])
          ) {
            if ($tableName == 'civicrm_address') {
              $this->_addressField = TRUE;
            }
            elseif ($tableName == 'civicrm_email') {
              $this->_emailField = TRUE;
            }
            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = CRM_Utils_Array::value('type', $field);
          }
        }
      }
    }

    $this->_select = "SELECT " . implode(', ', $select);
  }

  public function from() {
    $from = "
      FROM
        civicrm_contact {$this->_aliases['civicrm_contact']}
    ";

    $this->_from = $from;
  }

  public function where() {
    $this->_where = "WHERE id = 1 ";
  }

  function alterDisplay(&$rows) {
    $rows = [];

    /****************************************************
     * I
     ****************************************************/
    $row = [];
    $row['civicrm_contact_column1'] = '<strong>I.</strong>';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Bijkomende omzet in Vlaanderen bij een of meer clusterleden gekoppeld aan de binnen de cluster ontwikkelde activiteiten (MEUR)';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Bijkomende omzet gerealiseerd in internationale projecten bij een of meer clusterleden gekoppeld aan de binnen de cluster ontwikkelde activiteiten (MEUR)';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal bijkomende FTE\'s in Vlaanderen gekoppeld aan de binnen de cluster ontwikkelde activiteiten';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Bijkomende investeringen in Vlaanderen gekoppeld aan de binnen de cluster ontwikkelde activiteiten (MEUR)';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    /****************************************************
     * II
     ****************************************************/
    $row = [];
    $row['civicrm_contact_column1'] = '<strong>II.</strong>';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal actieve clusterleden';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal actieve KMO-clusterleden';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal netwerkevents georganiseerd door de cluster';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal unieke ondernemingen die kennis verkrijgen via andere activiteiten dan samenwerkingsprojecten';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal gegenereerde samenwerkingsinitiatieven tussen minstens 3 ondernemingen';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Aantal unieke ondernemingen betrokken in samenwerkingsinitiatieven met minstens 3 ondernemingen';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Het aandeel van KMO\'s betrokken in samenwerkingsinitiatieven tussen minstens 3 ondernemingen';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Tevredenheid';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    /****************************************************
     * III
     ****************************************************/
    $row = [];
    $row['civicrm_contact_column1'] = '<strong>III.</strong>';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Uitbouw van een publiek toegankelijke dataset m.b.t. de economische gegevens van de blauwe economie';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'Samenwerkingsovereenkomsten met andere initiatieven in Vlaanderen of internationaal';
    $row[$this->year1] = '';
    $row[$this->year2] = '';
    $row[$this->year3] = '';
    $rows[] = $row;

    /*
     * custom drupal form met id en cs persoon
     * gegevens werkgever ophalen
     * vragen/antwoorden komen overeen met custom fields van activiteiten van een specifiek type (bv. KPI bevraging)
     * form dynamisch opbouwen op basis van vragen
     * Hoe kies ik het jaar? (op basis van huidige maand/jaar?
     * slechts 1 activiteit per jaar
     *
     * voor kpi rapport: alle activiteiten van type bevraging met jaar X
     * zo kan je de detail ook terugvinden
     */
  }
}
