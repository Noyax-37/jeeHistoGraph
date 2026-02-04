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
    $actualVersion = config::byKey('version', 'jeeHistoGraph', '0.0', true);
    $this   ->setConfiguration('version', $actualVersion);
    $this->save();
    $refresh = $this->getCmd('action', 'refresh');
    if (!is_object($refresh)) {
        log::add("jeeHistoGraph", "debug", "création de refresh");
        $refresh = new jeeHistoGraphCmd();
        $refresh->setName(__('Rafraichir', __FILE__));
    }
    $refresh->setEqLogic_id($this->getId());
    $refresh->setLogicalId('refresh');
    $refresh->setType('action');
    $refresh->setSubType('other');
    $refresh->save();    
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
    log::add('jeeHistoGraph', 'debug', __('Exécution de la commande refresh pour l\'équipement ' . $this->getName(), __FILE__));
    $this->refreshWidget();
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
                    ["graph1_crosshair", 0],
                    ["graph2_crosshair", 0],
                    ["graph3_crosshair", 0],
                    ["graph4_crosshair", 0]
                ];
    for ($g = 1; $g <= 4; $g++) {
        $config[] = ["graph{$g}_type", 'line'];
        $config[] = ["periode_histo_graph{$g}", "global"];
        $config[] = ["delai_histo_graph{$g}", ''];
        $config[] = ["stacking_graph{$g}", "null"];
        $config[] = ["date_debut_histo_graph{$g}", ''];
        $config[] = ["date_debut_histo_2dates_graph{$g}", ""];
        $config[] = ["date_fin_histo_2dates_graph{$g}", ''];
        $config[] = ["graph{$g}_bg_transparent", 1];
        $config[] = ["graph{$g}_bg_couleur", ""];
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
        $config[] = ["graph{$g}_alternate_yAxis", 1];
        $config[] = ["graph{$g}_nbPointsTimeLine", 300];
        $config[] = ["graph{$g}_show_refPrec", 1];
        $config[] = ["graph{$g}_inverted", 1];
        $config[] = ["graph{$g}_tooltip_enabled", 1];
        $config[] = ["graph{$g}_dataLabels_overlaps", 0];
        $config[] = ["graph{$g}_3D_enabled", 0];
        $config[] = ["graph{$g}_3D_alpha", 15];
        $config[] = ["graph{$g}_3D_beta", 15];
        $config[] = ["graph{$g}_3D_depth", 25];
        $config[] = ["graph{$g}_3D_view_distance", 0];
        $config[] = ["graph{$g}_zoom_axe_x", 1];
        $config[] = ["graph{$g}_zoom_axe_y", 1];
        $config[] = ["graph{$g}_title_align", 'center'];
        $config[] = ["graph{$g}_default_color", 0];
        $config[] = ["graph{$g}_update_enabled", 1];
        $config[] = ["graph{$g}_update_append", 0];
        for ($i = 1; $i <= 10; $i++) {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $config[] = ["graph{$g}_curve{$i}_order", $i];
            $config[] = ["graph{$g}_index{$index}_nom", ''];
            $config[] = ["graph{$g}_curve{$i}_type", "inherit_curve"];
            $config[] = ["stacking_graph{$g}_curve{$i}", 0];
            $config[] = ["graph{$g}_curve{$i}_regroup", "aucun"];
            $config[] = ["graph{$g}_curve{$i}_typeRegroup", "aucun"];
            $config[] = ["graph{$g}_color{$i}", $defaultColors[$i-1]];
            $config[] = ["graph{$g}_cmdGraphe{$index}", ""];
            $config[] = ["graph{$g}_unite{$i}", ""];
            $config[] = ["graph{$g}_coef{$i}", ""];
            $config[] = ["graph{$g}_curve{$i}_stairStep", 0];
            $config[] = ["graph{$g}_curve{$i}_variation", 0];
            $config[] = ["display_graph{$g}_curve{$i}", 0];
            $config[] = ["graph{$g}_mini{$i}", ""];
            $config[] = ["graph{$g}_maxi{$i}", ""];
            $config[] = ["graph{$g}_plotlines{$i}", ""];
        }
    }
    return $config;
}


