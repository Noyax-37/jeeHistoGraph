<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/
/* * ***************************Includes********************************* */
require_once __DIR__ . '/../../../../core/php/core.inc.php';
class jeeHistoGraph extends eqLogic {
  /* * *************************Attributs****************************** */
  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */
  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */
  /* * ***********************Methode static*************************** */
  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  public static function cron() {}
  */
  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */
  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */
  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */
  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
  */
  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */
  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */
 
  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */
  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */
  /*
   * Permet d'indiquer des éléments supplémentaires à remonter dans les informations de configuration
   * lors de la création semi-automatique d'un post sur le forum community
   public static function getConfigForCommunity() {
      // Cette function doit retourner des infos complémentataires sous la forme d'un
      // string contenant les infos formatées en HTML.
      return "les infos essentiel de mon plugin";
   }
   */
  /* * *********************Méthodes d'instance************************* */
  // Fonction exécutée automatiquement avant la création de l'équipement
  public function preInsert() {
}
  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
    $configs = self::config();
    foreach ($configs as $key) {
        $this ->setConfiguration($key[0], $key[1]);
    }
    $this->save();
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }
  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {

 }
  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {

  }


  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
  }
  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }
  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }
  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

public static function config() {
    $defaultColors = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4','#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];
    // déterminer la config compléte avec les valeurs par défaut
    $config =   [ 
                    ["nbGraphs", 1], 
                    ["graphLayout", "auto"], 
                    ["periode_histo", 'nbJours'], 
                    ["delai_histo", 1], 
                    ["date_debut_histo", ''], 
                    ["date_debut_histo_2dates", ''],
                    ["date_fin_histo_2dates", '']
                ];
    for ($g = 1; $g <= 4; $g++) {
        $config[] = ["graph{$g}_type", 'line'];
        $config[] = ["graph{$g}_regroup", "aucun"];
        $config[] = ["graph{$g}_typeRegroup", "aucun"];
        $config[] = ["stacking_graph{$g}", "null"];
        $config[] = ["periode_histo_graph{$g}", "global"];
        $config[] = ["delai_histo_graph{$g}", ''];
        $config[] = ["date_debut_histo_graph{$g}", ''];
        $config[] = ["date_debut_histo_2dates_graph{$g}", ""];
        $config[] = ["date_fin_histo_2dates_graph{$g}", ''];
        $config[] = ["graph{$g}_bg_transparent", 1];
        $config[] = ["graph{$g}_bg_color", ""];
        $config[] = ["graph{$g}_bg_gradient_enabled", 0];
        $config[] = ["graph{$g}_bg_gradient_start", ""];
        $config[] = ["graph{$g}_bg_gradient_end", ""];
        $config[] = ["graph{$g}_bg_gradient_angle", 90];
        $config[] = ["titleGraph{$g}", "Titre Graph {$g}"];
        $config[] = ["graph{$g}_compare_type", 'none'];
        $config[] = ["graph{$g}_compare_month", "01"];
        $config[] = ["graph{$g}_rolling_start_month", "01"];
        $config[] = ["tooltip{$g}", 'regroup'];
        $config[] = ["graph{$g}_navigator", 1];
        $config[] = ["graph{$g}_barre", 1];
        $config[] = ["graph{$g}_buttons", 1];
        $config[] = ["graph{$g}_showLegend", 1];
        $config[] = ["graph{$g}_showTitle", 1];
        $config[] = ["graph{$g}_show_yAxis", 1];
        for ($i = 1; $i <= 10; $i++) {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $config[] = ["graph{$g}_index{$index}_nom", ''];
            $config[] = ["graph{$g}_curve{$i}_type", "inherit_curve"];
            $config[] = ["graph{$g}_color{$i}", $defaultColors[$i-1]];
            $config[] = ["graph{$g}_cmdGraphe{$index}", ""];
            $config[] = ["graph{$g}_unite{$i}", ""];
            $config[] = ["graph{$g}_coef{$i}", ""];
        }
    }
    return $config;
}


