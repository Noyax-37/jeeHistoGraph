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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
function jeeHistoGraph_install() {
    jeeHistoGraph_update();
}

// Fonction exécutée automatiquement après la mise à jour du plugin
function jeeHistoGraph_update() {

    $updateToLog = "jeeHistoGraphUpdate";

    $data = json_decode(file_get_contents(dirname(__FILE__) . '/info.json'), true);
    if (!is_array($data)) {
        log::add('jeeHistoGraphUpdate','warning',__('Impossible de décoder le fichier info.json (non bloquant ici)', __FILE__));
    }

    try {
        $core_version = $data['pluginVersion'];
        config::save('version', $core_version, 'jeeHistoGraph');
    } catch (\Exception $e) {
        $core_version = '0.0';
        log::add('jeeHistoGraphUpdate','warning',__('Pas de version de plugin (non bloquant ici)', __FILE__));
    }

    message::add('jeeHistoGraph', __('Installation du plugin jeeHistoGraph en cours...', __FILE__));
    log::add('jeeHistoGraph','debug','jeeHistoGraph_install');
    log::add('jeeHistoGraph','info','**********************************************************');
    log::add('jeeHistoGraph','info',__('********** Installation du plugin jeeHistoGraph **********', __FILE__));
    log::add('jeeHistoGraph','info',__('**** Voir log jeeHistoGraphUpdate pour plus de détail ****', __FILE__));
    log::add('jeeHistoGraph','info','**********************************************************');
    log::add('jeeHistoGraph','info','**         Core version    : '. $core_version. str_repeat(" ",27-strlen($core_version)) . '**');
    log::add('jeeHistoGraph','info','**********************************************************');

    log::add('jeeHistoGraphUpdate','debug','jeeHistoGraph_install');
    log::add('jeeHistoGraphUpdate','info','**********************************************************');
    log::add('jeeHistoGraphUpdate','info',__('********** Installation du plugin jeeHistoGraph **********', __FILE__));
    log::add('jeeHistoGraphUpdate','info','**********************************************************');
    log::add('jeeHistoGraphUpdate','info','**         Core version    : '. $core_version. str_repeat(" ",27-strlen($core_version)) . '**');
    log::add('jeeHistoGraphUpdate','info','**********************************************************');

    message::add('jeeHistoGraph', __('Mise à jour de la configuration des équipements jeeHistoGraph en cours...', __FILE__));   
    $configs = jeeHistoGraph::config();
    foreach (eqLogic::byType('jeeHistoGraph') as $eqLogic) {

        $refresh = $eqLogic->getCmd('action', 'refresh');
        if (!is_object($refresh)) {
            log::add('jeeHistoGraphUpdate', "debug", "création de refresh pour {$eqLogic->getName()}");
            $refresh = new jeeHistoGraphCmd();
            $refresh->setName(__('Rafraichir', __FILE__));
        } else {
            log::add('jeeHistoGraphUpdate','debug', "refresh existe pour l'eqlogiq {$eqLogic->getName()}");
        }
        $refresh->setEqLogic_id($eqLogic->getId());
        $refresh->setLogicalId('refresh');
        $refresh->setType('action');
        $refresh->setSubType('other');
        $refresh->save();


        foreach ($configs as $key) {
            $config[] = $key[0];
        }
        $version = $eqLogic->getConfiguration('version', '0.0');
        $actualVersion = config::byKey('version', 'jeeHistoGraph', '0.0', true);
        log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' current config version: {$version}, new plugin version: {$actualVersion}");
        if (version_compare($version, $actualVersion, '<')) {
            $decode = $eqLogic->getConfiguration();
            foreach ($decode as $key => $value) {
                if (in_array($key, $config) || substr($key, -9) == "_bg_color") {
                    if ($key == "graph1_typeRegroup" || $key == "graph2_typeRegroup" || $key == "graph3_typeRegroup" || $key == "graph4_typeRegroup") {
                        // Migration des anciennes valeurs de typeRegroup
                        switch ($value) {
                            case 'avg':
                                $newValue = 'average';
                                break;
                            case 'min':
                                $newValue = 'low';
                                break;
                            case 'max':
                                $newValue = 'high';
                                break;
                            default:
                                $newValue = $value;
                        }
                        if ($newValue != $value) {
                            log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' migrating configuration key: {$key} from value: {$value} to new value: {$newValue}");
                            $eqLogic   ->setConfiguration($key, $newValue);
                        }
                    }
                    if (substr($key, -9) == "_bg_color"){
                        $newKey = substr($key, 0, 10) . "couleur";
                        $eqLogic   ->setConfiguration($newKey, $value);
                        $eqLogic   ->setConfiguration($key, null);
                        log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' migrating configuration key: {$key} to new key: {$newKey} with value: {$value}");
                    }
                    if ($key == "graphLayout"){
                        // Migration des anciennes valeurs de graphLayout
                        switch ($value) {
                            case '2col':
                            case '2row':
                                $newValue = 'auto';
                                break;
                            default:
                                $newValue = $value;
                        }
                        if ($newValue != $value) {
                            log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' migrating configuration key: {$key} from value: {$value} to new value: {$newValue}");
                            $eqLogic   ->setConfiguration($key, $newValue);
                        }
                    }
                    if (substr($key,-4) == "_nom"){
                        if ($value != ""){
                            if (version_compare('2.04', $actualVersion, '>=')){ //met à jour la case à cocher d'affichage si la version avant mise à jour est <=2.04
                                $decomp=explode("_",$key);
                                $graph=explode("graph",$decomp[0])[1];
                                $index=(explode("index",$decomp[1])[1]);
                                $curve = intval($index) < 10 ? $index[-1] : $index;
                                $newDisplayKey="display_graph".$graph."_curve".$curve;
                                log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' migrating display configuration key: {$newDisplayKey} to value: 1 because {$key} is set to '{$value}'");
                                $eqLogic   ->setConfiguration($newDisplayKey, 1);
                            }
                        }
                    }
                    continue;
                }
                log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' removing obsolete configuration key: {$key} with value: " . json_encode($value));
                $eqLogic   ->setConfiguration($key, null);
            }
        }

        $eqLogic   ->save();

        $decode = $eqLogic->getConfiguration();
        foreach( $configs as $key ) {
            if (!isset($decode[$key[0]])) {
                log::add('jeeHistoGraphUpdate', 'debug', "EqLogic: '{$eqLogic->getName()}' adding new configuration key: {$key[0]} with default value: " . json_encode($key[1]));
                $eqLogic   ->setConfiguration($key[0], $key[1]);
            }
        }

        $eqLogic   ->setConfiguration('version', $actualVersion);
        $eqLogic   ->save();
        
    }
    

    message::add('jeeHistoGraph', __('Mise à jour du plugin jeeHistoGraph terminée, vous êtes en version', __FILE__) . ' ' . $core_version);
}

// Fonction exécutée automatiquement après la suppression du plugin
function jeeHistoGraph_remove() {
}
