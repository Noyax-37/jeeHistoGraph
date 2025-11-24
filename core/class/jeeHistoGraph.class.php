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
          $color = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4',
                    '#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];
          $this ->setConfiguration('delai_histo',1)
                ->setConfiguration('nbGraphs',1)
                ->setConfiguration('periode_histo','nbJours')
                ->setConfiguration('globalGraphType', 'line')
                ->setConfiguration('graphLayout', 'auto');

        for ($g = 1; $g <= 4; $g++) {
            $this   ->setConfiguration("graph{$g}_type", 'inherit_graph')
                    ->setConfiguration("graph{$g}_regroup", "aucun")
                    ->setConfiguration("graph{$g}_typeRegroup", "aucun")
                    ->setConfiguration("periode_histo_graph{$g}", "global")
                    ->setconfiguration("graph{$g}_color1",$color[0])
                    ->setconfiguration("graph{$g}_color2",$color[1])
                    ->setconfiguration("graph{$g}_color3",$color[2])
                    ->setconfiguration("graph{$g}_color4",$color[3])
                    ->setconfiguration("graph{$g}_color5",$color[4])
                    ->setconfiguration("graph{$g}_color6",$color[5])
                    ->setconfiguration("graph{$g}_color7",$color[6])
                    ->setconfiguration("graph{$g}_color8",$color[7])
                    ->setconfiguration("graph{$g}_color9",$color[8])
                    ->setconfiguration("graph{$g}_color10",$color[9]);
            for ($i = 1; $i <= 10; $i++) {
                $this->setConfiguration("graph{$g}_curve{$i}_type", 'inherit_curve');
            }
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
    log::add('jeeHistoGraph', 'debug', "Rendering jeeHistoGraph (id={$this->getId()}) with layout '{$graphLayout}' and {$nbGraphs} graphs");

    $globalGraphType = $this->getConfiguration('graphType', 'line');

    $graphContainers = '';
    $chartScripts = '';
    $defaultColors = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4','#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];

    for ($g = 1; $g <= 4; $g++) {
        if ($g > $nbGraphs) continue;
        // Type du graphique
        $graphTypeOverride = $this->getConfiguration("graph{$g}_type", 'inherit_graph');
        $graphType = ($graphTypeOverride === 'inherit_graph') ? $globalGraphType : $graphTypeOverride;
        

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
        $graphContainers .= "<div id=\"{$containerId}\" style=\"height: 100%; width: 100%;\"></div>";

        $delaiGraph = $this->getConfiguration("delai_histo_graph{$g}");
        $delai = (!empty($delaiGraph) && is_numeric($delaiGraph) && $delaiGraph > 0) 
            ? intval($delaiGraph) 
            : $this->getConfiguration('delai_histo', 1);
        $replace['#delai_histo_graph' . $g . '#'] = $delai;
        $startTime = date("Y-m-d H:i:s", time() - $delai * 24 * 60 * 60);
        $minTime = time() - $delai * 24 * 60 * 60;


        $dataGrouping = '';
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

            $dataGrouping = "
            dataGrouping: {
                enabled: true,
                forced: true,
                approximation: '{$approximation}',
                units: {$units}
            }";
        }        
        
        $seriesJS = '';
        $cmdUpdateJS = '';

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

            if (empty($indexNom) || empty($cmdGraphe)) continue;

            $cmd = cmd::byId(str_replace('#', '', $cmdGraphe));
            $listeHisto = ''; $cmdId = '';
            if (is_object($cmd)) {
                $finalCurveType = $curveTypeOverride;
                if ($finalCurveType === 'inherit_curve') {
                    $finalCurveType = $graphType;
                }
                $manualUnit = trim($this->getConfiguration("graph{$g}_unite{$i}", ''));
                if ($manualUnit !== '') {
                    $unite = $manualUnit;
                    $coef = floatval($this->getConfiguration("graph{$g}_coef{$i}", '1'));
                    log::add('jeeHistoGraph', 'debug', "Graph {$g} Curve {$i} using manual unit '{$unite}' with coefficient {$coef}");
                } else {
                    $unite = ($cmd && $cmd->getUnite()) ? $cmd->getUnite() : '';
                    $coef = 1;
                    log::add('jeeHistoGraph', 'debug', "Graph {$g} Curve {$i} unit '{$unite}' without coefficient");
                }
                $unite = $unite !== '' ? $unite : '';
                $histo = $cmd->getHistory($startTime);
                $coef  = $manualUnit !== '' ? floatval($this->getConfiguration("graph{$g}_coef{$i}", 1)) : 1;

                $listeHisto = [];
                foreach ($histo as $record) {
                    $ts = strtotime($record->getDatetime()) * 1000;
                    if ($ts >= $minTime) {
                        $listeHisto[] = [$ts, $record->getValue() * $coef];
                    }
                }
                $cmdId = str_replace('#', '', $cmdGraphe);
            }
            $seriesJS .= "{
                name: " . json_encode($indexNom . ($unite !== '' ? ' (' . $unite . ')' : '')) . ",
                color: " . json_encode($color) . ",
                type: " . json_encode($finalCurveType) . ",
                data: ". json_encode($listeHisto) . ",
                tooltip: {
                        valueSuffix: " . json_encode(' ' .$unite) . "
                    }
            },\n";
            if ($cmdId) {
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
        $rangeSelector = "{
            enabled: true,
            selected: 6,
            inputEnabled: false,
            floating: true,
            allButtonsEnabled: true,
            buttons: [
                { type: 'minute', count: 30, text: '30m' },
                { type: 'hour', count: 1, text: '1h' },
                { type: 'day', count: 1, text: '1j' },
                { type: 'day', count: 7, text: '1s' },
                { type: 'day', count: 30, text: '1m' },
                { type: 'day', count: 365, text: '1y' },
                { type: 'all', text: 'Tout' }
            ],
            inputPosition: {
                x: 0,
                y: 0
            },
            buttonPosition: {
                x: 0,
                y: 0
            }
        }";

        $navigator =    '{ 
                        enabled: true,
                        margin: 1
                        }';

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
                text: '{$titleGraph}', 
                height: 10,
                style: { 
                    fontWeight: 'bold', 
                    color: 'rgb(100, 100, 100)' 
                }
            },
            xAxis: { type: 'datetime' },
            yAxis: {
                opposite: true,
                labels: { 
                        format: '{value}',
                        align: 'left',
                        x: 4,
                        y: 0
                        },
                title: { text: '' },
            },
            credits: { enabled: false },
            legend: { 
                enabled: {$showLegend},
            },
            rangeSelector: {$rangeSelector},
            navigator: {$navigator},
            series: [{$seriesJS}].filter(s => s.name && s.data.length > 0),
            plotOptions: {
                series: {
                    tooltip: {
                        xDateFormat: '%d/%m/%Y %Hh%M',
                        shared: true,
                        valueDecimals: 2,
                    },
                    {$dataGrouping}
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
    log::add('jeeHistoGraph', 'debug', "jeeHistoGraph (id={$this->getId()}) replace: " . json_encode($replace));
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