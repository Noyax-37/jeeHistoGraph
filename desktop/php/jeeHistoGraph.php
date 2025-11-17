<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('jeeHistoGraph');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());

// Récupérer l'équipement en cours d'édition
$eqLogic = null;
if (init('id') != '') {
    $eqLogic = eqLogic::byId(init('id'));
}
if (!is_object($eqLogic) || $eqLogic->getEqType_name() != $plugin->getId()) {
    $eqLogic = null;
}
?>

<div class="row row-overflow">
	<!-- Page d'accueil du plugin -->
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
		<!-- Boutons de gestion du plugin -->
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br>
				<span>{{Ajouter}}</span>
			</div>
			<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
				<i class="fas fa-wrench"></i>
				<br>
				<span>{{Configuration}}</span>
			</div>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes jeeHistoGraph}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun équipement jeeHistoGraph trouvé, cliquer sur "Ajouter" pour commencer}}</div>';
		} else {
			// Champ de recherche
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			// Liste des équipements du plugin
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $eqLogic->getImage() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '<span class="hiddenAsCard displayTableRight hidden">';
				echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Equipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Equipement non visible}}"></i>';
				echo '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div> <!-- /.eqLogicThumbnailDisplay -->

	<!-- Page de présentation de l'équipement -->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<!-- barre de gestion de l'équipement -->
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<!-- Onglets -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
		</ul>
		<div class="tab-content">
			<!-- Onglet de configuration de l'équipement -->
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<!-- Partie gauche de l'onglet "Equipements" -->
				<!-- Paramètres généraux et spécifiques de l'équipement -->
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
								<div class="col-sm-6">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Objet parent}}</label>
								<div class="col-sm-6">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline">';
										echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
										echo '</label>';
									}
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Options}}</label>
								<div class="col-sm-6">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas fa-sliders-h"></i> {{Configuration du ou des graphique(s)}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nombre de graphique(s)}}</label>
								<div class="col-sm-3">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="nbGraphs">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
								</div>
							</div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">{{Période affichée en jour(s) (par défaut 1 jour)}}</label>
                                <div class="col-sm-3">
                                    <input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo" value="1">
                                </div>
                            </div>

							<div class="form-group">
								<label class="col-sm-3 control-label">{{Type de courbe globale (par défaut)}}</label>
								<div class="col-sm-4">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="globalGraphType" id="globalGraphType">
										<option value="line">{{Ligne classique}}</option>
										<option value="spline">{{Courbe lisse}}</option>
										<option value="areaspline">{{Aire lisse}}</option>
										<option value="area">{{Aire}}</option>
										<option value="column">{{Colonne}}</option>
									</select>
								</div>
								<div class="col-sm-4">
									<a class="btn btn-primary" id="bt_forceAllToGlobal"><i class="fas fa-magic"></i> Tout forcer au même type</a>
								</div>
							</div>

							<div class="form-group">
                                <label class="col-sm-3 control-label">{{Afficher la légende}}</label>
                                <div class="col-sm-3">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="showLegend" checked>
                                </div>
                            </div>

							<div class="form-group">
                                <label class="col-sm-3 control-label">{{Nb max points par courbe (500 par défaut)}}</label>
                                <div class="col-sm-3">
                                    <input type="number" min="50" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="maxPoints" value="500">
                                </div>
                                <div class="col-sm-5"><small>{{ à ajuster pour éviter les ralentissements}}</small></div>
                            </div>

							<div class="form-group">
								<label class="col-sm-3 control-label"></label>
								<div class="col-sm-6">
									<a class="btn btn-info" id="bt_resetAllBg"><i class="fas fa-undo"></i> {{Remettre tous les fonds transparents}}</a>
								</div>
							</div>

							<legend></i> {{ }}</legend>

							<?php
							$nbGraphs = 1;
							if (is_object($eqLogic)) {
								$nbGraphs = $eqLogic->getConfiguration('nbGraphs', 1);
							}
							$nbGraphs = max(1, min(4, $nbGraphs));
							if ($nbGraphs < 1 || $nbGraphs > 4) $nbGraphs = 1;

							for ($g = 1; $g <= 4; $g++) {
								$display = ($g <= $nbGraphs) ? '' : 'style="display:none;"';
								echo	'<div class="graphConfig" data-graph="' . $g . '" ' . $display . '>';
								echo		'<div class="form-group">';
								echo		'<legend><i class="fas fa-chart-line"></i>{{Graphique}} ' . $g . '</legend>';
								echo			'<div class="form-group">';
                                echo				'<label class="col-sm-3 control-label"> {{titre graphique}} ' . $g . ' : </label>';
								echo				'<div class="col-sm-6">';
								echo					'<input type="text" class="eqLogicAttr configKey" data-l1key="configuration" data-l2key="titleGraph' . $g . '"/>';
								echo				'</div>';
								echo			'</div>';
								echo 			'<div class="form-group">';
								echo 				'<label class="col-sm-3 control-label">{{Fond transparent}} ' . $g . '</label>';
								echo				'<div class="col-sm-3">';
								echo 					'<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph' . $g . '_bg_transparent" checked>';
								echo 				'</div>';
								echo 				'<div class="col-sm-3 bgColorInput" style="display:none;">';
								echo 					'<input type="color" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph' . $g . '_bg_color" value="#ffffff">';
								echo 				'</div>';
								echo 			'</div>';
								echo			'<div class="form-group">';
								echo				'<label class="col-sm-3 control-label">{{Période personnalisée (jours) :}}</label>';
								echo					'<div class="col-sm-3">';
								echo						'<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo_graph' . $g . '" placeholder="{{Global}}" title="{{Laisser vide pour utiliser la période globale}}">';
								echo					'</div>';
								echo 					'<div class="col-sm-6"><small>{{Laisser vide = période globale (' . ($eqLogic ? $eqLogic->getConfiguration('delai_histo', 1) : 1) . ' jour(s))}}</small></div>';
								echo 			'</div>';
								echo			'<div class="form-group">';
								echo				'<label class="col-sm-3 control-label">{{Type de courbe par défaut :}}</label>';
								echo				'<div class="col-sm-3">';
								echo					'<select class="eqLogicAttr form-control graphTypeSelect" data-l1key="configuration" data-l2key="graph' . $g . '_type"  id="graphType' . $g . '">';
								echo						'<option value="inherit_graph">{{Configuration globale}}</option>';
								echo						'<option value="line">{{Ligne classique}}</option>';
								echo						'<option value="spline">{{Courbe lisse}}</option>';
								echo						'<option value="areaspline">{{Aire lisse}}</option>';
								echo						'<option value="area">{{Aire}}</option>';
								echo						'<option value="column">{{Colonne}}</option>';
								echo					'</select>';
								echo				'</div>';
								echo				'<div class="col-sm-4">';
								echo					'<a class="btn btn-primary" id="bt_forceAllToGraph' . $g . '"><i class="fas fa-magic"></i> Tout forcer au même type</a>';
								echo				'</div>';
								echo			'</div>';
								echo			'<div class="form-group">';
								echo				'<label class="col-sm-3 control-label">{{Faire de regroupement par :}}</label>';
								echo				'<div class="col-sm-3">';
								echo					'<select class="eqLogicAttr form-control graphRegroup" data-l1key="configuration" data-l2key="graph' . $g . '_regroup">';
								echo						'<option value="aucun">{{Aucun groupement}}</option>';
								echo						'<option value="minute">{{par minute}}</option>';
								echo						'<option value="hour">{{par heure}}</option>';
								echo						'<option value="day">{{par jour}}</option>';
								echo						'<option value="week">{{par semaine}}</option>';
								echo						'<option value="month">{{par mois}}</option>';
								echo						'<option value="year">{{par année}}</option>';
								echo					'</select>';
								echo				'</div>';
								echo				'<label class="col-sm-3 control-label">{{Type de regroupement :}}</label>';
								echo				'<div class="col-sm-3">';
								echo					'<select class="eqLogicAttr form-control graphTypeRegroup" data-l1key="configuration" data-l2key="graph' . $g . '_typeRegroup">';
								echo						'<option value="aucun">{{Aucun type}}</option>';
								echo						'<option value="avg">{{moyenne}}</option>';
								echo						'<option value="sum">{{somme}}</option>';
								echo						'<option value="min">{{mini}}</option>';
								echo						'<option value="max">{{maxi}}</option>';
								echo					'</select>';
								echo				'</div>';
								echo			'</div>';
								echo			'<div class="form-group stackingPerGraph" style="display:none;" data-graph="' . $g . ' ?>">';
								echo				'<div class="form-group stackGraph";">';
								echo					'<label class="col-sm-3 control-label">{{Empilement (si aire ou colonne) :}}</label>';
								echo					'<div class="col-sm-3">';
								echo						'<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="stacking_graph' . $g .'">';
								echo							'<option value="">{{Aucun}}</option>';
								echo							'<option value="normal">{{Normal}}</option>';
								echo							'<option value="percent">{{Pourcentage}}</option>';
								echo						'</select>';
								echo					'</div>';
								echo				'</div>';
								echo			'</div>';
								echo		'</div>';

								// Bouton RAZ couleurs
								echo		'<div class="form-group Colors">';
								echo			'<label class="col-sm-2 control-label">{{}}</label>';
								echo			'<label class="col-sm-4 control-label">{{}}</label>';
								echo			'<a class="btn btn-warning tooltips col-sm-2 btjeeHistoGraphRazCouleurs" data-graph="' . $g . '"><i class="fas fa-medkit"></i> {{Couleurs par défaut}}</a>';
								echo		'</div>';

								echo		'<div class="form-group">';
								echo			'<label class="col-sm-1 control-label">{{Courbe}}</label>';
								echo			'<label class="col-sm-2 control-label pull-left">{{Libellé}}</label>';
								echo			'<label class="col-sm-2 control-label pull-left">{{Courbe}}</label>';
								echo			'<label class="col-sm-1 control-label pull-left">{{Couleur}}</label>';
								echo			'<label class="col-sm-4 control-label pull-left">{{Commande}}</label>';
								echo			'<label class="col-sm-1 control-label pull-left">{{Unité / coef}}</label>';
								echo		'</div>';

								for ($i = 1; $i <= 10; $i++) {
									$index = str_pad($i, 2, '0', STR_PAD_LEFT);
									$colorIdx = (($g-1)*10) + $i;

									echo		'<div class="form-group">';
									echo			'<label class="col-sm-1 control-label">{{Courbe ' . $index . '}} :</label>';
									echo			'<div class="col-sm-2">';
									echo				'<input type="text" class="eqLogicAttr configKey form-control" data-l1key="configuration" data-l2key="graph' . $g . '_index' . $index . '_nom" placeholder="..."/>';
									echo			'</div>';
									echo			'<div class="col-sm-2">';
									echo				'<select class="eqLogicAttr form-control curveTypeSelect' . $g . '" data-l1key="configuration" data-l2key="graph' . $g . '_curve' . $i . '_type">';
									echo					'<option value="inherit_curve" selected>{{Config graphique}}</option>';
									echo					'<option value="line">{{Ligne}}</option>';
									echo					'<option value="spline">{{Courbe lisse}}</option>';
									echo					'<option value="areaspline">{{Aire lisse}}</option>';
									echo					'<option value="area">{{Aire}}</option>';
									echo					'<option value="column">{{Colonne}}</option>';
									echo				'</select>';
									echo			'</div>';
									echo			'<div class="col-sm-1">';
									echo				'<input type="color" class="eqLogicAttr configKey inputColor" id="favcolor_g' . $g . '_c' . $i . '" data-l1key="configuration" data-l2key="graph' . $g . '_color' . $i . '" value="#FF4500"></input>';
									echo			'</div>';
									echo			'<div class="col-sm-4">';
									echo			'<div class="input-group">';
									echo				'<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph' . $g . '_cmdGraphe' . $index . '">';
									echo				'<a class="btn btn-default listEquipementInfo cursor input-group-addon" data-input="graph' . $g . '_cmdGraphe' . $index . '"><i class="fas fa-list-alt"></i></a></input>';
									echo			'</div>';
									echo			'</div>';
									echo			'<div class="col-sm-1">';
									echo                '<input type="text" class="eqLogicAttr configKey" placeholder="Unité" title="A compléter si besoin de changement d unité, laisser vide sinon" data-l1key="configuration" data-l2key="graph' . $g . '_unite' . $i . '"></input>';
									echo			'</div>';
									echo			'<div class="col-sm-1">';
									echo                '<input type="text" class="eqLogicAttr configKey" placeholder="coef" title="coefficient à appliquer (ne sera appliqué que si l unité est complétée)" data-l1key="configuration" data-l2key="graph'. $g . '_coef' . $i . '"></input>';
									echo            '</div>';
									echo		'</div>';
								}
								echo	'</div>'; // .graphConfig
								echo	'<hr/>';
							}
							?>

							<div class="form-group">
								</br>
								</br>
								</br>
								</br>
								<label class="col-sm-12 control-label pull-left" style="text-decoration:underline">{{Explications succintes}}:</label>
								</br> </br>
								<label class="col-sm-12 control-label pull-left">{{Si le libellé de la courbe n'est pas complété alors elle ne s'affichera pas même si la commande existe}} </label>
							</div>
						</div>

						<!-- Partie droite de l'onglet "Équipement" -->
						<!-- Affiche un champ de commentaire par défaut mais vous pouvez y mettre ce que vous voulez -->
						<div class="col-lg-6">
							<legend><i class="fas fa-info"></i> {{Informations}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Description}}</label>
								<div class="col-sm-6">
									<textarea class="form-control eqLogicAttr autogrow" data-l1key="comment"></textarea>
								</div>
							</div>
						</div>
					</fieldset>
				</form>
			</div><!-- /.tabpanel #eqlogictab-->

		</div><!-- /.tab-content -->
	</div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->