// Permet de modifier l'affichage du widget (également utilisable par les commandes)
public function toHtml($_version = 'dashboard', $eqLogic = null) {

    if (!is_object($eqLogic)) {
        $eqLogic = $this;
    }
    $replace = $eqLogic->preToHtml($_version);
    if (!is_array($replace)) {
        return $replace;
    }

    $version = jeedom::versionAlias($_version);
    $nbGraphs = max(1, min(4, $eqLogic->getConfiguration('nbGraphs', 1)));
    $replace['#nbGraphs#'] = $nbGraphs;

    $graphLayout = $eqLogic->getConfiguration('graphLayout', 'auto');
    $replace['#graphLayout#'] = $graphLayout;

    $periodeHisto = $eqLogic->getConfiguration('periode_histo', 'nbJours');
    $delaiGraph = $eqLogic->getConfiguration("delai_histo");
    $dateDebutGraph1date = $eqLogic->getConfiguration("date_debut_histo");
    $dateDebutGraph2Dates = $eqLogic->getConfiguration("date_debut_histo_2dates");
    $dateFinGraph2Dates = $eqLogic->getConfiguration("date_fin_histo_2dates");


    $graphContainers = '';
    $chartScripts = '';
    

    for ($g = 1; $g <= 4; $g++) {
        if ($g > $nbGraphs) continue;
        // Type du graphique
        $graphType = $eqLogic->getConfiguration("graph{$g}_type", 'line');
        if ($graphType == 'inherit_graph') $graphType = 'line';
        $periodeHistoGraph = $eqLogic->getConfiguration("periode_histo_graph{$g}", 'global');
        $stackingOption = $eqLogic->getConfiguration("stacking_graph{$g}", 'null');
        $stackingOption = ($stackingOption == 'null') ? null : $stackingOption;
        $showLegend = $eqLogic->getConfiguration("graph{$g}_showLegend", 1) ? 'true' : 'false';
        $showTitle = $eqLogic->getConfiguration("graph{$g}_showTitle", 1);
        $titleGraph = $showTitle ? $eqLogic->getConfiguration("titleGraph{$g}", "") : '';

        $configNavigatorEnabled = $eqLogic->getConfiguration("graph{$g}_navigator", 0) ? 'true' : 'false';
        $configBarreEnabled = $eqLogic->getConfiguration("graph{$g}_barre", 0) ? 'true' : 'false';
        $configButtonsEnabled = $eqLogic->getConfiguration("graph{$g}_buttons", 0) ? 'true' : 'false';

        log::add(__CLASS__, 'debug', "Graph {$g}: configbarre {$configBarreEnabled} confignavigator {$configNavigatorEnabled} configbuttons {$configButtonsEnabled}");
        

        // === CALCUL DU FOND DE LA ZONE DE TRACÉ (plot area only) ===
        $bgTransparent = $eqLogic->getConfiguration("graph{$g}_bg_transparent", 1);

        $plotBgCode = "null"; // par défaut = pas de fond (transparent)

        if (!$bgTransparent) {
            $useGradient = $eqLogic->getConfiguration("graph{$g}_bg_gradient_enabled", 0);

            if ($useGradient) {
                $start = $eqLogic->getConfiguration("graph{$g}_bg_gradient_start", '#001f3f');
                $end   = $eqLogic->getConfiguration("graph{$g}_bg_gradient_end",   '#007bff');
                $angle = (int)$eqLogic->getConfiguration("graph{$g}_bg_gradient_angle", 90);

                // Conversion angle CSS → direction Highcharts (0 = haut, 90 = droite, etc.)
                $angles = [
                    0   => ['x1' => 0, 'y1' => 1, 'x2' => 0, 'y2' => 0],  // bas → haut
                    45  => ['x1' => 0, 'y1' => 1, 'x2' => 1, 'y2' => 0],
                    90  => ['x1' => 0, 'y1' => 0, 'x2' => 1, 'y2' => 0],  // gauche → droite
                    135 => ['x1' => 0, 'y1' => 0, 'x2' => 1, 'y2' => 1],
                    180 => ['x1' => 0, 'y1' => 0, 'x2' => 0, 'y2' => 1],  // haut → bas
                    225 => ['x1' => 1, 'y1' => 0, 'x2' => 0, 'y2' => 1],
                    270 => ['x1' => 1, 'y1' => 0, 'x2' => 0, 'y2' => 0],  // droite → gauche
                    315 => ['x1' => 1, 'y1' => 1, 'x2' => 0, 'y2' => 0],
                ];
                $dir = $angles[$angle] ?? $angles[90];

                $plotBgCode = "{ linearGradient: { x1: {$dir['x1']}, y1: {$dir['y1']}, x2: {$dir['x2']}, y2: {$dir['y2']} }, stops: [[0, '$start'], [1, '$end']] }";

            } else {
                // Couleur unie
                $color = $eqLogic->getConfiguration("graph{$g}_bg_color", '#ffffff');
                $plotBgCode = "'$color'";
            }
        }

        $uid = $replace['#uid#'];
        $containerId = "graphContainer{$uid}_{$g}";
        $graphContainers .= "<div id=\"{$containerId}\" style=\"height: 100%; width: 98%; margin: 0 1% 0 1%;\"></div>";

        $periodeHistoGraph = $eqLogic->getConfiguration("periode_histo_graph{$g}", 'global');
        $global = false;
        if ($periodeHistoGraph === 'global') {
            $periodeHistoGraph = $periodeHisto;
            $global = true;
        }
        $actualisation = false;
        $endTime = null;
        switch ($periodeHistoGraph) {
            case 'deDateAdate':
                $dateDebutGraph = $eqLogic->getConfiguration("date_debut_histo_2dates_graph{$g}", date("Y-m-d H:i:s", time()));
                $dateFinGraph = $eqLogic->getConfiguration("date_fin_histo_2dates_graph{$g}", date("Y-m-d H:i:s", time()));
                $startTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateDebutGraph2Dates)) : date("Y-m-d H:i:s", strtotime($dateDebutGraph));
                $endTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateFinGraph2Dates)) : date("Y-m-d H:i:s", strtotime($dateFinGraph));
                $actualisation = false;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using interval for start time calculation. Start time: {$startTime} End time: {$endTime}");
                break;
            case 'deDate':
                $dateDebutGraph = $eqLogic->getConfiguration("date_debut_histo_graph{$g}", date("Y-m-d H:i:s", time() - 24 * 60 * 60));
                $startTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateDebutGraph1date)) : date("Y-m-d H:i:s", strtotime($dateDebutGraph));
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using date for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'nbJours':
                $delai = ($global) ? $delaiGraph : intval($eqLogic->getConfiguration("delai_histo_graph{$g}"));
                $startTime = date("Y-m-d H:i:s", time() - $delai * 24 * 60 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using delay of {$delai} days for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'dDay':
                $startTime = date("Y-m-d 00:00:00", time());
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using today for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'dWeek':
                $startTime = date("Y-m-d 00:00:00", strtotime('monday this week'));
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using this week for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'dMonth':
                $startTime = date("Y-m-01 00:00:00", time());
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using this month for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'dYear':
                $startTime = date("Y-01-01 00:00:00", time());
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using this year for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'dAll':
                $startTime = date("1970-01-01 00:00:00");
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using all data for start time calculation. Start time: {$startTime} End time: now");
                break;
            default:
        }

        $split = $eqLogic->getConfiguration("tooltip{$g}", 'regroup');
        $splitJS = ($split == 'regroup') ? 'false' : 'true';


        $dataGroupingJS = 'enabled: false,';
        $regroup = $eqLogic->getConfiguration("graph{$g}_regroup", 'aucun');
        $typeRegroup = $eqLogic->getConfiguration("graph{$g}_typeRegroup", 'aucun');
        $dataGroupingDateTimeLabelFormatsJS = "
                                        millisecond: ['%A %e %b %Y, %H:%M:%S.%L', '%A %e %b %Y de %H:%M:%S.%L', ' à %H:%M:%S.%L'],
                                        second: ['%A %e %b %Y, %H:%M:%S', '%A %e %b %Y de %H:%M:%S', ' à %H:%M:%S'],
                                        minute: ['%A %e %b %Y, %H:%M', '%A %e %b %Y de %H:%M', ' à %H:%M'],
                                        hour: ['%A %e %b %Y, %H:%M', '%A %e %b %Y de %H:%M', ' à %H:%M'],
                                        day: ['%A %e %b %Y', 'Du %A %b %e', ' au %A %b %e %Y'],
                                        week: ['Semaine du %e %b %Y', 'Du %e %b %Y', ' au %e %b %Y'],
                                        month: ['%B %Y', 'De %B', ' à %B %Y'],
                                        year: ['%Y', 'De %Y', ' à %Y']
            ";

        if ($regroup !== 'aucun' && $typeRegroup !== 'aucun') {
            $units = '';
            switch ($regroup) {
                case 'minute':
                    $units = "[[ 'minute', [1, 5, 10, 15, 30] ]]";
                    break;
                case 'hour':
                    $units = "[[ 'hour', [1, 2, 4, 6, 12] ], [ 'day', [1] ]]";
                    break;
                case 'day':
                    $units = "[[ 'day', [1] ], [ 'week', [1] ]]";
                    break;
                case 'week':
                    $units = "[[ 'week', [1] ], [ 'month', [1] ]]";
                    break;
                case 'month':
                    $units = "[[ 'month', [1, 3, 6] ], [ 'year', null ]]";
                    break;
                case 'year':
                    $units = "[[ 'year', [1] ]]";
                    break;
                default:
                    $units = "[[ 'minute', [1,5,15,30] ], [ 'hour', [1,6] ], [ 'day', [1] ], [ 'week', [1] ], [ 'month', [1,3,6] ], [ 'year', null ]]";
            }

            $approximation = $typeRegroup; // 'average', 'sum', 'min', 'max', 'average' → 'average'

            $dataGroupingJS = "
                enabled: true,
                forced: true,
                approximation: '{$approximation}',
                units: {$units},
            ";
        }        
        
        $seriesJS = '';
        $cmdUpdateJS = '';
        $compareType = $eqLogic->getConfiguration("graph{$g}_compare_type", 'none');
        $compareMonth = $eqLogic->getConfiguration("graph{$g}_compare_month", date('m'));
        $rollingStartMonth = $eqLogic->getConfiguration("graph{$g}_rolling_start_month", '01');
        $recordData = [];

        $first = false;

        // Collecter les unités en premier pour définir les axes Y
        $units = [];
        for ($i = 1; $i <= 10; $i++) {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $cmdKey = "graph{$g}_cmdGraphe{$index}";
            $nomKey = "graph{$g}_index{$index}_nom";
            $cmdGraphe = $eqLogic->getConfiguration($cmdKey);
            $indexNom = $eqLogic->getConfiguration($nomKey);

            if (empty($indexNom) || empty($cmdGraphe)) {
                continue;
            }

            if (!$first){
                $first = true;
            } else {
                if ($compareType == 'prev_year' || $compareType == 'prev_year_month'){
                    continue;
                }
            }

            $cmd = cmd::byId(str_replace('#', '', $cmdGraphe));
            if (is_object($cmd)) {
                $manualUnit = trim($eqLogic->getConfiguration("graph{$g}_unite{$i}", ''));
                if ($manualUnit !== '') {
                    $unite = $manualUnit;
                } else {
                    $unite = ($cmd && $cmd->getUnite()) ? $cmd->getUnite() : '';
                }
                $units[] = $unite;
            }
        }

        $uniqueUnits = array_values(array_unique(array_filter($units)));
        $unitToAxis = [];
        foreach ($uniqueUnits as $idx => $u) {
            $index = $idx;
            //if ($idx == 2) $index = $idx-1;
            $unitToAxis[$u] = $index;
        }
        $nbAxes   = count($uniqueUnits);


        // construire les axes Y
        $showYAxis = $eqLogic->getConfiguration("graph{$g}_show_yAxis", 1);
        $yAxisJS = 'yAxis: [';
        foreach ($uniqueUnits as $idx => $u) {
            $offset = $idx * 40;
            $visible = $showYAxis ? 'true' : 'false';
            $yAxisJS .= "{
                visible: {$visible},
                opposite: true,
                offset: {$offset},
                labels: {
                    format: '{value} " . ($u !== '' ? " " . addslashes($u) : '') . "',
                    align: 'left',
                    x: 8,
                    y: 4,
                    style: { fontSize: '11px' },
                    " . ($nbAxes > 1 ? "useHTML: true,
                    formatter: function () {
                        return '<div style=\"transform: rotate(-45deg); transform-origin: left center; margin-top: 15px; white-space: nowrap;\">' + this.value + ' {$u}</div>';
                    }" : "") . "
                },
                crosshair: {
                    width: " . ($showYAxis ? '1' : '0') . ",
                    dashStyle: 'Dash',
                    zIndex: 5
                }                
            },";
        }
        $yAxisJS .= '],';

        $first = false; // Reset for second loop

        for ($i = 1; $i <= 10; $i++) {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $cmdKey = "graph{$g}_cmdGraphe{$index}";
            $nomKey = "graph{$g}_index{$index}_nom";
            $colorKey = "graph{$g}_color{$i}";
            $curveTypeKey = "graph{$g}_curve{$i}_type";
            $cmdGraphe = $eqLogic->getConfiguration($cmdKey);
            $indexNom = $eqLogic->getConfiguration($nomKey);
            $color = $eqLogic->getConfiguration($colorKey, $defaultColors[$i-1] ?? '#000000');
            $curveTypeOverride = $eqLogic->getConfiguration($curveTypeKey, 'inherit_curve');


            if (empty($indexNom) || empty($cmdGraphe)) {
                continue;
            }
            
            if (!$first){
                    $first = true;
            } else {
                if ($compareType == 'prev_year' || $compareType == 'prev_year_month'){
                    continue;
                }
            }
            
            log::add(__CLASS__, 'debug', "Graph {$g} Curve {$i}: Processing with command {$cmdGraphe}, name {$indexNom}, compare={$compareType} and first={$first}");


            $cmd = cmd::byId(str_replace('#', '', $cmdGraphe));
            $listeHisto = ''; 
            $cmdId = '';
            if (is_object($cmd)) {
                $finalCurveType = $curveTypeOverride;
                if ($finalCurveType === 'inherit_curve') {
                    $finalCurveType = $graphType;
                }
                $manualUnit = trim($eqLogic->getConfiguration("graph{$g}_unite{$i}", ''));
                if ($manualUnit !== '') {
                    $unite = $manualUnit;
                } else {
                    $unite = ($cmd && $cmd->getUnite()) ? $cmd->getUnite() : '';
                }
                $coef = floatval($eqLogic->getConfiguration("graph{$g}_coef{$i}", '1'));
                $histo = $cmd->getHistory($startTime, isset($endTime) ? $endTime : null);

                $listeHisto = [];
                $recordYear = null;
                $currentYear = (int)date('Y');
                $monthToStart = (int)$rollingStartMonth;
                $rolling = false;
                
                //$recordData = [];
                foreach ($histo as $record) {
                    if ($compareType == 'none'){
                        $ts = strtotime($record->getDatetime()) * 1000;
                        $listeHisto[] = [$ts, $record->getValue() * $coef];
                    } elseif ($compareType == 'prev_year') {
                        $recordDate = new DateTime($record->getDatetime());
                        $recordYear = (int)$recordDate->format('Y');
                        $recordMonth = (int)$recordDate->format('m');
                        if ($recordMonth < $monthToStart) {
                            $recordYear = $recordYear - 1;
                            $rolling = true;
                        }
                        $yearsDiff = $currentYear - $recordYear;
                        $adjustedDate = $recordDate->modify("+{$yearsDiff} years");
                        $ts = $adjustedDate->getTimestamp() * 1000;
                        $recordData[$recordYear][] = [$ts, $record->getValue() * $coef];
                    } elseif ($compareType == 'prev_year_month') {
                        $recordDate = new DateTime($record->getDatetime());
                        $recordYear = (int)$recordDate->format('Y');
                        $recordMonth = (int)$recordDate->format('m');
                        if ($recordMonth == $compareMonth) {
                            $yearsDiff = $currentYear - $recordYear;
                            $adjustedDate = $recordDate->modify("+{$yearsDiff} years");
                            $ts = $adjustedDate->getTimestamp() * 1000;
                            $recordData[$recordYear][] = [$ts, $record->getValue() * $coef];
                        }
                    }
                }
                $cmdId = str_replace('#', '', $cmdGraphe);
            }
            
            $xAxisJS = "type: 'datetime',";

            $headerFormatJS = '<span>{point.key}</span><br>';
            $dateTimeLabelFormats = "   millisecond: '%H:%M:%S.%L',
                                        second: '%H:%M:%S',
                                        minute: '%H:%M',
                                        hour: '%H:%M',
                                        day: '%e. %b',
                                        week: '%e. %b',
                                        month: '%b \'%y',
                                        year: '%Y'
                                            ";
            if ($regroup !== 'aucun' && $typeRegroup !== 'aucun') {
                switch ($regroup) {
                    case 'minute':
                        // $headerFormatJS = '<span style="font-size: 10px;">%A %d %B %Y<br/>%H:%M</span><br/>';
                        $buttonJS = "buttons: [
                                            { type: 'minute', count: 30, text: '30m' },
                                            { type: 'hour', count: 1, text: '1h' },
                                            { type: 'day', count: 1, text: '1j' },
                                            { type: 'day', count: 7, text: '1s' },
                                            { type: 'day', count: 30, text: '1m' },
                                            { type: 'day', count: 365, text: '1an' },
                                            { type: 'all', text: 'Tout' }
                                        ]";
                        break;
                    case 'hour':
                        $buttonJS = "buttons: [
                                            { type: 'day', count: 1, text: '1j' },
                                            { type: 'day', count: 7, text: '1s' },
                                            { type: 'day', count: 30, text: '1m' },
                                            { type: 'day', count: 365, text: '1an' },
                                            { type: 'all', text: 'Tout' }
                                        ]";
                        break;
                    case 'day':
                        $buttonJS = "buttons: [
                                            { type: 'day', count: 7, text: '1s' },
                                            { type: 'day', count: 30, text: '1m' },
                                            { type: 'day', count: 365, text: '1an' },
                                            { type: 'all', text: 'Tout' }
                                        ]";
                        break;
                    case 'week':
                        $buttonJS = "buttons: [
                                            { type: 'day', count: 30, text: '1m' },
                                            { type: 'day', count: 365, text: '1an' },
                                            { type: 'all', text: 'Tout' }
                                        ]";
                        break;
                    case 'month':
                        $buttonJS = "buttons: [
                                            { type: 'day', count: 365, text: '1an' },
                                            { type: 'all', text: 'Tout' }
                                        ]";
                        break;
                    case 'year':
                        $buttonJS = "buttons: [
                                            { type: 'all', text: 'Tout' }
                                        ]";
                        break;
                    default:
                }
            } else {
                $buttonJS = "buttons: [
                            { type: 'minute', count: 30, text: '30m' },
                            { type: 'hour', count: 1, text: '1h' },
                            { type: 'day', count: 1, text: '1j' },
                            { type: 'day', count: 7, text: '1s' },
                            { type: 'day', count: 30, text: '1m' },
                            { type: 'day', count: 365, text: '1an' },
                            { type: 'all', text: 'Tout' }
                        ]";
            }

            $axisIndex = $unitToAxis[$unite] ?? 0;
            log::add(__CLASS__, 'debug', "Graph {$g} Curve {$i}: Unit '{$unite}' assigned to axis index {$axisIndex}");

            if ($compareType == 'prev_year' && isset($recordData) && is_array($recordData)) {
                $nbSeries = count($recordData);
                if ($nbSeries > 2) {
                    $baseSeries = 1;
                    $navigatorEnabled = $configNavigatorEnabled;
                } else {
                    $navigatorEnabled = 'false';
                    $baseSeries = 0;
                }
                foreach ($recordData as $year => $data) {
                    if ($rolling){
                        $years = $year . '.' . (substr($year,2,2) + 1);
                    } else {
                        $years = $year;
                    }
                    $seriesJS .= "{
                        name: " . json_encode($indexNom . " - {$years}") . ",
                        type: " . json_encode($finalCurveType) . ",
                        data: ". json_encode($data) . ",
                        valueSuffix: " . json_encode(' ' .$unite) . ",
                        tooltip: {
                            valueSuffix: " . json_encode(' ' .$unite) . "
                        },
                        yAxis: {$axisIndex}
                    },\n";
                }
                $xAxisJS .=  "
                            labels: {
                                formatter: function() {
                                    return Highcharts.dateFormat('%d %b', this.value);
                                }
                            },
                        ";

                $xDateFormatJS = "%d %B - %Hh%M";
                $dataGroupingDateTimeLabelFormatsJS = "
                                millisecond: [
                                    '%A %e %b, %H:%M:%S.%L', '%A %e %b de %H:%M:%S.%L', ' à %H:%M:%S.%L'
                                ],
                                second: ['%A %e %b, %H:%M:%S', '%A %e %b de %H:%M:%S', ' à %H:%M:%S'],
                                minute: ['%A %e %b, %H:%M', '%A %e %b de %H:%M', ' à %H:%M'],
                                hour: ['%A %e %b, %H:%M', '%A %e %b de %H:%M', ' à %H:%M'],
                                day: ['%A %e %b', 'Du %A %b %e', ' au %A %b %e'],
                                week: ['Semaine du %e %b', 'Du %e %b', ' au %e %b'],
                                month: ['%B', 'De %B', ' à %B'],
                                year: ['%Y', 'De %Y', ' à %Y']
                            ";
                $navigatorJS =    "{ 
                    enabled: $navigatorEnabled,
                    baseSeries: $baseSeries,
                    margin: 1
                    }";
            }

            if ($compareType == 'prev_year_month' && isset($recordData) && is_array($recordData)) {
                foreach ($recordData as $year => $data) {
                    $seriesJS .= "{
                        name: " . json_encode($indexNom . " - {$year}") . ",
                        type: " . json_encode($finalCurveType) . ",
                        data: ". json_encode($data) . ",
                        tooltip: {
                            valueSuffix: " . json_encode(' ' .$unite) . "
                        },
                        yAxis: {$axisIndex}
                    },\n";
                }
                $xDateFormatJS = "%d %B - %Hh%M";
                $buttonJS = "buttons: [
                                        { type: 'day', count: 7, text: '1s' },
                                        { type: 'all', text: 'Tout' }
                                    ]";
                $navigatorJS =    "{ 
                    enabled: $configNavigatorEnabled,
                    margin: 1
                    }";
            }

            if ($compareType == 'none'){
                $seriesJS .= "{
                    name: " . json_encode($indexNom . ($unite !== '' ? ' (' . $unite . ')' : '')) . ",
                    color: " . json_encode($color) . ",
                    type: " . json_encode($finalCurveType) . ",
                    data: ". json_encode($listeHisto) . ",
                    tooltip: {
                        pointFormat: '<span style=\"color:{series.color};font-weight:bold\"> ● </span>{$indexNom} : <b>{point.y} " . $unite . "</b><br/>',
                    },
                    dateTimeLabelFormats: {
                        millisecond: [
                            '%A, %e %b, %H:%M:%S.%L', '%A, %e %b, %H:%M:%S.%L', '-%H:%M:%S.%L'
                        ],
                        second: ['%A, %e %b, %H:%M:%S', '%A, %e %b, %H:%M:%S', '-%H:%M:%S'],
                        minute: ['%A %e %b, %H:%M', '%A %e %b de %H:%M', ' à %H:%M'],
                        hour: ['%A %e %b, %H:%M', '%A %e %b de %H:%M', ' à %H:%M'],
                        day: ['%A %e %b %Y', 'Du %A %b %e', ' au %A %b %e %Y'],
                        week: ['Semaine du %e %b', 'Du %e %b', ' au %e %b'],
                        month: ['%B %Y', 'De %B', ' à %B %Y'],
                        year: ['%Y', 'De %Y', ' à %Y']
                    },
                    yAxis: {$axisIndex}
                },\n";

                $xDateFormatJS = "%d %B %Y - %Hh%M";
                $dateTimeLabelFormats = "
                        millisecond: [
                            '%A, %e %b, %H:%M:%S.%L', '%A, %e %b, %H:%M:%S.%L', '-%H:%M:%S.%L'
                        ],
                        second: ['%A, %e %b, %H:%M:%S', '%A, %e %b, %H:%M:%S', '-%H:%M:%S'],
                        minute: ['%A %e %b, %H:%M', '%A %e %b de %H:%M', ' à %H:%M'],
                        hour: ['%A %e %b, %H:%M', '%A %e %b de %H:%M', ' à %H:%M'],
                        day: ['%A %e %b %Y', 'Du %A %b %e', ' au %A %b %e %Y'],
                        week: ['Semaine du %e %b', 'Du %e %b', ' au %e %b'],
                        month: ['%B %Y', 'De %B', ' à %B %Y'],
                        year: ['%Y', 'De %Y', ' à %Y']
                                            ";
                $navigatorJS =    "{ 
                    enabled: $configNavigatorEnabled,
                    margin: 1
                    }";
            }

            if ($cmdId and $actualisation) {
                $cmdUpdateJS .= "
                if ('{$cmdId}' !== '') {
                    jeedom.cmd.addUpdateFunction('{$cmdId}', function(_options) {
                        const dateLocaleMs = Math.floor(new Date().getTime()/1000) * 1000; 
                        const y = parseFloat(_options.display_value);
                        
                        if (window.chart_g{$g} && window.chart_g{$g}.series[{$i}-1]) {
                            window.chart_g{$g}.series[{$i}-1].addPoint([dateLocaleMs, y], true, false, true); 
                        }
                    });
                }\n";
            }

        }


        $rangeSelectorJS = "{
            enabled: {$configButtonsEnabled},
            selected: 6,
            inputEnabled: false,
            floating: true,
            allButtonsEnabled: true,
            {$buttonJS},
            inputPosition: {
                x: 0,
                y: 0
            },
            buttonPosition: {
                x: 0,
                y: -15
            }
        }";

        $chartScripts .= "
        window.chart_g{$g} = Highcharts.stockChart('{$containerId}', {
            chart: {
                type: '<?php echo $graphType; ?>',
                backgroundColor: 'transparent',
                plotBackgroundColor: {$plotBgCode},
                spacing: [10, 0, 10, 0]
            },
            exporting: {
                enabled: true,
            },
            title: { 
                floating: false,
                margin: 0,
                y: 5,
                text: '{$titleGraph}', 
                height: 10,
                style: { 
                    fontWeight: 'bold', 
                    color: 'rgb(100, 100, 100)' 
                }
            },
            xAxis: { {$xAxisJS} },
            {$yAxisJS}
            credits: { enabled: false },
            legend: { 
                enabled: {$showLegend},
            },
            rangeSelector: {$rangeSelectorJS},
            navigator: {$navigatorJS},
            scrollbar: {
                margin: 10,
                enabled: true,
                enabled: {$configBarreEnabled}
            },
            tooltip: {
                xDateFormat: '{$xDateFormatJS}',
                dateTimeLabelFormats: { {$dateTimeLabelFormats} },
                backgroundColor: 'rgb(var(--bg-color))',
                useHTML: true,
                useHTML: true,
                shadow: true,
                style: {
                    color: 'rgb(var(--contrast-color))',
                },
                headerFormat: '{$headerFormatJS}',
                split: $splitJS,
                shared: 'true',
                valueDecimals: 2,
            },
            plotOptions: {
                series: {
                    stacking: '$stackingOption',
                    groupPadding:0.1,
                    pointPadding:0,
                    fillOpacity: 0.1,
                    dataGrouping: { {$dataGroupingJS}
                                    dateTimeLabelFormats: { {$dataGroupingDateTimeLabelFormatsJS} }
                                  },
                }
            },
            series: [{$seriesJS}]
        });
        setTimeout(() => window.chart_g{$g} && window.chart_g{$g}.reflow(), 50);
        {$cmdUpdateJS}
        ";
    }


    $replace['#graph_containers#'] = $graphContainers;
    $replace['#chart_scripts#'] = $chartScripts;
    
    log::add(__CLASS__, 'debug', "replace= " . json_encode($replace));

    $html = template_replace($replace, getTemplate('core', $version, 'jeeHistoGraph', __CLASS__));
    return $eqLogic->postToHtml($_version, $html);
}

  
    // Rafraîchissement du graphique sur le dashboard
    public static function rfresh($eqLogic) {
        jeeHistoGraph::toHtml( 'dashboard' , $eqLogic);
    }

}



class jeeHistoGraphCmd extends cmd {
  /* * *************************Attributs****************************** */
  /*
  public static $_widgetPossibility = array();
  */
  /* * ***********************Methode static*************************** */
  /* * *********************Methode d'instance************************* */
  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */
  // Exécution d'une commande
  public function execute($_options = array()) {
    $eqLogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
    switch ($this->getLogicalId()) { //vérifie le logicalid de la commande      
      case 'refresh': // LogicalId de la commande rafraîchir que l’on a créé dans la méthode Postsave
        jeeHistoGraph::rfresh($eqLogic);
      break;
      default:
        log::add('jeeHistoGraph', 'debug', __('Erreur durant execute', __FILE__));
        break;
    }
  }

  /* * **********************Getteur Setteur*************************** */
}
?>