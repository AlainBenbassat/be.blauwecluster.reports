<?php
use CRM_Reports_ExtensionUtil as E;

class CRM_Reports_Form_Report_KPI2021 extends CRM_Report_Form {
  private $year1 = 2021;
  private $year2;
  private $year3;
  private $year4;

  public function __construct() {
    $this->year2 = $this->year1 + 1;
    $this->year3 = $this->year1 + 2;
    $this->year4 = $this->year1 + 3;

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
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column3' => [
            'title' => $this->year2,
            'required' => TRUE,
            'dbAlias' => '1',
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column4' => [
            'title' => $this->year3,
            'required' => TRUE,
            'dbAlias' => '1',
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column5' => [
            'title' => $this->year4,
            'required' => TRUE,
            'dbAlias' => '1',
            'type' => CRM_Utils_Type::T_INT,
          ],
          'column6' => [
            'title' => 'Cumulatief / Gemiddeld',
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

    // hyperlinks to KPI detail page
    $linksToDetailsPage = 'Bekijk de details: ';
    $years = [$this->year1, $this->year2, $this->year3, $this->year4];
    foreach ($years as $year) {
      $url = CRM_Utils_System::url('civicrm/kpi-details', 'reset=1&year=' . $year);
      $link = '<a href="' . $url . '">' . $year . '</a>';

      if ($year == $this->year1) {
        $linksToDetailsPage .= $link;
      }
      else {
        $linksToDetailsPage .= ', ' . $link;
      }
    }
    $this->assign('linksToDetailsPage', $linksToDetailsPage);

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
    $kpiHelper = new CRM_BlauweClusterKPI();

    // overwrite standard rows array
    $rows = [];

    /****************************************************
     * Categorie 1
     ****************************************************/
    $row = [];
    $row['civicrm_contact_column1'] = '<strong>CATEGORIE 1: HEFBOOM</strong>';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'H 1. Bijkomende omzet in Vlaanderen bij een of meer clusterleden gekoppeld aan de binnen de cluster ontwikkelde activiteiten (MEUR)';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, 'EUR');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'H-1 bis. Bijkomende omzet gerealiseerd in internationale projecten bij een of meer clusterleden gekoppeld aan de binnen de cluster ontwikkelde activiteiten (MEUR)';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, 'EUR');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'H-2. Aantal bijkomende VTE\'s in Vlaanderen gekoppeld aan de binnen de cluster ontwikkelde activiteiten';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'H-3. Bijkomende investeringen in Vlaanderen gekoppeld aan de binnen de cluster ontwikkelde activiteiten (MEUR)';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    /****************************************************
     * Categorie 2
     ****************************************************/
    $row = [];
    $row['civicrm_contact_column1'] = '<strong>CATEGORIE 2: CLUSTERWERKING</strong>';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-1. Aantal actieve clusterleden';
    $row['civicrm_contact_column2'] = $kpiHelper->getC1($this->year1, 'all', TRUE, FALSE);
    $row['civicrm_contact_column3'] = $kpiHelper->getC1($this->year2, 'all', TRUE, FALSE);
    $row['civicrm_contact_column4'] = $kpiHelper->getC1($this->year3, 'all', TRUE, FALSE);
    $row['civicrm_contact_column5'] = $kpiHelper->getC1($this->year4, 'all', TRUE, FALSE);
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-1 bis. Aantal actieve KMO-clusterleden';
    $row['civicrm_contact_column2'] = $kpiHelper->getC1($this->year1, 'all', TRUE, TRUE);
    $row['civicrm_contact_column3'] = $kpiHelper->getC1($this->year2, 'all', TRUE, TRUE);
    $row['civicrm_contact_column4'] = $kpiHelper->getC1($this->year3, 'all', TRUE, TRUE);
    $row['civicrm_contact_column5'] = $kpiHelper->getC1($this->year4, 'all', TRUE, TRUE);
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-2. Aantal netwerkevents georganiseerd door de cluster';
    $row['civicrm_contact_column2'] = $kpiHelper->getC2($this->year1, TRUE);
    $row['civicrm_contact_column3'] = $kpiHelper->getC2($this->year2, TRUE);
    $row['civicrm_contact_column4'] = $kpiHelper->getC2($this->year3, TRUE);
    $row['civicrm_contact_column5'] = $kpiHelper->getC2($this->year4, TRUE);
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-3. Aantal unieke ondernemingen die kennis verkrijgen via andere activiteiten dan samenwerkingsprojecten';
    $row['civicrm_contact_column2'] = $kpiHelper->getC3($this->year1, TRUE);
    $row['civicrm_contact_column3'] = $kpiHelper->getC3($this->year2, TRUE);
    $row['civicrm_contact_column4'] = $kpiHelper->getC3($this->year3, TRUE);
    $row['civicrm_contact_column5'] = $kpiHelper->getC3($this->year4, TRUE);
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-4. Aantal gegenereerde samenwerkingsinitiatieven tussen minstens 3 ondernemingen';
    $row['civicrm_contact_column2'] = $kpiHelper->getC4($this->year1, TRUE);
    $row['civicrm_contact_column3'] = $kpiHelper->getC4($this->year2, TRUE);
    $row['civicrm_contact_column4'] = $kpiHelper->getC4($this->year3, TRUE);
    $row['civicrm_contact_column5'] = $kpiHelper->getC4($this->year4, TRUE);
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-5. Aantal unieke ondernemingen betrokken in samenwerkingsinitiatieven met minstens 3 ondernemingen';
    $c5y1 = $kpiHelper->getC5($this->year1, TRUE, FALSE);
    $c5y2 = $kpiHelper->getC5($this->year2, TRUE, FALSE);
    $c5y3 = $kpiHelper->getC5($this->year3, TRUE, FALSE);
    $c5y4 = $kpiHelper->getC5($this->year4, TRUE, FALSE);
    $row['civicrm_contact_column2'] = $c5y1;
    $row['civicrm_contact_column3'] = $c5y2;
    $row['civicrm_contact_column4'] = $c5y3;
    $row['civicrm_contact_column5'] = $c5y4;
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-5 bis. Het aandeel van KMO\'s betrokken in samenwerkingsinitiatieven tussen minstens 3 ondernemingen';
    $row['civicrm_contact_column2'] = round($kpiHelper->getC5($this->year1, TRUE, TRUE) / $c5y1 * 100) . '%';
    $row['civicrm_contact_column3'] = round($kpiHelper->getC5($this->year2, TRUE, TRUE) / $c5y2 * 100) . '%';
    $row['civicrm_contact_column4'] = round($kpiHelper->getC5($this->year3, TRUE, TRUE) / $c5y3 * 100) . '%';
    $row['civicrm_contact_column5'] = round($kpiHelper->getC5($this->year3, TRUE, TRUE) / $c5y4 * 100) . '%';
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'C-6. Tevredenheid';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    /****************************************************
     * Categorie 3
     ****************************************************/
    $row = [];
    $row['civicrm_contact_column1'] = '<strong>CATEGORIE 3: SPECIFIEKE DOELSTELLINGEN</strong>';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'S-1. Uitbouw van een publiek toegankelijke dataset m.b.t. de economische gegevens van de blauwe economie';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'S-2. Aantal samenwerkingsovereenkomsten met andere initiatieven in Vlaanderen of internationaal';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, '');
    $rows[] = $row;

    $row = [];
    $row['civicrm_contact_column1'] = 'S-2. Aantal samenwerkingsovereenkomsten met andere initiatieven in Vlaanderen of internationaal';
    $row['civicrm_contact_column2'] = '';
    $row['civicrm_contact_column3'] = '';
    $row['civicrm_contact_column4'] = '';
    $row['civicrm_contact_column5'] = '';
    $row['civicrm_contact_column6'] = '';
    $this->rowPostProcess($row, '');
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

  public function rowPostProcess(&$row, $unit) {
    $valSum = '';
    $valAvg = '';

    // get the column values
    $val1 = $row['civicrm_contact_column2'];
    $val2 = $row['civicrm_contact_column3'];
    $val3 = $row['civicrm_contact_column4'];
    $val4 = $row['civicrm_contact_column5'];

    // calculate sum and average for numeric values
    if (is_numeric($val1) && is_numeric($val2) && is_numeric($val3)) {
      // calculate sum
      $valSum = $val1 + $val2 + $val3 + $val4;

      // calculate average
      $valAvg = round($valSum / 4, 2);
    }

    // add unit to all row values
    if ($unit) {
      $row['civicrm_contact_column2'] .= $row['civicrm_contact_column2'] . ' ' . $unit;
      $row['civicrm_contact_column3'] .= $row['civicrm_contact_column3'] . ' ' . $unit;
      $row['civicrm_contact_column4'] .= $row['civicrm_contact_column4'] . ' ' . $unit;
      $row['civicrm_contact_column5'] .= $row['civicrm_contact_column4'] . ' ' . $unit;

      $valSum .= ' ' . $unit;
      $valAvg .= ' ' . $unit;
    }

    // fill in column 6
    $row['civicrm_contact_column6'] = "$valSum / $valAvg";
  }



}
