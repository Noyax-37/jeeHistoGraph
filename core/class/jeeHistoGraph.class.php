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
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class jeeHistoGraph extends eqLogic {
  /*     * *************************Attributs****************************** */

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

  /*     * ***********************Methode static*************************** */

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

  /*     * *********************Méthodes d'instance************************* */

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

    $unite = '';
    $delai = 1;
    $startTime = date("Y-m-d H:i:s", time() - $delai * 24 * 60 * 60);
    $minTime = time() - 3 * 60 * 60;

    // Initialiser toutes les clés
    for ($i = 1; $i <= 10; $i++) {
        $index = str_pad($i, 2, '0', STR_PAD_LEFT);
        $replace["#listeHistoGraphe{$index}#"] = '';
        $replace["#index{$index}#"] = '';
        $replace["#idCmdGraphe{$index}#"] = '';
        $replace["#color{$i}#"] = '';
    }

    for ($i = 1; $i <= 10; $i++) {
        $index = str_pad($i, 2, '0', STR_PAD_LEFT);
        $cmdKey = "cmdGraphe{$index}";
        $nomKey = "index{$index}_nom";

        $cmdGraphe = $this->getConfiguration($cmdKey);
        $indexNom = $this->getConfiguration($nomKey);
        $color = $this->getConfiguration("color{$i}");

        // Si nom vide ou commande vide → série vide
        if (empty($indexNom) || empty($cmdGraphe)) {
            continue;
        }

        $listeHisto = '';
        $cmd = cmd::byId(str_replace('#', '', $cmdGraphe));
        if (is_object($cmd)) {
            $histo = $cmd->getHistory($startTime);
            $lastValue = null;
            $n = 0;

            foreach ($histo as $row) {
                $ts = strtotime($row->getDatetime());
                if ($ts >= $minTime) {
                    $n++;
                    $value = $row->getValue();
                    $listeHisto .= "[Date.UTC(" . date("Y", $ts) . "," . (date("m", $ts)-1) . ","
                      . date("d", $ts) . "," . date("H", $ts) . "," . date("i", $ts) . "," . date("s", $ts) . "),{$value}],\n";
                } else {
                    $lastValue = $row->getValue();
                }
            }

            if ($n == 0 && $lastValue !== null) {
                $ts = $minTime;
                $listeHisto .= "[Date.UTC(" . date("Y", $ts) . "," . (date("m", $ts)-1) . ","
                  . date("d", $ts) . "," . date("H", $ts) . "," . date("i", $ts) . "," . date("s", $ts) . "),{$lastValue}],\n";
            }

            $ts = time();
            $value = $cmd->execCmd();
            $listeHisto .= "[Date.UTC(" . date("Y", $ts) . "," . (date("m", $ts)-1) . ","
              . date("d", $ts) . "," . date("H", $ts) . "," . date("i", $ts) . "," . date("s", $ts) . "),{$value}],\n";

            $unite = $cmd->getUnite();
        }

        $cmdId = str_replace('#', '', $cmdGraphe);

        // Remplir les placeholders
        $replace["#listeHistoGraphe{$index}#"] = $listeHisto;
        $replace["#index{$index}#"] = $indexNom;
        $replace["#idCmdGraphe{$index}#"] = $cmdId;
        $replace["#color{$i}#"] = $color;
    }

    $replace['#unite#'] = $unite;

    log::add('jeeHistoGraph', 'debug', 'Graphique généré pour eqLogic ' . $this->getId());
    $html = template_replace($replace, getTemplate('core', $version, 'jeeHistoGraph', __CLASS__));
    return $this->postToHtml($_version, $html);
}

  /*     * **********************Getteur Setteur*************************** */
}

class jeeHistoGraphCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
  }

  /*     * **********************Getteur Setteur*************************** */
}