<script>
document.querySelector('[data-l1key="configuration"][data-l2key="nbGraphs"]').addEventListener('change', function() {
    const nb = parseInt(this.value);
    document.querySelectorAll('.graphConfig').forEach((el, i) => {
        el.style.display = (i + 1 <= nb) ? '' : 'none';
    });
});

document.querySelectorAll('.btjeeHistoGraphRazCouleurs').forEach(btn => {
    btn.addEventListener('click', function() {
		// Couleurs par défaut par graphique
		if(typeof(defaultColors)=='undefined'){
			defaultColors = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4','#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];
		}
        const graph = this.getAttribute('data-graph');
        defaultColors.forEach((color, i) => {
            const input = document.querySelector('#favcolor_g' + graph + '_c' + (i+1));
            if (input) input.value = color;
            input.dispatchEvent(new Event('change'));
        });
    });
});

// Init au chargement
$(function() {
    const nb = parseInt($('[data-l1key="configuration"][data-l2key="nbGraphs"]').val()) || 1;
    $('[data-l1key="configuration"][data-l2key="nbGraphs"]').val(nb).trigger('change');
});

// Gestion du type de graphique global
$('#globalGraphType').on('change', function() {
    const isPerGraph = $(this).val() === 'perGraph';
    $('.graphTypePerGraph').each(function() {
        $(this).closest('.graphConfig').find('.graphTypePerGraph').toggle(isPerGraph);
    });
});

