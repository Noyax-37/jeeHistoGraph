<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('jeeHistoGraph');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i><br><span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i><br><span>{{Configuration}}</span>
            </div>
        </div>

        <legend><i class="fas fa-chart-line"></i> {{Mes graphiques multi-historiques}}</legend>
        <?php
        if (count($eqLogics) == 0) {
            echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;color:#767676;">{{Aucun graphique créé, cliquez sur "Ajouter" pour commencer}}</div>';
        } else {
            echo '<div class="input-group" style="margin:5px;"><input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">
                  <div class="input-group-btn">
                    <a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>
                  </div></div>';

            echo '<div class="eqLogicThumbnailContainer">';
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">
                      <img src="' . $eqLogic->getImage() . '"/><br>
                      <span class="name">' . $eqLogic->getHumanName(true, true) . '</span>
                      </div>';
            }
            echo '</div>';
        }
        ?>
    </div>

    <div class="col-xs-12 eqLogic" style="display: none;">
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span></a>
                <a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span></a>
                <a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
                <a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
            </span>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Équipement}}</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-lg-7">
                            <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nom du graphique}}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="Ex: Conso électrique salon">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Objet parent}}</label>
                                <div class="col-sm-6">
                                    <select class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php foreach (jeeObject::all() as $object) {
                                            echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Options}}</label>
                                <div class="col-sm-8">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked> {{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked> {{Visible}}</label>
                                </div>
                            </div>

                            <legend><i class="fas fa-sliders-h"></i> {{Configuration du graphique}}</legend>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Période affichée (jours)}}</label>
                                <div class="col-sm-3">
                                    <input type="number" min="1" max="365" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo" value="1">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Type de graphique}}</label>
                                <div class="col-sm-4">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graphType">
                                        <option value="line">{{Ligne classique}}</option>
                                        <option value="spline">{{Courbe lisse}}</option>
                                        <option value="areaspline">{{Aire lisse}}</option>
                                        <option value="area">{{Aire}}</option>
                                        <option value="stepLine">{{Escalier}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Afficher la légende}}</label>
                                <div class="col-sm-3">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="showLegend" checked>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Empilement (aire uniquement)}}</label>
                                <div class="col-sm-4">
                                    <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="stacking">
                                        <option value="">{{Aucun}}</option>
                                        <option value="normal">{{Normal}}</option>
                                        <option value="percent">{{Pourcentage}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Nb max points par courbe}}</label>
                                <div class="col-sm-3">
                                    <input type="number" min="50" max="2000" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="maxPoints" value="500">
                                </div>
                                <div class="col-sm-5"><small>{{Évite les ralentissements sur mobile}}</small></div>
                            </div>

                            <legend><i class="fas fa-palette"></i> {{Courbes (jusqu'à 10)}}</legend>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">{{Courbe}}</label>
                                <label class="col-sm-3 control-label">{{Libellé}}</label>
                                <label class="col-sm-2 control-label">{{Couleur}}</label>
                                <label class="col-sm-5 control-label">{{Commande historique}}</label>
                            </div>

                            <?php
                            $colorsDefault = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4',
							                    '#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];
                            for ($i = 1; $i <= 10; $i++) {
                                $index = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $defaultColor = $colorsDefault[$i-1] ?? '#777777';
                                echo '<div class="form-group">';
                                echo '<label class="col-sm-2 control-label">{{Courbe '.$i.'}}</label>';
                                echo '<div class="col-sm-3"><input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="index'.$index.'_nom" placeholder="..."></div>';
                                echo '<div class="col-sm-2"><input type="color" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="color'.$i.'" value="'.$defaultColor.'"></div>';
                                echo '<div class="col-sm-5 input-group">';
                                echo '<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="cmdGraphe'.$index.'">';
                                echo '<span class="input-group-btn"><a class="btn btn-default listEquipementInfo cursor" data-input="cmdGraphe'.$index.'"><i class="fas fa-list-alt"></i></a></span>';
                                echo '</div></div>';
                            }
                            ?>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <a class="btn btn-warning" id="bt_resetColors"><i class="fas fa-palette"></i> {{Réinitialiser les couleurs}}</a>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>{{Note}} :</strong> Une courbe ne s’affiche que si le libellé et la commande sont remplis.<br>
                                Toutes les commandes doivent idéalement avoir la même unité.
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Description}}</label>
                                <div class="col-sm-9">
                                    <textarea class="eqLogicAttr form-control autogrow" data-l1key="comment" rows="6" placeholder="Ex: Graphique regroupant température, humidité et consommation du salon"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$('#bt_resetColors').on('click', function () {
    const defaultColors = <?php echo json_encode($colorsDefault); ?>;
    for (let i = 1; i <= 10; i++) {
        $('.eqLogicAttr[data-l2key=color' + i + ']').val(defaultColors[i-1]).trigger('change');
    }
});
</script>

<?php 
include_file('desktop', 'jeeHistoGraph', 'js', 'jeeHistoGraph'); 
include_file('core', 'plugin.template', 'js'); 
?>