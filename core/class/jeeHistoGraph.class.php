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
          $this->setconfiguration('color1',$color[0])
               ->setconfiguration('color2',$color[1])
               ->setconfiguration('color3',$color[2])
               ->setconfiguration('color4',$color[3])
               ->setconfiguration('color5',$color[4])
               ->setconfiguration('color6',$color[5])
               ->setconfiguration('color7',$color[6])
               ->setconfiguration('color8',$color[7])
               ->setconfiguration('color9',$color[8])
               ->setconfiguration('color10',$color[9]);
          $this->save();

        $this->setConfiguration('globalGraphType', 'line');
        for ($g = 1; $g <= 4; $g++) {
            $this->setConfiguration("graph{$g}_type", 'inherit_graph');
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
    $globalGraphType = $this->getConfiguration('graphType', 'line');

    $graphContainers = '';
    $chartScripts = '';
    $defaultColors = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4','#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];

    for ($g = 1; $g <= 4; $g++) {
        if ($g > $nbGraphs) continue;
        // Type du graphique
        $graphTypeOverride = $this->getConfiguration("graph{$g}_type", 'inherit_graph');
        $graphType = ($graphTypeOverride === 'inherit_graph') ? $globalGraphType : $graphTypeOverride;

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

        $bgTransparent = $this->getConfiguration("graph{$g}_bg_transparent", 1);
        $bgColor = '#ffffff'; // blanc par défaut
        if (!$bgTransparent) {
            $bgColor = $this->getConfiguration("graph{$g}_bg_color", '#ffffff');
        }
        $chartBgColor = $bgTransparent ? 'transparent' : $bgColor;
        log::add('jeeHistoGraph', 'debug', "Graph {$g} background color: {$chartBgColor}");

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
                $unite = $cmd->getUnite() ?: '';
                $unite = trim($unite) === '' ? '' : $unite . ' ';
                $histo = $cmd->getHistory($startTime);
                $lastValue = null; $n = 0;
                foreach ($histo as $row) {
                    $ts = strtotime($row->getDatetime());
                    log::add('jeeHistoGraph', 'debug', 'données : ' . json_encode($ts) . ' / ' . json_encode($row->getValue()));
                    if ($ts >= $minTime) {
                        $n++;
                        $value = $row->getValue();
                        $listeHisto .= "[Date.UTC(" . date("Y", $ts) . "," . (date("m", $ts)-1) . "," . date("d", $ts) . "," . date("H", $ts) . "," . date("i", $ts) . "," . date("s", $ts) . "),{$value}],\n";
                    } else {
                        $lastValue = $row->getValue();
                    }
                }
                if ($n == 0 && $lastValue !== null) {
                    $ts = $minTime;
                    $listeHisto .= "[Date.UTC(" . date("Y", $ts) . "," . (date("m", $ts)-1) . "," . date("d", $ts) . "," . date("H", $ts) . "," . date("i", $ts) . "," . date("s", $ts) . "),{$lastValue}],\n";
                }
                $ts = time();
                $value = $cmd->execCmd();
                $listeHisto .= "[Date.UTC(" . date("Y", $ts) . "," . (date("m", $ts)-1) . "," . date("d", $ts) . "," . date("H", $ts) . "," . date("i", $ts) . "," . date("s", $ts) . "),{$value}],\n";
                $cmdId = str_replace('#', '', $cmdGraphe);
            }
            $seriesJS .= "{
                name: " . json_encode($indexNom) . ",
                color: " . json_encode($color) . ",
                type: " . json_encode($finalCurveType) . ",
                marker: { enabled: false },
                data: [{$listeHisto}],
                unite: " . json_encode($unite) . "
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
        window.chart_g{$g} = Highcharts.chart('{$containerId}', {
            chart: {
                type: '{$graphType}',
                plotBackgroundColor: '{$chartBgColor}',
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
                labels: { format: '{value}' },
                title: { text: '' },
            },
            tooltip: { shared: true, useHTML: true, borderRadius: 10, pointFormat: '<tr><td style=\"color:{series.color}\">{series.name}: </td><td><b>{point.y:.1f}{series.options.unite}</b></td></tr>' },
            credits: { enabled: false },
            legend: { 
                enabled: {$showLegend},
            },
            rangeSelector: {$rangeSelector},
            navigator: {$navigator},
            series: [{$seriesJS}].filter(s => s.name && s.data.length > 0)
        });
        setTimeout(() => window.chart_g{$g} && window.chart_g{$g}.reflow(), 50);
        {$cmdUpdateJS}
        ";
    }

    $replace['#graph_containers#'] = $graphContainers;
    $replace['#chart_scripts#'] = $chartScripts;
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