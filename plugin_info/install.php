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

    $data = json_decode(file_get_contents(dirname(__FILE__) . '/info.json'), true);
    if (!is_array($data)) {
        log::add('jeeHistoGraph','warning',__('Impossible de décoder le fichier info.json (non bloquant ici)', __FILE__));
    }

    try {
        $core_version = $data['pluginVersion'];
        config::save('version', $core_version, 'jeeHistoGraph');
    } catch (\Exception $e) {
        $core_version = 'inconnue';
        log::add('jeeHistoGraph','warning',__('Pas de version de plugin (non bloquant ici)', __FILE__));
    }

    message::add('jeeHistoGraph', __('Installation du plugin jeeHistoGraph en cours...', __FILE__));
    log::add('jeeHistoGraph','debug','jeeHistoGraph_install');
    log::add('jeeHistoGraph','info','**********************************************************');
    log::add('jeeHistoGraph','info',__('********** Installation du plugin jeeHistoGraph **********', __FILE__));
    log::add('jeeHistoGraph','info','**********************************************************');
    log::add('jeeHistoGraph','info','**         Core version    : '. $core_version. str_repeat(" ",27-strlen($core_version)) . '**');
    log::add('jeeHistoGraph','info','**********************************************************');
    

    message::add('jeeHistoGraph', __('Mise à jour du plugin jeeHistoGraph terminée, vous êtes en version', __FILE__) . ' ' . $core_version);
}

// Fonction exécutée automatiquement après la suppression du plugin
function jeeHistoGraph_remove() {
}
