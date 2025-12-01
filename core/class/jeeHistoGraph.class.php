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
                    ["date_fin_histo_2dates", ''],
                    ["showLegend", 1], 
                    ["maxPoints", 500]
                ];
    for ($g = 1; $g <= 4; $g++) {
        $config[] = ["graph{$g}_type", 'line'];
        $config[] = ["graph{$g}_regroup", "aucun"];
        $config[] = ["graph{$g}_typeRegroup", "aucun"];
        $config[] = ["stacking_graph{$g}", "aucun"];
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
public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
        return $replace;
    }

    $version = jeedom::versionAlias($_version);
    $nbGraphs = max(1, min(4, $this->getConfiguration('nbGraphs', 1)));
    $replace['#nbGraphs#'] = $nbGraphs;

    $graphLayout = $this->getConfiguration('graphLayout', 'auto');
    $replace['#graphLayout#'] = $graphLayout;

    $periodeHisto = $this->getConfiguration('periode_histo', 'nbJours');
    $delaiGraph = $this->getConfiguration("delai_histo");
    $dateDebutGraph1date = $this->getConfiguration("date_debut_histo");
    $dateDebutGraph2Dates = $this->getConfiguration("date_debut_histo_2dates");
    $dateFinGraph2Dates = $this->getConfiguration("date_fin_histo_2dates");


    $graphContainers = '';
    $chartScripts = '';
    

    for ($g = 1; $g <= 4; $g++) {
        if ($g > $nbGraphs) continue;
        // Type du graphique
        $graphType = $this->getConfiguration("graph{$g}_type", 'line');
        if ($graphType == 'inherit_graph') $graphType = 'line';
        $periodeHistoGraph = $this->getConfiguration("periode_histo_graph{$g}", 'global');
        

        // === CALCUL DU FOND DE LA ZONE DE TRACÉ (plot area only) ===
        $bgTransparent = $this->getConfiguration("graph{$g}_bg_transparent", 1);

        $plotBgCode = "null"; // par défaut = pas de fond (transparent)

        if (!$bgTransparent) {
            $useGradient = $this->getConfiguration("graph{$g}_bg_gradient_enabled", 0);

            if ($useGradient) {
                $start = $this->getConfiguration("graph{$g}_bg_gradient_start", '#001f3f');
                $end   = $this->getConfiguration("graph{$g}_bg_gradient_end",   '#007bff');
                $angle = (int)$this->getConfiguration("graph{$g}_bg_gradient_angle", 90);

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
                $color = $this->getConfiguration("graph{$g}_bg_color", '#ffffff');
                $plotBgCode = "'$color'";
            }
        }

        $uid = $replace['#uid#'];
        $containerId = "graphContainer{$uid}_{$g}";
        $titleGraph = $this->getConfiguration("titleGraph{$g}", "");
        $graphContainers .= "<div id=\"{$containerId}\" style=\"height: 100%; width: 98%; margin: 0 1% 0 1%;\"></div>";

        $periodeHistoGraph = $this->getConfiguration("periode_histo_graph{$g}", 'global');
        $global = false;
        if ($periodeHistoGraph === 'global') {
            $periodeHistoGraph = $periodeHisto;
            $global = true;
        }
        $actualisation = false;
        $endTime = null;
        switch ($periodeHistoGraph) {
            case 'deDateAdate':
                $dateDebutGraph = $this->getConfiguration("date_debut_histo_2dates_graph{$g}", date("Y-m-d H:i:s", time()));
                $dateFinGraph = $this->getConfiguration("date_fin_histo_2dates_graph{$g}", date("Y-m-d H:i:s", time()));
                $startTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateDebutGraph2Dates)) : date("Y-m-d H:i:s", strtotime($dateDebutGraph));
                $endTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateFinGraph2Dates)) : date("Y-m-d H:i:s", strtotime($dateFinGraph));
                $actualisation = false;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using interval for start time calculation. Start time: {$startTime} End time: {$endTime}");
                break;
            case 'deDate':
                $dateDebutGraph = $this->getConfiguration("date_debut_histo_graph{$g}", date("Y-m-d H:i:s", time() - 24 * 60 * 60));
                $startTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateDebutGraph1date)) : date("Y-m-d H:i:s", strtotime($dateDebutGraph));
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using date for start time calculation. Start time: {$startTime} End time: now");
                break;
            case 'nbJours':
            default:
                $delai = ($global) ? $delaiGraph : intval($this->getConfiguration("delai_histo_graph{$g}"));
                $startTime = date("Y-m-d H:i:s", time() - $delai * 24 * 60 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $actualisation = true;
                log::add(__CLASS__, 'debug', "Graph {$g}: Using delay of {$delai} days for start time calculation. Start time: {$startTime} End time: now");
                break;
        }

        $split = $this->getConfiguration("tooltip{$g}", 'regroup');
        $splitJS = 'false';
        if ($split != 'regroup') $splitJS = 'true';


        $dataGroupingJS = '';
        $regroup = $this->getConfiguration("graph{$g}_regroup", 'aucun');
        $typeRegroup = $this->getConfiguration("graph{$g}_typeRegroup", 'aucun');

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
            dataGrouping: {
                enabled: true,
                forced: true,
                approximation: '{$approximation}',
                units: {$units}
            }";
        }        
        
        $seriesJS = '';
        $cmdUpdateJS = '';
        $compareType = $this->getConfiguration("graph{$g}_compare_type", 'none');
        $compareMonth = $this->getConfiguration("graph{$g}_compare_month", date('m'));
        $rollingStartMonth = $this->getConfiguration("graph{$g}_rolling_start_month", '01');
        $recordData = [];

        $first = false;

        for ($i = 1; $i <= 10; $i++) {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $cmdKey = "graph{$g}_cmdGraphe{$index}";
            $nomKey = "graph{$g}_index{$index}_nom";
            $colorKey = "graph{$g}_color{$i}";
            $curveTypeKey = "graph{$g}_curve{$i}_type";
            $cmdGraphe = $this->getConfiguration($cmdKey);
            $indexNom = $this->getConfiguration($nomKey);
            $color = $this->getConfiguration($colorKey, $defaultColors[$i-1] ?? '#000000');
            $curveTypeOverride = $this->getConfiguration($curveTypeKey, 'inherit_curve');

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
                $manualUnit = trim($this->getConfiguration("graph{$g}_unite{$i}", ''));
                if ($manualUnit !== '') {
                    $unite = $manualUnit;
                    $coef = floatval($this->getConfiguration("graph{$g}_coef{$i}", '1'));
                } else {
                    $unite = ($cmd && $cmd->getUnite()) ? $cmd->getUnite() : '';
                    $coef = 1;
                }
                $unite = $unite !== '' ? $unite : '';
                $histo = $cmd->getHistory($startTime, isset($endTime) ? $endTime : null);
                $coef  = $manualUnit !== '' ? floatval($this->getConfiguration("graph{$g}_coef{$i}", 1)) : 1;

                $listeHisto = [];
                $recordYear = null;
                $currentYear = (int)date('Y');
                $monthToStart = (int)$rollingStartMonth;
                $rolling = false;
                log::add(__CLASS__, 'debug', 'ok');
                
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
                        // $headerFormatJS = '<span style="font-size: 10px;">%A %d %B %Y<br/></span><br/>';
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
                $headerFormatJS = '{point.key}';
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

            if ($compareType == 'prev_year' && isset($recordData) && is_array($recordData)) {
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
                        }
                    },\n";
                }
                $xAxisJS .=  "
                            labels: {
                                formatter: function() {
                                    return Highcharts.dateFormat('%d %b', this.value);
                                }
                            },
                        ";

                $xDateFormatJS = "%d/%m %Hh%M";
                $navigatorJS =    '{ 
                    enabled: false,
                    margin: 1
                    }';
            }

            if ($compareType == 'prev_year_month' && isset($recordData) && is_array($recordData)) {
                foreach ($recordData as $year => $data) {
                    $seriesJS .= "{
                        name: " . json_encode($indexNom . " - {$year}") . ",
                        type: " . json_encode($finalCurveType) . ",
                        data: ". json_encode($data) . ",
                        tooltip: {
                            valueSuffix: " . json_encode(' ' .$unite) . "
                        }
                    },\n";
                }
                $xDateFormatJS = "%d/%m %Hh%M";
                $buttonJS = "buttons: [
                                        { type: 'day', count: 7, text: '1s' },
                                        { type: 'all', text: 'Tout' }
                                    ]";
                $navigatorJS =    '{ 
                    enabled: true,
                    margin: 1
                    }';
            }

            if ($compareType == 'none'){
                $seriesJS .= "{
                    name: " . json_encode($indexNom . ($unite !== '' ? ' (' . $unite . ')' : '')) . ",
                    color: " . json_encode($color) . ",
                    type: " . json_encode($finalCurveType) . ",
                    data: ". json_encode($listeHisto) . ",
                },\n";

                $xDateFormatJS = "%d/%m/%Y %Hh%M";
                $navigatorJS =    '{ 
                    enabled: true,
                    margin: 1
                    }';
            }

            if ($cmdId and $actualisation) {
                $cmdUpdateJS .= "
                if ('{$cmdId}' !== '') {
                    jeedom.cmd.addUpdateFunction('{$cmdId}', function(_options) {
                        const dateUTC = Date.UTC(new Date().getFullYear(), new Date().getMonth(), new Date().getDate(),
                            new Date().getHours(), new Date().getMinutes(), new Date().getSeconds());
                        const y = parseFloat(_options.display_value);
                        if (window.chart_g{$g} && window.chart_g{$g}.series[{$i}-1]) {
                            window.chart_g{$g}.series[{$i}-1].addPoint([dateUTC, y], true, true, true);
                        }
                    });
                }\n";
            }

        }


        $showLegend = $this->getConfiguration('showLegend', 1) ? 'true' : 'false';
        $rangeSelectorJS = "{
            enabled: true,
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
                y: 20
            }
        }";

        $chartScripts .= "
        window.chart_g{$g} = Highcharts.StockChart('{$containerId}', {
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
                floating: true,
                y: 20,
                text: '{$titleGraph}', 
                height: 10,
                style: { 
                    fontWeight: 'bold', 
                    color: 'rgb(100, 100, 100)' 
                }
            },
            xAxis: { {$xAxisJS} },
            yAxis: {
                opposite: true,
                labels: { 
                        format: '{value}',
                        align: 'left',
                        distance: '50%',
                        x: 10,
                        y: -2,
                        },
                title: { text: '' },
            },
            credits: { enabled: false },
            legend: { 
                enabled: {$showLegend},
            },
            rangeSelector: {$rangeSelectorJS},
            navigator: {$navigatorJS},
            scrollbar: {
                margin: 10,
                enabled: true
            },
            tooltip: {
                headerFormat: '{$headerFormatJS}<br>',
                xDateFormat: '{$xDateFormatJS}',
                split: $splitJS,
                shared: true,
                valueDecimals: 2,
            },
            plotOptions: {
                series: {
                    fillOpacity: 0.1,
                    {$dataGroupingJS}
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
    
    log::add(__CLASS__, 'debug', "ok , replace= " . json_encode($replace));

    $html = template_replace($replace, getTemplate('core', $version, 'jeeHistoGraph', __CLASS__));
    return $this->postToHtml($_version, $html);
}
  /* * **********************Getteur Setteur*************************** */
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
  }
  /* * **********************Getteur Setteur*************************** */
}
?>