// Au chargement
$(function() {
    $('#globalGraphType').trigger('change');
});

// Gestion de l'affichage de l'input couleur selon la case "transparent"
$(document).on('change', '[data-l2key^="graph"][data-l2key$="_bg_transparent"]', function() {
    const $checkbox = $(this);
    const graphNum = $checkbox.data('l2key').match(/graph(\d)_bg_transparent/)[1];
    const $colorInput = $checkbox.closest('.form-group').find('[data-l2key="graph' + graphNum + '_bg_color"]').closest('.bgColorInput');
    
    if ($checkbox.is(':checked')) {
        $colorInput.hide();
    } else {
        $colorInput.show();
    }
});

// Bouton global : remettre tous les fonds transparents
$('#bt_resetAllBg').on('click', function() {
    $('.graphConfig:visible').each(function() {
        const graphNum = $(this).data('graph');
        const $checkbox = $(this).find('[data-l2key="graph' + graphNum + '_bg_transparent"]');
        const $colorInput = $(this).find('[data-l2key="graph' + graphNum + '_bg_color"]').closest('.bgColorInput');
        
        $checkbox.prop('checked', true).trigger('change');
        $colorInput.hide();
    });
});

// Initialisation au chargement
$(function() {
    $('[data-l2key^="graph"][data-l2key$="_bg_transparent"]').trigger('change');
});