// Permet de modifier l'affichage du widget (également utilisable par les commandes)
public function toHtml($_version = 'dashboard', $eqLogic = null) {
    //log::add(__CLASS__, 'debug', "toHtml called for version: {$_version} and for eqlogic: " . ($eqLogic ? $eqLogic->getName() : 'self'));
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
    $nameEqpmnt = $eqLogic->getName();
    $message = '';

    $graphLayout = $eqLogic->getConfiguration('graphLayout', 'auto');
    $replace['#graphLayout#'] = $graphLayout;

    $periodeHisto = $eqLogic->getConfiguration('periode_histo', 'nbJours');
    $delaiGraph = $eqLogic->getConfiguration("delai_histo");
    $dateDebutGraph1date = $eqLogic->getConfiguration("date_debut_histo");
    $dateDebutGraph2Dates = $eqLogic->getConfiguration("date_debut_histo_2dates");
    $dateFinGraph2Dates = $eqLogic->getConfiguration("date_fin_histo_2dates");
    $crosshair[1] = $eqLogic->getConfiguration("graph1_crosshair", '0') == '1' ? 'true' : 'false';
    $crosshair[2] = $eqLogic->getConfiguration("graph2_crosshair", '0') == '1' ? 'true' : 'false';
    $crosshair[3] = $eqLogic->getConfiguration("graph3_crosshair", '0') == '1' ? 'true' : 'false';
    $crosshair[4] = $eqLogic->getConfiguration("graph4_crosshair", '0') == '1' ? 'true' : 'false';
    $cross = 0;
    for ($g = 1; $g <= $nbGraphs; $g++) {
        if ($crosshair[$g] == 'true') {
            $cross += 1;
        }
    }
    if ($cross < 2) {
        $crosshair[1] = 'false';
        $crosshair[2] = 'false';
        $crosshair[3] = 'false';
        $crosshair[4] = 'false';
    }


    $graphContainers = '';
    $chartScripts = '';

    $defaultHightchartColors = [ "#2caffe", "#544fc5", "#00e272", "#fe6a35", "#6b8abc", "#d568fb", "#2ee0ca", "#fa4b42", "#feb56a", "#91e8e1" ];
    
        
    for ($g = 1; $g <= $nbGraphs; $g++) {
        // Type du graphique
        $alignThresholdsJS = 'true';
        $graphType = $eqLogic->getConfiguration("graph{$g}_type", 'line');
        $periodeHistoGraph = $eqLogic->getConfiguration("periode_histo_graph{$g}", 'global');
        $stackingOption = $eqLogic->getConfiguration("stacking_graph{$g}", 'null');
        $stackingOption = ($stackingOption == 'null') ? null : $stackingOption;
        $showLegend = $eqLogic->getConfiguration("graph{$g}_showLegend", 1) ? 'true' : 'false';
        $showTitle = $eqLogic->getConfiguration("graph{$g}_showTitle", 1);
        $titleGraph = $showTitle ? $eqLogic->getConfiguration("titleGraph{$g}", "") : '';
        $titleAlign = $eqLogic->getConfiguration("graph{$g}_title_align", 'center');
        $updateEnabled = $eqLogic->getConfiguration("graph{$g}_update_enabled", 1) ? true : false;
        $updateAppend = $eqLogic->getConfiguration("graph{$g}_update_append", 0) ? 'false' : 'true';
        $xTitlePosition = $titleAlign == 'right' ? -30 : 0;
        $chartOrStock = 'StockChart';
        $inverted = 'false';
        $tooltipEnabled = 'true';

        // Position des boutons zoom et reset zoom
        $titleAlign = $showTitle ? $titleAlign : 'center'; // si pas de titre
        $buttonAxisAlign = $titleAlign == 'left' ? 'right' : 'left';
        $xRangeSelectorButtonPosition = $titleAlign == 'left' ? -40 : 0;
        $yRangeSelectorButtonPosition = $showTitle ? -30 : 0; // remonte les boutons sauf si titre non affiché
        $xZoomResetButtonPosition = $titleAlign == 'left' ? -70 : 70;
        $yZoomResetButtonPosition = $showTitle ? -35 : 0;
        
        $configNavigatorEnabled = $eqLogic->getConfiguration("graph{$g}_navigator", 0) ? 'true' : 'false';
        $configBarreEnabled = $eqLogic->getConfiguration("graph{$g}_barre", 0) ? 'true' : 'false';
        $configButtonsEnabled = $eqLogic->getConfiguration("graph{$g}_buttons", 0) ? 'true' : 'false';
        $config3DEnabled = $eqLogic->getConfiguration("graph{$g}_3D_enabled", 0) ? 'true' : 'false';
        if ($config3DEnabled == 'true') {
            // Si 3D activé, on désactive la barre de navigation
            $configNavigatorEnabled = 'false';
            $configBarreEnabled = 'false';
        }

        if ($configNavigatorEnabled == 'false' && $configBarreEnabled == 'false') { // si ni barre ni navigator, on force la barre invisible pour permettre le scroll
            $configBarreEnabled = 'true';
            $scrollbarConfig = "
                enabled: true,
                height: 0,          // hauteur nulle = invisible
                margin: -999,       // ou -50 selon la version
                barBackgroundColor: 'transparent',
                trackBackgroundColor: 'transparent',
                buttonBackgroundColor: 'transparent',
                buttonArrowColor: 'transparent'
            ";
        } else {
            $scrollbarConfig = "
                enabled: {$configBarreEnabled},
                margin: 10
            ";
        }

        //$configNavigatorEnabled = 'true';
        //$configBarreEnabled = 'true';
        $config3DAlpha = (int)$eqLogic->getConfiguration("graph{$g}_3D_alpha", 15);
        $config3DBeta = (int)$eqLogic->getConfiguration("graph{$g}_3D_beta", 15);
        $config3DDepth = (int)$eqLogic->getConfiguration("graph{$g}_3D_depth", 25);
        $config3DViewDistance = (int)$eqLogic->getConfiguration("graph{$g}_3D_view_distance", 0);
        $configZoomAxeX = $eqLogic->getConfiguration("graph{$g}_zoom_axe_x", 1) ? true : false;
        $configZoomAxeY = $eqLogic->getConfiguration("graph{$g}_zoom_axe_y", 1) ? true : false;
        if ($configZoomAxeX && $configZoomAxeY) {
            $zoomType = 'xy';
        } elseif ($configZoomAxeX) {
            $zoomType = 'x';
        } elseif ($configZoomAxeY) {
            $zoomType = 'y';
        } else {
            $zoomType = 'none';
        }

        $xAxisMinJS = 'undefined';
        $xAxisMaxJS = 'undefined';
        

        //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: configbarre {$configBarreEnabled} confignavigator {$configNavigatorEnabled} configbuttons {$configButtonsEnabled}");
        

        // === CALCUL DU FOND DE LA ZONE DE TRACÉ (plot area only) ===
        $bgTransparent = $eqLogic->getConfiguration("graph{$g}_bg_transparent", 1);

        $plotBgCode = "null"; // par défaut = pas de fond (transparent)
        $plotBgCode3d = "'#ffffff60'";
        $plotBgmini = "80"; // atténuation pour les côtés des graphs 3d

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
                $dir = $angles[$angle];
                
                $plotBgCode = "{ linearGradient: { x1: {$dir['x1']}, y1: {$dir['y1']}, x2: {$dir['x2']}, y2: {$dir['y2']} }, stops: [[0, '$start'], [1, '$end']] }";
                $plotBgCode3d = "{ linearGradient: { x1: {$dir['x1']}, y1: {$dir['y1']}, x2: {$dir['x2']}, y2: {$dir['y2']} }, stops: [[0, '" . $start .  $plotBgmini . "'], [1, '" . $end . $plotBgmini . "']] }";
            } else {
                // Couleur unie
                $bgColor = $eqLogic->getConfiguration("graph{$g}_bg_couleur", '#ffffff');
                $plotBgCode = "'$bgColor'";
                $plotBgCode3d = "'" . $bgColor . $plotBgmini . "'";
            }
        }

        $id = $replace['#id#'];
        $containerId = "graphContainer{$id}_{$g}";
        $graphContainers .= "<div id=\"{$containerId}\" style=\"height: 100%; width: 98%; margin: 0 1% 0 1%;\"></div>";
        $periodeHistoGraph = $eqLogic->getConfiguration("periode_histo_graph{$g}", 'global');
        $global = false;
        if ($periodeHistoGraph === 'global') {
            $periodeHistoGraph = $periodeHisto;
            $global = true;
        }
        $actualisation = $updateEnabled;
        $endTime = null;
        switch ($periodeHistoGraph) {
            case 'deDateAdate':
                $dateDebutGraph = $eqLogic->getConfiguration("date_debut_histo_2dates_graph{$g}", date("Y-m-d H:i:s", time()));
                $dateFinGraph = $eqLogic->getConfiguration("date_fin_histo_2dates_graph{$g}", date("Y-m-d H:i:s", time()));
                $startTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateDebutGraph2Dates)) : date("Y-m-d H:i:s", strtotime($dateDebutGraph));
                $endTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateFinGraph2Dates)) : date("Y-m-d H:i:s", strtotime($dateFinGraph));
                $actualisation = false;
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using interval for start time calculation. Start time: {$startTime} End time: {$endTime}");
                break;
            case 'deDate':
                $dateDebutGraph = $eqLogic->getConfiguration("date_debut_histo_graph{$g}", date("Y-m-d H:i:s", time() - 24 * 60 * 60));
                $startTime = ($global) ? date("Y-m-d H:i:s", strtotime($dateDebutGraph1date)) : date("Y-m-d H:i:s", strtotime($dateDebutGraph));
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using date for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'nbJours':
                $delai = ($global) ? $delaiGraph : intval($eqLogic->getConfiguration("delai_histo_graph{$g}"));
                $startTime = date("Y-m-d H:i:s", time() - $delai * 24 * 60 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using delay of {$delai} days for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dLast5Min':
                $startTime = date("Y-m-d H:i:s", time() - 5 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using last 5 minutes for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dLast15Min':
                $startTime = date("Y-m-d H:i:s", time() - 15 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using last 15 minutes for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dLast30Min':
                $startTime = date("Y-m-d H:i:s", time() - 30 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using last 30 minutes for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dLastHour':
                $startTime = date("Y-m-d H:i:s", time() - 60 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using last hour for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dLast6Hours':
                $startTime = date("Y-m-d H:i:s", time() - 6 * 60 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using last 6 hours for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dLast12Hours':
                $startTime = date("Y-m-d H:i:s", time() - 12 * 60 * 60);
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using last 12 hours for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dDay':
                $startTime = date("Y-m-d 00:00:00", time());
                $endTime = date("Y-m-d H:i:s", time());
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using today for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                break;
            case 'dWeek':
                $startTime = date("Y-m-d 00:00:00", strtotime('monday this week'));
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using this week for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dMonth':
                $startTime = date("Y-m-01 00:00:00", time());
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using this month for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dYear':
                $startTime = date("Y-01-01 00:00:00", time());
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = strtotime($startTime . ' UTC') * 1000;
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using this year for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            case 'dAll':
                $startTime = date("1970-01-01 00:00:00");
                $endTime = date("Y-m-d H:i:s", time());
                $xAxisMinJS = 'undefined';
                $xAxisMaxJS = strtotime($endTime . ' UTC') * 1000;
                log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Using all data for start time calculation. Start time: {$startTime} End time: now ({$endTime})");
                break;
            default:
        }

        $split = $eqLogic->getConfiguration("tooltip{$g}", 'regroup');
        $splitJS = 'false';
        $sharedJS = 'false';

        if ($graphType != 'timeLine'){
            if ($split == 'sans') {
                $tooltipEnabled = 'false';
            } elseif ($split == 'normal') {
                $splitJS = 'false';
                $sharedJS = 'false';
            } elseif ($split == 'regroup') {
                $splitJS = 'false';
                $sharedJS = 'true';
            } else {
                $splitJS = 'true';
                $sharedJS = 'true';
            }
        }

        $seriesJS = '';
        $cmdUpdateJS = '';
        $compareType = $eqLogic->getConfiguration("graph{$g}_compare_type", 'none');
        $compareMonth = $eqLogic->getConfiguration("graph{$g}_compare_month", date('m'));
        $rollingStartMonth = $eqLogic->getConfiguration("graph{$g}_rolling_start_month", '01');
        $recordData = [];

        $first = false;
        $defaultColors = $eqLogic->getConfiguration("graph{$g}_default_color", 0);
        //log::add(__CLASS__, 'debug', "Crosshair for Equipment: '{$nameEqpmnt}' Graph {$g}: {$crosshair}");
                
        // Collecter les unités en premier pour définir les axes Y
        $units = [];
        $mini = [];
        $maxi = [];
        $plot = [];
        $colorYAxis = [];
        for ($i = 1; $i <= 10; $i++) {
            $displayCurve = $eqLogic->getConfiguration("display_graph{$g}_curve{$i}", 0);
            if ($displayCurve != '0') {
                $index = str_pad($i, 2, '0', STR_PAD_LEFT);
                $cmdKey = "graph{$g}_cmdGraphe{$index}";
                $nomKey = "graph{$g}_index{$index}_nom";
                $cmdGraphe = $eqLogic->getConfiguration($cmdKey);
                $indexNom = $eqLogic->getConfiguration($nomKey);
                $displayCurve = $eqLogic->getConfiguration("display_graph{$g}_curve{$i}", 0);
                $miniValue = $eqLogic->getConfiguration("graph{$g}_mini{$i}", '');
                $maxiValue = $eqLogic->getConfiguration("graph{$g}_maxi{$i}", '');
                $plotlines = $eqLogic->getConfiguration("graph{$g}_plotlines{$i}", '') == '' ? 'null' : floatval($eqLogic->getConfiguration("graph{$g}_plotlines{$i}"));
                $colorHighchartsCurve = $defaultHightchartColors[($i-1)];
                $colorCurve = $eqLogic->getConfiguration("graph{$g}_color{$i}", $colorHighchartsCurve);
                $colorPlotlines = $defaultColors == 1 ? $colorHighchartsCurve : $colorCurve;

                if ($displayCurve == '0' || empty($cmdGraphe)) {
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
                    $manualUnit = trim($eqLogic->getConfiguration("graph{$g}_unite{$i}", ' '));
                    if ($manualUnit !== '') {
                        $unite = $manualUnit;
                    } else {
                        $unite = ($cmd && $cmd->getUnite()) ? $cmd->getUnite() : ' ';
                    }
                    $units[] = $unite;
                    $unit = ($unite == ' ' || $unite == '') ? 'sans' : $unite;
                    if (!isset($plot[$unit])){
                        $plot[$unit] = "
                        {
                            id: {$i},
                            dashStyle: 'longdashdot',
                            color: '$colorPlotlines',
                            value: $plotlines,
                            width: 2
                        },";
                    }
                    if (!isset($colorYAxis[$unit])) {
                        $colorYAxis[$unit] = $colorPlotlines;
                    }
                   
                    if (!isset($mini[$unite]) || ($miniValue!= '' && $miniValue < $mini[$unite])) {
                        $mini[$unite] = $miniValue;
                    }
                    if (!isset($maxi[$unite]) || ($maxiValue != '' && $maxiValue > $maxi[$unite])) {
                        $maxi[$unite] = $maxiValue;
                    }
                    $indexNom = empty($indexNom) ? $cmd->getName() : $indexNom;
                }
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

        //log::add(__CLASS__, 'debug', "mini = ". json_encode($mini) . " maxi = ". json_encode($maxi));

        // construire les axes Y
        $showYAxis = $eqLogic->getConfiguration("graph{$g}_show_yAxis", 1);
        $visible = $showYAxis ? 'true' : 'false';
        $alternateYAxis = $eqLogic->getConfiguration("graph{$g}_alternate_yAxis", 1);
        $position = 'true'; // commencer à droite
        $yAxisJS = '';
        $z = 5;
        foreach ($uniqueUnits as $idx => $u) {
            $unit = ($u == ' ' || $u == '') ? 'sans' : $u;
            $offset = $idx * 20;
            $YaxisColor  = $colorYAxis[$unit];
            $crossHairColor  = $crosshair[$g] == 'false' ? $colorYAxis[$unit] : '';
            $cros = $crosshair[$g] == 'false' ? 'true' : 'false';
            $align = ($position == 'false' && $alternateYAxis == 1) ? 'right' : 'left';
            $yAxisJS .= "{
                plotLines: [ " . (isset($plot[$unit]) ? $plot[$unit] : '') . " ],
                max: " . (isset($maxi[$u]) && $maxi[$u] !== '' ? floatval($maxi[$u]) : 'null') . ",
                min: " . (isset($mini[$u]) && $mini[$u] !== '' ? floatval($mini[$u]) : 'null') . ",
                visible: {$visible},
                opposite: {$position},
                offset: {$offset},
                labels: {
                    format: '{value} " . ($u !== '' ? " " . addslashes($u) : '') . "',
                    align: '{$align}',
                    x: 8,
                    y: 4,
                    style: { 
                        fontSize: '11px',
                        color: '$YaxisColor'
                    },
                    " . ($nbAxes > 1 ? "useHTML: true,
                    formatter: function () {
                        return '<div style=\"transform: rotate(-45deg); transform-origin: left center; margin-top: 15px; white-space: nowrap;\">' + this.value + ' {$u}</div>';
                    }" : "") . "
                },
                crosshair: {
                    width: " . ($showYAxis ? '1' : '0') . ",
                    color: '$crossHairColor',
                    dashStyle: 'Dash',
                    zIndex: $z,
                    enabled: true,
                    snap: $cros,
                }
            },";
            $z += 1;
            $position = ($position == 'true' && $alternateYAxis == 1) ? 'false' : 'true'; // alterner gauche/droite
        }
        
        
        //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g}: Y Axis JS: " . $yAxisJS);

        $first = false; // Reset for second loop
        $nbSeries = 0;

        $defaultColors = $eqLogic->getConfiguration("graph{$g}_default_color", 0);
        
        $checkOrderingUsed = [];
        for ($i = 1; $i <= 10; $i++) {
            $index = str_pad($i, 2, '0', STR_PAD_LEFT);
            $affOrdering = intval($eqLogic->getConfiguration("graph{$g}_curve{$i}_order", ''));
            if (in_array($affOrdering, $checkOrderingUsed) || $affOrdering == '') {
                // ordre déjà utilisé, forcer à la fin
                $affOrdering += 10;
            }
            $checkOrderingUsed[] = $affOrdering;
            $cmdKey = "graph{$g}_cmdGraphe{$index}";
            $nomKey = "graph{$g}_index{$index}_nom";
            //$colorKey = "graph{$g}_color{$i}";
            $curveTypeKey = "graph{$g}_curve{$i}_type";
            $stackingOptionEnabled = $eqLogic->getConfiguration("stacking_graph{$g}_curve{$i}", 0) ? true : false;
            $stackingOption = ($stackingOptionEnabled) ? $stackingOption : null; 
            $cmdGraphe = $eqLogic->getConfiguration($cmdKey);
            $indexNom = $eqLogic->getConfiguration($nomKey);
            
            $colorHighchartsCurve = $defaultHightchartColors[($i-1)];
            $colorCurve = $eqLogic->getConfiguration("graph{$g}_color{$i}", $colorHighchartsCurve);
            $color = $defaultColors == 1 ? $colorHighchartsCurve : $colorCurve;

            //$color = $eqLogic->getConfiguration($colorKey, $defaultColors[$i-1] ?? '#000000');

            $colorJS = "color: " . json_encode($color) . ",";
            $curveTypeOverride = $eqLogic->getConfiguration($curveTypeKey, 'inherit_curve');
            $stairStepKey = $eqLogic->getConfiguration("graph{$g}_curve{$i}_stairStep", 0) ? 'true' : 'false';
            $variation = $eqLogic->getConfiguration("graph{$g}_curve{$i}_variation", 0) ? true : false;
            $displayCurve = $eqLogic->getConfiguration("display_graph{$g}_curve{$i}", 0);
            $coef = 1;
            
            if ($displayCurve == '0' || empty($cmdGraphe)) {
                continue;
            }

            if (!$first){
                    $first = true;
            } else {
                if ($compareType == 'prev_year' || $compareType == 'prev_year_month'){
                    continue;
                }
            }

            $nbSeries += 1;
            
            //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} Curve {$i}: Processing with command {$cmdGraphe}, name {$indexNom}, compare={$compareType} and first={$first}");


            $cmd = cmd::byId(str_replace('#', '', $cmdGraphe));
            $listeHisto = ''; 
            $cmdId = '';
            if (is_object($cmd)) {
                $indexNom = empty($indexNom) ? $cmd->getName() : $indexNom;
                $finalCurveType = $curveTypeOverride;
                if ($finalCurveType === 'inherit_curve') {
                    $finalCurveType = $graphType;
                }

                if ($curveTypeOverride === 'timeline' || $graphType === 'timeline') {
                    $finalCurveType = 'timeline';
                    $chartOrStock = 'chart';
                    $refPrec = $eqLogic->getConfiguration("graph{$g}_show_refPrec", 1) ? true : false;
                    $inverted = $eqLogic->getConfiguration("graph{$g}_inverted", 1) ? 'true' : 'false';
                    if ($inverted== 'true') {
                        $xRangeSelectorButtonPosition = $titleAlign == 'left' ? -40 : -70;
                        $xZoomResetButtonPosition = $titleAlign == 'left' ? -120 : 0;
                    }
                    $tooltipEnabled = $eqLogic->getConfiguration("graph{$g}_tooltip_enabled", 1) ? 'true' : 'false';
                    $limitHisto = intval($eqLogic->getConfiguration("graph{$g}_nbPointsTimeLine", 300));
                    if ($limitHisto <= 0 || $limitHisto > 300) {
                        $limitHisto = 300;
                    }
                }

                if ($stackingOptionEnabled && (!is_null($stackingOption) || $stackingOption != 'null')) {
                    $alignThresholdsJS = 'false';
                }

                // log::add(__CLASS__, 'debug', "alignThresholdsJS $alignThresholdsJS stackingOptionEnabled $stackingOptionEnabled stackingOption $stackingOption finalCurveType $finalCurveType ");


                $manualUnit = trim($eqLogic->getConfiguration("graph{$g}_unite{$i}", ''));
                if ($manualUnit !== '') {
                    $unite = $manualUnit;
                } else {
                    $unite = ($cmd && $cmd->getUnite()) ? $cmd->getUnite() : '';
                }
                $coef = floatval($eqLogic->getConfiguration("graph{$g}_coef{$i}", '1'));
                $histo = $cmd->getHistory($startTime, isset($endTime) ? $endTime : null);
                //log::add(__CLASS__, 'debug', "Equipment2: '{$nameEqpmnt}' Graph {$g} Curve {$i}: Retrieved " . count($histo) . " history records: date= 2026-01-14 23:39:03" . strtotime("2026-01-14 23:39:03 UTC"));
                
                $listeHisto = [];
                $recordYear = null;
                $currentYear = (int)date('Y');
                $currentMonth = (int)date('m');
                $monthToStart = (int)$rollingStartMonth;
                $rolling = false;
                $label = '';
                $text = '';
                $description = '';

                $dataGroupingJS = 'enabled: false,';
                $regroup = $eqLogic->getConfiguration("graph{$g}_curve{$i}_regroup", 'aucun');
                $typeRegroup = $eqLogic->getConfiguration("graph{$g}_curve{$i}_typeRegroup", 'aucun');
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
                
                // Si la courbe est de type timeline → on transforme les données
                if ($finalCurveType === 'timeline') {
                    foreach ($histo as $record) {
                        $ts = strtotime($record->getDatetime() . ' UTC') * 1000;
                        $value = $record->getValue();

                        $previousLabel = $label ?? '';  
                        $label = (is_numeric($value)) ? round($value * $coef, 2) : $value;
                        if (!empty($unite)) {
                            $label .= ' ' . $unite;
                        }
                        if (empty($previousLabel)) {
                            $previousLabel = 'Début data';
                        } elseif ($previousLabel === $label) {
                            continue; // éviter les doublons
                        }

                        if ($refPrec) {
                            $description = $previousLabel . ' → ' . $label . ' le ' . gmdate('d/m/Y à H:i:s', $ts/1000);
                        } else {
                            $description = 'Le ' . gmdate('d/m/Y à H:i:s', $ts/1000);
                        }
                        
                        $listeHisto[] = [
                            'x' => $ts,
                            'name' => $indexNom,           // nom de la série (ou de l'événement)
                            'label' => $label,             // texte principal sur la timeline
                            'description' => $description, // texte détaillé dans le popup
                        ];
                    }
                    if (count($listeHisto) > $limitHisto) {
                        $listeHisto = array_slice($listeHisto, -$limitHisto);
                        $message .= "le graphique {$g} de l'équipement '{$nameEqpmnt}' a été limité à {$limitHisto} points pour la timeline '{$indexNom}'.";
                    }
                    $yAxisJS = "{ visible: false }";
                } else {

                    //$recordData = [];
                    $prevValue = null;
                    $prevValueHisto = 0;
                    foreach ($histo as $record) {
                        $valueHisto = $record->getValue();
                        if ($variation){
                            $prevValueHisto = $valueHisto;
                            if ($prevValue!==null){
                                $valueHisto =  $valueHisto - $prevValue;
                                $prevValue = $prevValueHisto;
                                $ts = strtotime($record->getDatetime() . ' UTC') * 1000;
                            } else {
                                $prevValue = $prevValueHisto;
                                $ts = strtotime($record->getDatetime() . ' UTC') * 1000;
                                continue;
                            }
                        }
                         
                        if ($compareType == 'none'){
                            $ts = strtotime($record->getDatetime() . ' UTC') * 1000;
                            $listeHisto[] = [$ts,  $valueHisto * $coef];
                        } elseif ($compareType == 'prev_year') {
                            $recordDate = new DateTime($record->getDatetime() . ' UTC');
                            $recordYear = (int)$recordDate->format('Y');
                            $recordMonth = (int)$recordDate->format('m');
                            if ($recordMonth < $monthToStart) {
                                $recordYear = $recordYear - 1;
                                $rolling = true;
                            }
                            $yearsDiff = $currentYear - $recordYear;
                            $adjustedDate = $recordDate->modify("+{$yearsDiff} years");
                            $ts = $adjustedDate->getTimestamp() * 1000;
                            $recordData[$recordYear][] = [$ts, $valueHisto * $coef];
                        } elseif ($compareType == 'prev_year_month') {
                            $recordDate = new DateTime($record->getDatetime() . ' UTC');
                            $recordYear = (int)$recordDate->format('Y');
                            $recordMonth = (int)$recordDate->format('m');
                            if ($recordMonth == $compareMonth) {
                                $yearsDiff = $currentYear - $recordYear;
                                $adjustedDate = $recordDate->modify("+{$yearsDiff} years");
                                $ts = $adjustedDate->getTimestamp() * 1000;
                                $recordData[$recordYear][] = [$ts, $valueHisto * $coef];
                            }
                        }
                    }
                }

                $cmdId = str_replace('#', '', $cmdGraphe);
            }
            
            $xAxisJS = "type: 'datetime',
                        ordinal: false,";
            $xAxisDateTimeLabelFormatJS = '';

            $headerFormatJS = '<span>{point.key}</span><br>';
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
                            { type: 'second', count: 30, text: '30s' },
                            { type: 'minute', count: 1, text: '1m' },
                            { type: 'minute', count: 5, text: '5m' },
                            { type: 'minute', count: 15, text: '15m' },
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
            //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} Curve {$i}: Unit '{$unite}' assigned to axis index {$axisIndex}");

            if ($finalCurveType === 'timeline') {
                $dataLabelsOverlap = $eqLogic->getConfiguration("graph{$g}_dataLabels_overlaps", 0) ? 'true' : 'false';
                $seriesJS .= "{
                        name: " . json_encode($indexNom) . ",
                        index: {$affOrdering},
                        type: 'timeline',
                        pointInterval: 24 * 3600 * 1000,
                        data: " . json_encode($listeHisto) . ", 
                        legendIndex: {$i},
                        marker: { symbol: 'circle' },
                        dataLabels: {
                            allowOverlap: {$dataLabelsOverlap},
                            format: '<span style=\"color:{point.color}\">● </span><span ' + 'style=\"font-weight: bold;\" > ' + '{point.name}</span><br/>{point.x:%d-%m-%y %H:%M:%S}<br/>{point.label}',
                            width: 200,
                        },
                        tooltip: {
                            outside: false,
                            pointFormat: '<span style=\"color:{point.color}\">● </span><span ' + 'style=\"font-weight: bold;\" > ' + '{point.name}</span><br/></span><b>{point.label}</b><br/>{point.description}<br/>',
                        }
                    },\n";
            } else {
                if ($compareType == 'prev_year' && isset($recordData) && is_array($recordData)) {
                    $cmdUpdateJS = '';
                    $nbSeries = count($recordData);
                    $affOrdering = $nbSeries;
                    if ($nbSeries > 2) {
                        $baseSeries = 1;
                        $navigatorEnabled = $configNavigatorEnabled;
                    } else {
                        $navigatorEnabled = 'false';
                        $baseSeries = 0;
                    }
                    $actualisation = false;
                    foreach ($recordData as $year => $data) {
                        if ($year == $currentYear) {
                            $actualisation = $updateEnabled; // ne pas mettre à jour si pas l'année courante
                            $updateAppend = 'false';
                        }

                        //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} i= {$i} nbSeries= {$nbSeries} year= {$year} actualisation= " . ($actualisation ? 'true' : 'false'));
                        if ($rolling){
                            $years = $year . '.' . (substr($year,2,2) + 1);
                        } else {
                            $years = $year;
                        }
                        $seriesJS .= "{
                            name: " . json_encode($indexNom . " - {$years}") . ",
                            index: {$affOrdering},
                            showInNavigator: true,
                            borderColor: " . json_encode($color) . ",
                            step: {$stairStepKey},
                            type: " . json_encode($finalCurveType) . ",
                            data: ". json_encode($data) . ",
                            valueSuffix: " . json_encode(' ' .$unite) . ",
                            tooltip: {
                                valueSuffix: " . json_encode(' ' .$unite) . "
                            },
                            yAxis: {$axisIndex},

                            stacking: '$stackingOption',
                            dataGrouping: { {$dataGroupingJS}
                                dateTimeLabelFormats: { {$dataGroupingDateTimeLabelFormatsJS} }
                            },
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
                    $navigatorJS =    " 
                        enabled: $navigatorEnabled,
                        baseSeries: $baseSeries,
                        margin: 1
                        ";
                    //$xAxisMinJS = strtotime($year . '-01-01 00:00:00 UTC') * 1000;
                    //$xAxisMaxJS = strtotime($year . '-12-31 00:00:00 UTC') * 1000;
                    $xAxisMinJS = 'undefined';
                    $xAxisMaxJS = 'undefined';
                }

                if ($compareType == 'prev_year_month' && isset($recordData) && is_array($recordData)) {
                    $actualisation = false;
                    $cmdUpdateJS = '';
                    $nbSeries = count($recordData);
                    $affOrdering = $nbSeries;
                    foreach ($recordData as $year => $data) {
                        if ($compareMonth == $currentMonth && $year == $currentYear){
                            $actualisation = $updateEnabled; // ne pas mettre à jour si pas le mois courant de l'année courante
                            $updateAppend = 'false';
                        } 
                        //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} i= {$i} nbSeries= {$nbSeries} comparemonth: {$compareMonth} currentmonth= {$currentMonth} year= {$year} actualisation= " . ($actualisation ? 'true' : 'false'));
                        $seriesJS .= "{
                            name: " . json_encode($indexNom . " - {$year}") . ",
                            index: {$affOrdering},
                            showInNavigator: true,
                            borderColor: " . json_encode($color) . ",
                            step: {$stairStepKey},
                            type: " . json_encode($finalCurveType) . ",
                            data: ". json_encode($data) . ",
                            tooltip: {
                                valueSuffix: " . json_encode(' ' .$unite) . "
                            },
                            yAxis: {$axisIndex},
                            stacking: '$stackingOption',
                            dataGrouping: { {$dataGroupingJS}
                                dateTimeLabelFormats: { {$dataGroupingDateTimeLabelFormatsJS} }
                            },
                        },\n";
                    }
                    $xDateFormatJS = "%d %B - %Hh%M";
                    $buttonJS = "buttons: [
                                            { type: 'day', count: 7, text: '1s' },
                                            { type: 'all', text: 'Tout' }
                                        ]";
                    $navigatorJS =    " 
                        enabled: $configNavigatorEnabled,
                        margin: 1
                        ";
                    $xAxisMinJS = 'undefined';
                    $xAxisMaxJS = 'undefined';
                }


                if ($compareType == 'none'){
                    //$message.= "nombre de points pour la courbe '{$indexNom}': " . count($listeHisto) . ". ";
                    $seriesJS .= "{
                        name: " . json_encode($indexNom . ($unite !== '' ? ' (' . $unite . ')' : '')) . ",
                        index: {$affOrdering},
                        showInNavigator: true,
                        borderColor: " . json_encode($color) . ",
                        step: {$stairStepKey},
                        {$colorJS}
                        type: " . json_encode($finalCurveType) . ",
                        id: " . json_encode("graph_{$g}_curve_{$i}") . ",
                        data: ". json_encode($listeHisto) . ",
                        tooltip: {
                            pointFormat: '<span style=\"color:{series.color};font-weight:bold\"> ● </span>{$indexNom} : <b>{point.y} " . $unite . "</b><br/>',
                        },
                        //marker: { symbol: 'square' },
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
                        yAxis: {$axisIndex},
                        stacking: '$stackingOption',
                        dataGrouping: { {$dataGroupingJS}
                                        dateTimeLabelFormats: { {$dataGroupingDateTimeLabelFormatsJS} }
                                    },
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
                    $navigatorJS =    " 
                        enabled: $configNavigatorEnabled,
                        margin: 1
                        ";
                    $xAxisJS .=  "
                                labels: {
                                    skew3d: true,
                                    },";
                }

            }
            
            //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} i= {$i} nbSeries= {$nbSeries} year= {$year} cmdId= {$cmdId} actualisation= " . ($actualisation ? 'true' : 'false'));
            if ($cmdId and $actualisation) {
                //log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} i= {$i} nbSeries= {$nbSeries} graphtype= {$graphType}");
                //log::add(__CLASS__, 'debug', "{$graphType} data: " . json_encode($listeHisto));
                if ($graphType == 'timeline') {
                    if ($refPrec){
                        $cmdUpdateJS .= "
                        if ('{$cmdId}' !== '') {
                            jeedom.cmd.addUpdateFunction('{$cmdId}', function(_options) {
                                const dateLastValue = new Date(_options.collectDate + 'Z').getTime();
                                const y = parseFloat(_options.value);
                                
                                if (window.chart_g{$g}_id{$eqLogic->getId()}){
                                    for (let s = 0; s < window.chart_g{$g}_id{$eqLogic->getId()}.series.length; s++) {
                                        if (window.chart_g{$g}_id{$eqLogic->getId()} && window.chart_g{$g}_id{$eqLogic->getId()}.series[s].userOptions.index === {$affOrdering}) {
                                            const chart = window.chart_g{$g}_id{$eqLogic->getId()};
                                            const series = chart.series[s];
                                            const dateObj = new Date(new Date(_options.collectDate).getTime());
                                            const valeur = y + ' {$unite}';
                                            
                                            // Récupérer le label du dernier point existant (si il y en a un)
                                            let previousLabel = '';
                                            const points = series.points;
                                            if (points.length > 0) {
                                                const lastPoint = points[points.length - 1];
                                                previousLabel = lastPoint.label || '';
                                            }

                                            const dateFormatee = previousLabel + ' → ' + valeur + ' le ' + 
                                                                    dateObj.toLocaleDateString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', }) + 
                                                                    ' à ' + 
                                                                    dateObj.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit',second: '2-digit',});

                                            series.addPoint({
                                                x: dateLastValue,
                                                name: '{$indexNom}',
                                                label: valeur,
                                                description: dateFormatee 
                                            }, true, $updateAppend, true);  // redraw, shift (supprime le plus ancien si trop de points), animation
                                        };
                                    }
                                };
                            });
                        }\n";
                    } else {
                        $cmdUpdateJS .= "
                        if ('{$cmdId}' !== '') {
                            jeedom.cmd.addUpdateFunction('{$cmdId}', function(_options) {
                                const dateLastValue = new Date(_options.collectDate + 'Z').getTime();
                                const y = parseFloat(_options.value);
                                
                                if (window.chart_g{$g}_id{$eqLogic->getId()}){
                                    for (let s = 0; s < window.chart_g{$g}_id{$eqLogic->getId()}.series.length; s++) {
                                        if (window.chart_g{$g}_id{$eqLogic->getId()} && window.chart_g{$g}_id{$eqLogic->getId()}.series[s].userOptions.index === {$affOrdering}) {
                                            const chart = window.chart_g{$g}_id{$eqLogic->getId()};
                                            const series = chart.series[{$nbSeries}-1];
                                            const dateObj = new Date(new Date(_options.collectDate).getTime());
                                            const valeur = y + ' {$unite}';
                                            
                                            const dateFormatee = 'Le ' + 
                                                                    dateObj.toLocaleDateString('fr-FR', {day: '2-digit', month: '2-digit', year: 'numeric', }) + 
                                                                    ' à ' + 
                                                                    dateObj.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit',second: '2-digit',});

                                            series.addPoint({
                                                x: dateLastValue,
                                                name: '{$indexNom}',
                                                label: valeur,
                                                description: dateFormatee 
                                            }, true, $updateAppend, true);  // redraw, shift (supprime le plus ancien si trop de points), animation
                                        };
                                    };
                                };
                            });
                        }\n";
                    }
                    //log::add(__CLASS__, 'debug', "graph{$g} nbseries: {$nbSeries}");
                } else {
                    $var = $variation ? 'true':'false';
                    log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' Graph {$g} Curve {$i}: $nbSeries series - affOrdering: {$affOrdering}"); 
                    $cmdUpdateJS .= "
                    if ('{$cmdId}' !== '') {
                        jeedom.cmd.addUpdateFunction('{$cmdId}', function(_options) {
                            
                            debug = false;
                            
                            if (window.chart_g{$g}_id{$eqLogic->getId()}){
                                for (let s = 0; s < window.chart_g{$g}_id{$eqLogic->getId()}.series.length; s++) {
                                    if (window.chart_g{$g}_id{$eqLogic->getId()} && window.chart_g{$g}_id{$eqLogic->getId()}.series[s].userOptions.index === {$affOrdering}) {
                                        if(debug){console.log('test: ', window.chart_g{$g}_id{$eqLogic->getId()}.series);}
                                        
                                        const dateLastValue = new Date(_options.collectDate + 'Z').getTime();

                                        const variation = ({$var} === true || {$var} === 'true' || {$var} === 1 || {$var} === '1');

                                        const series = window.chart_g{$g}_id{$eqLogic->getId()}.series[s];
                                        if (debug){console.log('series: ', series);}
                                        let currentRawValue = _options.value;
                                        if (debug){console.log('options display: ', _options.display_value, ' options value: ', _options.value, ' options unit: ', _options.unit, ' options raw unit: ', _options.raw_unit, ' date: ', _options.collectDate);}
                                        if (debug){console.log('options: ', _options);}
                                        
                                        // Récupère la dernière valeur brute stockée (ou null si première fois)
                                        let lastRawValue = series.userOptions?.lastRawValue ?? null;

                                        let yToAdd = currentRawValue * {$coef}; // par défaut : valeur brute

                                        if (variation) {
                                            if (lastRawValue !== null) {
                                                yToAdd = currentRawValue - lastRawValue; // vrai delta
                                            } else {
                                                yToAdd = 0; // premier point en mode variation
                                            }
                                        }
                                        if (debug){console.log('yToAdd: ', yToAdd);}

                                        // Ajout du point
                                        series.addPoint([dateLastValue, yToAdd], true, $updateAppend, true);

                            
                                        
                                        // Sauvegarde la valeur brute pour la prochaine mise à jour
                                        if (!series.userOptions) series.userOptions = {};
                                        series.userOptions.lastRawValue = currentRawValue;
                                    };
                                };
                            };
                        });
                    }\n";
                }
            }

        }

        
        $rangeSelectorJS = "
            enabled: {$configButtonsEnabled},
            selected: 26,
            inputEnabled: false,
            floating: true,
            allButtonsEnabled: true,
            dropdown: 'always',
            {$buttonJS},
            buttonPosition: {
                align: '$buttonAxisAlign',
                x: {$xRangeSelectorButtonPosition},
                y: {$yRangeSelectorButtonPosition},
            }
        ";

        // === DÉTECTION TIMELINE ET FORCAGE DU TYPE ===
        $isTimeline = false;
        for ($i = 1; $i <= 10; $i++) {
            $curveTypeKey = "graph{$g}_curve{$i}_type";
            $curveTypeOverride = $eqLogic->getConfiguration($curveTypeKey, 'inherit_curve');
            if ($curveTypeOverride === 'timeline' || $graphType === 'timeline') {
                $isTimeline = true;
            }
        }
        if ($isTimeline) {
            $headerFormatJS = ''; // pas de header pour les timelines
            $graphType = 'timeline';
            $xAxisNavigatorJS = '';
            if ($inverted == 'true') {
                $xAxisJS .=  "
                    dateTimeLabelFormats: {
                        millisecond: '%H:%M:%S.%L',
                        second: '%H:%M:%S',
                        minute: '%H:%M',
                        hour: '%H:%M',
                        day: '%e/%m/%y',
                        week: '%e/%m/%y',
                        month: '%m/%y',
                        year: '%Y'
                    },";
                $xAxisNavigatorJS = "xAxis: {
                    labels: {
                        formatter: function () {
                            return Highcharts.dateFormat('%d/%o', this.value);
                            }
                        }
                    },";
            }
            $navigatorJS =    " 
                enabled: $configNavigatorEnabled,
                margin: 20,
                {$xAxisNavigatorJS}
            ";
        }

        $cros = $crosshair[$g] == 'false' ? 'true' : 'false';
        $xAxisJS .= "
            crosshair: {
                enabled: true,
                snap: $cros,
            },";

        
        if ($config3DEnabled != 'true'){
            $plotBackgroundColorJS = "{$plotBgCode}";
        } else {
            $plotBackgroundColorJS = "'transparent'";
        }
        //$plotBackgroundColorJS = "{$plotBgCode}";

        $chartScripts .= "
            $(document).ready(function() {        
                    window.chart_g{$g}_id{$eqLogic->getId()} = Highcharts.{$chartOrStock}('{$containerId}', {
                        chart: {
                            alignThresholds: $alignThresholdsJS,
                            inverted: {$inverted},
                            options3d: {
                                enabled: {$config3DEnabled},
                                alpha: {$config3DAlpha},
                                beta: {$config3DBeta},
                                depth: {$config3DDepth},
                                viewDistance: {$config3DViewDistance},
                                fitToPlot: true,
                                axisLabelPosition: 'auto',
                                frame: {
                                    visible: 'auto',
                                    top: {
                                        color: {$plotBgCode3d},
                                        visible: 'auto'
                                    },
                                    bottom: {
                                        color: {$plotBgCode3d},
                                        visible: 'auto'
                                    },
                                    front: {
                                        color: {$plotBgCode},
                                        visible: 'auto'
                                    },
                                    back: {
                                        color: {$plotBgCode},
                                        visible: 'auto',
                                    },
                                    left: {
                                        color: {$plotBgCode3d},
                                        visible: 'auto'
                                    },
                                    right: {
                                        color: {$plotBgCode3d},
                                        visible: 'auto'
                                    },
                                }                    
                            },
                            panning: true,
                            panKey: 'shift',                
                            plotBackgroundColor: $plotBackgroundColorJS,
                            spacing: [10, 0, 10, 0],
                            type: '$graphType',
                            zooming: {
                                mouseWheel: true,
                                type: '{$zoomType}',
                                resetButton: {
                                    position: {
                                        align: '$buttonAxisAlign',
                                        verticalAlign: 'top',
                                        x: $xZoomResetButtonPosition,
                                        y: $yZoomResetButtonPosition,
                                    },
                                    theme: {
                                        style: {
                                            fontSize: '10px',
                                        }, 
                                    }
                                }                    
                            },                
                        },
                        credits: { enabled: false },
                        exporting: {
                            enabled: true,
                            fallbackToExportServer: false,
                            libURL: '/3rdparty/highstock/lib',
                            local: true,
                        },
                        legend: { 
                            enabled: {$showLegend},
                        },
                        navigator: { 
                            {$navigatorJS}
                        },
                        plotOptions: {
                            series: {
                                stacking: '$stackingOption',
                                groupPadding:0.1,
                                pointPadding:0,
                                fillOpacity: 0.1,
                                connectNulls: false,
                                turboThreshold: 0                    
                            },
                        },
                        rangeSelector: {
                            {$rangeSelectorJS}
                            },
                        scrollbar: {
                            {$scrollbarConfig}
                        },
                        series: [{$seriesJS}],
                        title: { 
                            floating: false,
                            margin: 0,
                            y: 5,
                            text: '{$titleGraph}', 
                            align: '{$titleAlign}',
                            x: $xTitlePosition,
                            height: 10,
                            style: { 
                                fontWeight: 'bold', 
                                color: 'rgb(100, 100, 100)' 
                            }
                        },
                        time: {
                            useUTC: true
                        },
                        tooltip: {
                            enabled: $tooltipEnabled,
                            xDateFormat: '{$xDateFormatJS}',
                            dateTimeLabelFormats: { 
                                {$dateTimeLabelFormats} 
                                },
                            backgroundColor: 'rgb(var(--bg-color))',
                            useHTML: true,
                            useHTML: true,
                            shadow: true,
                            style: {
                                color: 'rgb(var(--contrast-color))',
                            },
                            headerFormat: '{$headerFormatJS}',
                            split: $splitJS,
                            shared: $sharedJS,
                            valueDecimals: 2,
                        },
                        xAxis: {
                            showLastLabel: true,
                            min: $xAxisMinJS,
                            max: $xAxisMaxJS,
                            {$xAxisJS}
                            },
                        yAxis: [ 
                            {$yAxisJS} 
                            ],
                    });
                    $(window).trigger('resize'); // Simule un resize pour forcer le reflow
                });        
                    setTimeout(() => {
                    if (is_object(window.chart_g{$g}_id{$eqLogic->getId()})) {
                        window.chart_g{$g}_id{$eqLogic->getId()}.reflow()
                    }
                    }, 50);       
                    {$cmdUpdateJS}
                    ";
    }

    $syncCrosshairJS = "";
    
    if ($nbGraphs == 2) {
        $syncCrosshairJS = "
        syncCrosshair('chart_g1_id{$eqLogic->getId()}', 'chart_g2_id{$eqLogic->getId()}');
        syncCrosshair('chart_g2_id{$eqLogic->getId()}', 'chart_g1_id{$eqLogic->getId()}');    
        ";    
    } else if ($nbGraphs == 3) {
        $syncCrosshairJS = "
        syncCrosshair('chart_g1_id{$eqLogic->getId()}', 'chart_g2_id{$eqLogic->getId()}');
        syncCrosshair('chart_g1_id{$eqLogic->getId()}', 'chart_g3_id{$eqLogic->getId()}');
        syncCrosshair('chart_g2_id{$eqLogic->getId()}', 'chart_g1_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g2_id{$eqLogic->getId()}', 'chart_g3_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g3_id{$eqLogic->getId()}', 'chart_g1_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g3_id{$eqLogic->getId()}', 'chart_g2_id{$eqLogic->getId()}');    
        ";    
    } else if ($nbGraphs == 4) {
        $syncCrosshairJS = "
        syncCrosshair('chart_g1_id{$eqLogic->getId()}', 'chart_g2_id{$eqLogic->getId()}');
        syncCrosshair('chart_g1_id{$eqLogic->getId()}', 'chart_g3_id{$eqLogic->getId()}');
        syncCrosshair('chart_g1_id{$eqLogic->getId()}', 'chart_g4_id{$eqLogic->getId()}');
        syncCrosshair('chart_g2_id{$eqLogic->getId()}', 'chart_g1_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g2_id{$eqLogic->getId()}', 'chart_g3_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g2_id{$eqLogic->getId()}', 'chart_g4_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g3_id{$eqLogic->getId()}', 'chart_g1_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g3_id{$eqLogic->getId()}', 'chart_g2_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g3_id{$eqLogic->getId()}', 'chart_g4_id{$eqLogic->getId()}');
        syncCrosshair('chart_g4_id{$eqLogic->getId()}', 'chart_g1_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g4_id{$eqLogic->getId()}', 'chart_g2_id{$eqLogic->getId()}');    
        syncCrosshair('chart_g4_id{$eqLogic->getId()}', 'chart_g3_id{$eqLogic->getId()}');
        ";    
    }
    
    if ($nbGraphs >= 2) {
        $chartScripts .= "// Function to synchronize crosshairs
            function syncCrosshair(chartFromId, chartToId) {
                // Fonction de synchronisation protégée
                function trySync() {
                    const from = window[chartFromId];
                    const to   = window[chartToId];

                    if (!from || !to || !from.container || !to.container) {
                        // Au moins un des deux charts n'est pas prêt
                        setTimeout(trySync, 300);   // on réessaie dans 300ms
                        return;
                    }

                    // Les deux existent → on peut synchroniser
                    Highcharts.addEvent(from.container, 'mousemove', function(e) {
                        const xVal   = from.xAxis[0].toValue(e.chartX);
                        const xPixel = to.xAxis[0].toPixels(xVal);

                        // On simule l'événement pour l'autre graphique
                        const simulatedEvent = {
                            chartX: xPixel,
                            chartY: e.chartY
                        };

                        to.xAxis[0].drawCrosshair(simulatedEvent);
                        to.yAxis.forEach(axis => axis.drawCrosshair(simulatedEvent));
                    });
                }

                trySync();
            }

            $syncCrosshairJS
        ";    
    } else {
        $chartScripts .= "// No crosshair synchronization needed for a single graph.";
    }

    $replace['#graph_containers#'] = $graphContainers;
    $replace['#chart_scripts#'] = $chartScripts;
    if (!empty($message)) {
        $replace['#message#'] = '. Message: ' . $message;
    } else {
        $replace['#message#'] = '';
    }
    $replace['#coreVersion#'] = config::byKey('version',__CLASS__);
    
    log::add(__CLASS__, 'debug', "Equipment: '{$nameEqpmnt}' replace= " . json_encode($replace));

    $html = template_replace($replace, getTemplate('core', $version, 'jeeHistoGraph', __CLASS__));
    return $eqLogic->postToHtml($_version, $html);
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
    log::add('jeeHistoGraph', 'debug', "prehtml => " . print_r($eqLogic->pretoHtml('dashboard'), true));
    switch ($this->getLogicalId()) { //vérifie le logicalid de la commande      
      case 'refresh': // LogicalId de la commande rafraîchir
        log::add('jeeHistoGraph', 'debug', __('Exécution de la commande refresh pour l\'équipement ' . $eqLogic->getName(), __FILE__));
        $eqLogic->refreshWidget();
      break;
      default:
        log::add('jeeHistoGraph', 'debug', __('Erreur durant execute', __FILE__));
        break;
    }
  }

  /* * **********************Getteur Setteur*************************** */
}


?>