// BOUTON MAGIQUE : Tout forcer au type global
$('#bt_forceAllToGlobal').on('click', function() {
	const globalType = $('#globalGraphType').val();
	
	// Tous les graphiques → inherit_graph
	$('.graphTypeSelect').val('inherit_graph').trigger('change');
	
	// Toutes les courbes → inherit_curve
	$('.curveTypeSelect').val('inherit_curve').trigger('change');
	
	jeedomUtils.showAlert({
		message: 'Toutes les courbes et graphiques sont maintenant forcés au type global : ' + globalType,
		level: 'success'
	});
});

// BOUTON MAGIQUE : Tout forcer au type graphique 1
$('#bt_forceAllToGraph1').on('click', function() {
	const graphType = $('#graphType1').val();
	
	// Toutes les courbes → inherit_curve
	$('.curveTypeSelect1').val('inherit_curve').trigger('change');
	
	jeedomUtils.showAlert({
		message: 'Toutes les courbes du graphique 1 sont maintenant forcés au type : ' + graphType,
		level: 'success'
	});
});

// BOUTON MAGIQUE : Tout forcer au type graphique 2
$('#bt_forceAllToGraph2').on('click', function() {
	const graphType = $('#graphType2').val();
	
	// Toutes les courbes → inherit_curve
	$('.curveTypeSelect2').val('inherit_curve').trigger('change');
	
	jeedomUtils.showAlert({
		message: 'Toutes les courbes du graphique 2 sont maintenant forcés au type : ' + graphType,
		level: 'success'
	});
});

// BOUTON MAGIQUE : Tout forcer au type graphique 3
$('#bt_forceAllToGraph3').on('click', function() {
	const graphType = $('#graphType3').val();
	
	// Toutes les courbes → inherit_curve
	$('.curveTypeSelect3').val('inherit_curve').trigger('change');
	
	jeedomUtils.showAlert({
		message: 'Toutes les courbes du graphique 3 sont maintenant forcés au type : ' + graphType,
		level: 'success'
	});
});

// BOUTON MAGIQUE : Tout forcer au type graphique 4
$('#bt_forceAllToGraph4').on('click', function() {
	const graphType = $('#graphType4').val();
	
	// Toutes les courbes → inherit_curve
	$('.curveTypeSelect4').val('inherit_curve').trigger('change');
	
	jeedomUtils.showAlert({
		message: 'Toutes les courbes du graphique 4 sont maintenant forcés au type : ' + graphType,
		level: 'success'
	});
});

</script>

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'jeeHistoGraph', 'js', 'jeeHistoGraph'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js'); ?>
