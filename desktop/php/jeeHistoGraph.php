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

							<legend><i class="fas fa-sliders-h"></i> {{Configuration commune aux graphiques}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nombre de graphique(s)}}</label>
								<div class="col-sm-3">
									<select class="eqLogicAttr form-control nbgraphs" data-l1key="configuration" data-l2key="nbGraphs">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="col-sm-3 control-label">{{Disposition des graphiques}}</label>
								<div class="col-sm-3">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graphLayout">
										<option value="auto">{{Automatique (selon le nombre)}}</option>
										<option value="1col">{{1 colonne (empilés verticalement)}}</option>
										<option value="1row">{{1 ligne (côte à côte)}}</option>
										<option value="2x2">{{Grille 2×2}}</option>
										<option value="2col">{{2 colonnes}}</option>
										<option value="2row">{{2 lignes}}</option>
										<option value="1big-2small">{{1 grand en haut + 2 petits en bas}}</option>
										<option value="2small-1big">{{2 petits en haut + 1 grand en bas}}</option>
										<option value="3grid">{{3 graphiques → 2 en haut, 1 en bas centré}}</option>
									</select>
								</div>
							</div>

							<div class="form-group col-sm-12" style="border: 1px solid grey; padding: 2px;">
								<div class="form-group">
									<label class="col-sm-3 control-label">{{Période d'affichage}}</label>
									<div class="col-sm-3">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="periode_histo">
											<option value="nbJours">{{en nombre de jours}}</option>
											<option value="deDate">{{à partir d'une date}}</option>
											<option value="deDateAdate">{{entre 2 dates}}</option>
											<option disabled="disabled">_____</option>
											<option value="dDay">{{Aujourd'hui}}</option>
											<option value="dWeek">{{Cette semaine}}</option>
											<option value="dMonth">{{Ce mois}}</option>
											<option value="dYear">{{Cette année}}</option>
											<option disabled="disabled">_____</option>
											<option value="dAll">{{Toutes les données}}</option>
										</select>
									</div>
								</div>
								
								<div class="col-sm-2">

								</div>

								<!-- Nombre de jours -->
								<div class="form-group periode_histo nbJours">
									<label class="col-sm-3 control-label">{{Période affichée en jour(s)}}</label>
									<div class="col-sm-3">
										<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo" placeholder="1">
									</div>
									<label class="col-sm-3 control-label pull-left">{{(avec actualisation auto)}}</label>
								</div>

								<!-- De date à maintenant -->
								<div class="form-group periode_histo deDate" style="display:none;">
									<label class="col-sm-2 control-label">{{De date début}}</label>
									<div class="col-sm-3">
										<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo">
									</div>
									<label class="col-sm-3 control-label pull-left">{{à maintenant (actualisation auto)}}</label>
								</div>

								<!-- Entre deux dates -->
								<div class="form-group periode_histo deDateAdate" style="display:none;">
									<label class="col-sm-2 control-label">{{Date de début}}</label>
									<div class="col-sm-3">
										<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo_2dates">
									</div>
									<label class="col-sm-1 control-label">{{Date de fin}}</label>
									<div class="col-sm-3">
										<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_fin_histo_2dates">
									</div>
								</div>
							</div>

							<div class="form-group" style="display:none">
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

							</br>
								<?php
									$nbGraphs = 1;
									if (is_object($eqLogic)) {
										$nbGraphs = $eqLogic->getConfiguration('nbGraphs', 1);
									}
									$nbGraphs = max(1, min(4, $nbGraphs));
									if ($nbGraphs < 1 || $nbGraphs > 4) $nbGraphs = 1;

									for ($g = 1; $g <= 4; $g++) {
										$display = ($g <= $nbGraphs) ? '' : 'style="display:none;"';
								?>
							
							<div class="graphConfig" data-graph="<?= $g ?>" <?= $display ?>>
								<div class="form-group col-sm-12" style="border: 6px solid grey; border-style: double; padding: 2px;">
									<legend><i class="fas fa-chart-line"></i>{{Graphique}} <?= $g ?></legend>
									<div class="form-group">
                                		<label class="col-sm-3 control-label"><b> {{titre graphique}} <?= $g ?> : </b></label>
										<div class="col-sm-6">
											<input type="text" class="eqLogicAttr configKey" data-l1key="configuration" data-l2key="titleGraph<?= $g ?>"/>
										</div>
									</div>

									<!-- Affichage du titre -->
									<div class="form-group">
										<label class="col-sm-3 control-label">{{Afficher le titre}}</label>
										<div class="col-sm-3">
											<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_showTitle" checked>
										</div>
									</div>

									<!-- Choix graphique spécial -->
									<div class="form-group" style="display: none;">
										<label class="col-sm-3 control-label">
											<i class="fas fa-sun" style="color:#f39c12"></i> {{Graphique spécial solaire}}
										</label>
										<div class="col-sm-4">
											<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graphSolar_g<?= $g ?>">
											<small class="form-text text-muted">
												→ {{4 courbes fixes : réseau, production, batterie, conso totale}}
											</small>
										</div>
									</div>

									<!-- curves -->
									<div class="form-group" style="border: 1px solid grey;">
										<div class="col-sm-2"> 
										</div>
										<span><b><i class="fas fa-chart-bar"></i> {{Paramètres des courbes}}</b></span>

										<!-- Sélecteur de courbe -->
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Type de courbe par défaut :}}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control graphTypeSelect" data-l1key="configuration" data-l2key="graph<?= $g ?>_type"  id="graphType<?= $g ?>">
													<option value="line">{{Ligne classique}}</option>
													<option value="spline">{{Courbe lisse}}</option>
													<option value="areaspline">{{Aire lisse}}</option>
													<option value="area">{{Aire}}</option>
													<option value="column">{{Colonne}}</option>
													<option value="bar">{{Barre}}</option>
												</select>
											</div>
											<div class="col-sm-4">
												<a class="btn btn-primary" id="bt_forceAllToGraph<?= $g ?>"><i class="fas fa-magic"></i> Tout forcer au même type</a>
											</div>
										</div>

										<!-- Empilement -->
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Empilement (si aire ou colonne) :}}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="stacking_graph<?= $g ?>">
													<option value="">{{Aucun}}</option>
													<option value="normal">{{Normal}}</option>
													<option value="percent">{{Pourcentage}}</option>
												</select>
											</div>
										</div>

										<!-- Bouton RAZ couleurs -->
										<div class="form-group Colors">
											<label class="col-sm-3 control-label">{{RAZ couleurs courbes : }}</label>
											<div class="col-sm-4">
												<a class="btn btn-warning tooltips btjeeHistoGraphRazCouleurs" data-graph="<?= $g ?>"><i class="fas fa-medkit"></i> {{Remettre les couleurs par défaut}}</a>
											</div>
										</div>

										<!-- Affichage de la légende -->
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Afficher la légende}}</label>
											<div class="col-sm-3">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_showLegend" checked>
											</div>
										</div>

										<!-- Fond du graphique -->
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Fond transparent :}}</label>
											<div class="col-sm-1">										
												<input type="checkbox" class="eqLogicAttr bgTransparentCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_transparent" checked>
											</div>
										</div>

										<div class="bgOptions" style="display:none;">
											<div class="col-sm-12 bgColorInput">
												<label class="col-sm-4 control-label">{{Couleur unie}}</label>
												<div class="col-sm-2">
													<input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_color" value="#ffffff">
												</div>
											</div>

											<div class="col-sm-12 form-group gradientOptions" style="margin-top:10px;display:none;">
												<label class="col-sm-4 control-label">{{Utiliser un dégradé}}</label>
												<div class="col-sm-4">
													<input type="checkbox" class="eqLogicAttr gradientCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_enabled">
												</div>
											</div>

											<div class="gradientControls" style="display:none;margin-left:20px;">
												<div class="form-group">
													<label class="col-sm-2 control-label">{{Couleur début}}</label>
													<div class="col-sm-1">
														<input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_start" value="#001f3f">
													</div>
													<label class="col-sm-1 control-label">{{Couleur fin}}</label>
													<div class="col-sm-1">
														<input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_end" value="#007bff">
													</div>
													<label class="col-sm-1 control-label">{{Angle}}</label>
													<div class="col-sm-1">
														<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_angle">
															<option value="0">0°</option>
															<option value="45">45°</option>
															<option value="90" selected>90°</option>
															<option value="135">135°</option>
															<option value="180">180°</option>
															<option value="225">225°</option>
															<option value="270">270°</option>
															<option value="315">315°</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label class="col-sm-2 control-label">{{Aperçu}}</label>
													<div class="col-sm-1">
														<div class="gradientPreview" style="width:100px;height:100px;border:2px solid #ccc;border-radius:8px;background:linear-gradient(90deg,#001f3f 0%,#007bff 100%);box-shadow:0 2px 6px rgba(0,0,0,0.2);margin:5px auto;display:flex;align-items:center;justify-content:center;">
															<small style="color:rgba(255,255,255,0.8);font-weight:bold;text-shadow:1px 1px 2px #000;"></small>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Sélecteur de période -->
									<div class="form-group" style="border: 1px solid grey; padding: 2px;">
										<div class="col-sm-2"> </div>
										<span><b><i class="fas fa-calendar-times"></i> {{Gestion de la période à afficher}}</b></span>
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Période d'affichage}}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control periodeSelect" data-l1key="configuration" data-l2key="periode_histo_graph<?= $g ?>">
													<option value="global">{{Paramètre global}}</option>
													<option value="nbJours">{{En nombre de jours}}</option>
													<option value="deDate">{{À partir d'une date}}</option>
													<option value="deDateAdate">{{Entre 2 dates}}</option>
													<option disabled="disabled">_____</option>
													<option value="dDay">{{Aujourd'hui}}</option>
													<option value="dWeek">{{Cette semaine}}</option>
													<option value="dMonth">{{Ce mois}}</option>
													<option value="dYear">{{Cette année}}</option>
													<option disabled="disabled">_____</option>
													<option value="dAll">{{Toutes les données}}</option>
												</select>
											</div>
										</div>

										<div class="col-sm-2">

										</div>

										<!-- === Champ nombre de jours === -->
										<div class="form-group periodeBlock<?= $g ?> nbJours" style="display:none;">
											<label class="col-sm-3 control-label">{{Période affichée en jour(s)}}</label>
											<div class="col-sm-2">
												<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo_graph<?= $g ?>" placeholder="{{Global}}">
											</div>
										</div>

										<!-- === Champ "de date" === -->
										<div class="form-group periodeBlock<?= $g ?> deDate" style="display:none;">
											<label class="col-sm-2 control-label">{{De date début}}</label>
											<div class="col-sm-3">
												<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo_graph<?= $g ?>">
											</div>
											<label class="col-sm-3 control-label pull-left">{{à maintenant (actualisation auto)}}</label>
										</div>

										<!-- === Champs "entre 2 dates" === -->
										<div class="form-group periodeBlock<?= $g ?> deDateAdate" style="display:none;">
											<label class="col-sm-2 control-label">{{Date de début}}</label>
											<div class="col-sm-3">
												<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo_2dates_graph<?= $g ?>">
											</div>
											<label class="col-sm-1 control-label">{{Date de fin}}</label>
											<div class="col-sm-3">
												<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_fin_histo_2dates_graph<?= $g ?>">
											</div>
										</div>
									</div>

									<!-- regroupements -->
									<div class="form-group" style="border: 1px solid grey; padding: 2px;">
										<div class="col-sm-2"> </div>
										<span><b><i class="fas fa-arrows-alt"></i> {{Gestion des regroupements / comparaisons}}</b></span>

										<div class="form-group">
											<label class="col-sm-3 control-label">{{Comparaison temporelle :}}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_compare_type">
													<option value="none">{{Aucune}}</option>
													<option value="prev_year_month">{{Même mois des années précédentes}}</option>
													<option value="prev_year">{{Années précédentes}}</option>
												</select>
											</div>

											<div class="col-sm-6 compareRollingMonth" style="display:none;">
												<label class="col-sm-3 control-label"><small>{{Mois de début}}</small></label>
												<select class="col-sm-3 eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_rolling_start_month">
													<option value="01">{{Janvier}}</option>
													<option value="02">{{Février}}</option>
													<option value="03">{{Mars}}</option>
													<option value="04">{{Avril}}</option>
													<option value="05">{{Mai}}</option>
													<option value="06">{{Juin}}</option>
													<option value="07">{{Juillet}}</option>
													<option value="08">{{Août}}</option>
													<option value="09">{{Septembre}}</option>
													<option value="10">{{Octobre}}</option>
													<option value="11">{{Novembre}}</option>
													<option value="12">{{Décembre}}</option>
												</select>
											</div>
											<div class="col-sm-3 compareMonth" style="display:none;">
												<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_compare_month">
													<option value="01">{{Janvier}}</option>
													<option value="02">{{Février}}</option>
													<option value="03">{{Mars}}</option>
													<option value="04">{{Avril}}</option>
													<option value="05">{{Mai}}</option>
													<option value="06">{{Juin}}</option>
													<option value="07">{{Juillet}}</option>
													<option value="08">{{Août}}</option>
													<option value="09">{{Septembre}}</option>
													<option value="10">{{Octobre}}</option>
													<option value="11">{{Novembre}}</option>
													<option value="12">{{Décembre}}</option>
												</select>
											</div>
										</div>									

										<div class="form-group">
											<label class="col-sm-3 control-label">{{Regrouper les données par :}}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control graphRegroup" data-l1key="configuration" data-l2key="graph<?= $g ?>_regroup">
													<option value="aucun">{{Aucun groupement}}</option>
													<option value="minute">{{par minute}}</option>
													<option value="hour">{{par heure}}</option>
													<option value="day">{{par jour}}</option>
													<option value="week">{{par semaine}}</option>
													<option value="month">{{par mois}}</option>
													<option value="year">{{par année}}</option>
												</select>
											</div>
											<label class="col-sm-3 control-label">{{Intervalle de regroupement :}}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control graphTypeRegroup" data-l1key="configuration" data-l2key="graph<?= $g ?>_typeRegroup">
													<option value="aucun">{{Aucun type}}</option>
													<option value="avg">{{moyenne}}</option>
													<option value="sum">{{somme}}</option>
													<option value="min">{{mini}}</option>
													<option value="max">{{maxi}}</option>
												</select>
											</div>
										</div>

									</div>

									<!-- navigator -->
									<div class="form-group" style="border: 1px solid grey; padding: 2px;">
										<div class="col-sm-2"> </div>
										<span><b><i class="fas fa-glasses"></i> {{Aide navigation}}</b></span>

										<!-- infobulle -->
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Type d'infobulle: }}</label>
											<div class="col-sm-3">
												<select class="eqLogicAttr form-control periodeSelect" data-l1key="configuration" data-l2key="tooltip<?= $g ?>">
													<option value="regroup">{{regroupé, une seule infobulle}}</option>
													<option value="multi">{{une infobulle par courbe}}</option>
												</select>
											</div>
										</div>

										<!-- barres et boutons -->
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Afficher la barre de navigation: }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr bgNavigator" data-l1key="configuration" data-l2key="graph<?= $g ?>_navigator" checked>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Afficher la barre de défilement: }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr bgNavigator" data-l1key="configuration" data-l2key="graph<?= $g ?>_barre" checked>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">{{Afficher les boutons: }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr bgNavigator" data-l1key="configuration" data-l2key="graph<?= $g ?>_buttons" checked>
											</div>
										</div>

										<!-- AJOUT DE LA SECTION ILLUSTRÉE -->
										<div class="form-group">
											<label class="col-sm-3 control-label"></label>
											<div class="col-sm-9">
												<div style="background:#f5f5f5; border-radius:8px; padding:15px; margin-top:10px; border:1px solid #ddd;">
													<p style="margin:0 0 15px 0; font-weight:bold; color:#333;">
														<i class="fas fa-info-circle"></i> {{À quoi correspondent ces options ?}}
													</p>

													<div style="display:flex; flex-wrap:wrap; gap:20px; justify-content:center; align-items:flex-start;">
														<!-- Navigator (mini graphique) -->
														<div style="text-align:center; flex:1; min-width:250px;">
															<p style="margin:5px 0;"><strong>{{Barre de navigation (navigator)}}</strong></p>
															<img src="plugins/jeeHistoGraph/desktop/images/navigator.jpg" 
															     style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
															<small style="color:#666; display:block; margin-top:5px;">
																{{Vue miniature du graphique complet – permet de zoomer en glissant les poignées grises}}
															</small>
														</div>

														<!-- Barre de défilement -->
														<div style="text-align:center; flex:1; min-width:250px;">
															<p style="margin:5px 0;"><strong>{{Barre de défilement}}</strong></p>
															<img src="plugins/jeeHistoGraph/desktop/images/scrollbar.jpg" 
															     style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
															<small style="color:#666; display:block; margin-top:5px;">
																{{Permet de se déplacer horizontalement dans le temps}}
															</small>
														</div>

														<!-- Boutons de période -->
														<div style="text-align:center; flex:1; min-width:250px;">
															<p style="margin:5px 0;"><strong>{{Boutons de période}}</strong></p>
															<img src="plugins/jeeHistoGraph/desktop/images/range_buttons.jpg" 
															     style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
															<small style="color:#666; display:block; margin-top:5px;">
																{{Permet de passer rapidement à une période prédéfinie}}
															</small>
														</div>

													</div>
												</div>
											</div>
										</div>

										<!-- fond du graphique -->
									</div>

									<br />


									<!-- table paramétrage courbes -->

									<div class="form-group col-sm-12">
										<table class="table table-bordered table-condensed">
											<thead>
												<tr>
													<th class="text-center" style="width: 10%;">{{Courbe}}</th>
													<th class="text-center" style="width: 13%;">{{Libellé}}</th>
													<th class="text-center" style="width: 16%;">{{Type de courbe}}</th>
													<th class="text-center" style="width: 5%;">{{Couleur}}</th>
													<th class="text-center" style="width: 40%;">{{Commande}}</th>
													<th class="text-center" style="width: 8%;">{{Unité}}</th>
													<th class="text-center" style="width: 8%;">{{Coef}}</th>
												</tr>
											</thead>
											<tbody>
												<?php
													for ($i = 1; $i <= 10; $i++) {
														$index = str_pad($i, 2, '0', STR_PAD_LEFT);
														$colorIdx = (($g-1)*10) + $i;
												?>
												<tr>
													<td class="text-center"> {{Courbe <?= $index ?>}} </td>
													<td><input type="text" class="col-sm-12 eqLogicAttr configKey form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_index<?= $index ?>_nom" placeholder="..."/></td>
													<td>
														<select class="col-sm-12 eqLogicAttr form-control curveTypeSelect<?= $g ?>" data-l1key="configuration" data-l2key="graph<?= $g ?>_curve<?= $i ?>_type">
															<option value="inherit_curve" selected>{{Config graphique}}</option>
															<option value="line">{{Ligne}}</option>
															<option value="spline">{{Courbe lisse}}</option>
															<option value="areaspline">{{Aire lisse}}</option>
															<option value="area">{{Aire}}</option>
															<option value="column">{{Colonne}}</option>
														</select>
													</td>
													<td><input type="color" class="col-sm-12 eqLogicAttr configKey inputColor" id="favcolor_g<?= $g ?>_c<?= $i ?>" data-l1key="configuration" data-l2key="graph<?= $g ?>_color<?= $i ?>" value="#FF4500"></input></td>
													<td>
														<div class="col-sm-12 input-group">
															<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_cmdGraphe<?= $index ?>">
															<a class="btn btn-default listEquipementInfo cursor input-group-addon" data-input="graph<?= $g ?>_cmdGraphe<?= $index ?>"><i class="fas fa-list-alt"></i></a></input>
														</div>
													</td>
													<td>
														<input type="text" class="col-sm-12 eqLogicAttr configKey" placeholder="Unité" title="A compléter si besoin de changement d unité, laisser vide sinon" data-l1key="configuration" data-l2key="graph<?= $g ?>_unite<?= $i ?>">
														</input>
													</td>
													<td>
														<input type="text" class="col-sm-12 eqLogicAttr configKey" placeholder="coef" title="coefficient à appliquer" data-l1key="configuration" data-l2key="graph<?= $g ?>_coef<?= $i ?>">
														</input>
													</td>
												</tr>
												<?php } ?>
											</tbody>
										</table>



									</div>
								</div>

							</div>
							<br />
								<?php } ?>

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

// === Gestion du fond (transparent / couleur / dégradé) ===
$(document).on('change', '.bgTransparentCheckbox, .gradientCheckbox, [data-l2key*="_bg_gradient_"]', function() {
    const $graphDiv = $(this).closest('.graphConfig');
    const graphNum = $graphDiv.data('graph');

    const transparent = $graphDiv.find('[data-l2key="graph' + graphNum + '_bg_transparent"]').is(':checked');
    const $bgOptions = $graphDiv.find('.bgOptions');
    const $gradientOptions = $graphDiv.find('.gradientOptions');
    const $gradientControls = $graphDiv.find('.gradientControls');
    const $preview = $graphDiv.find('.gradientPreview');

    if (transparent) {
        $bgOptions.hide();
    } else {
        $bgOptions.show();
        const useGradient = $graphDiv.find('[data-l2key="graph' + graphNum + '_bg_gradient_enabled"]').is(':checked');
        if (useGradient) {
            $gradientOptions.show();
            $gradientControls.show();
            $graphDiv.find('.bgColorInput').hide();
        } else {
            $gradientOptions.show();
            $gradientControls.hide();
            $graphDiv.find('.bgColorInput').show();
        }
        updateGradientPreview($graphDiv);
    }
});

function updateGradientPreview($graphDiv) {
    const graphNum = $graphDiv.data('graph');
    const start = $graphDiv.find('[data-l2key="graph' + graphNum + '_bg_gradient_start"]').val() || '#001f3f';
    const end = $graphDiv.find('[data-l2key="graph' + graphNum + '_bg_gradient_end"]').val() || '#007bff';
    const angle = $graphDiv.find('[data-l2key="graph' + graphNum + '_bg_gradient_angle"]').val() || '90';
    const $preview = $graphDiv.find('.gradientPreview');

    $preview.css({
        'background': `linear-gradient(${angle}deg, ${start} 0%, ${end} 100%)`
    });
}

// Initialisation au chargement
$(function() {
    $('.graphConfig').each(function() {
        $(this).find('.bgTransparentCheckbox').trigger('change');
    });
});

// Initialisation au chargement
$(function() {
    $('[data-l2key^="graph"][data-l2key$="_bg_transparent"]').trigger('change');
});

// BOUTON MAGIQUE : Tout forcer au type graphique 1
$('#bt_forceAllToGraph1').on('click', function() {
	const graphType = $('#graphType1').val();
	
	// Toutes les courbes → inherit_curve
	$('.curveTypeSelect1').val('inherit_curve').trigger('change');
	
	jeedomUtils.showAlert({
		message: 'Toutes les courbes du graphique 1 sont maintenant forcées au type : ' + graphType + " n'oubliez pas de sauvegarder",
		level: 'success'
	});
	jeedomUtils.showAlert({
		message: "n'oubliez pas de sauvegarder",
		level: 'warning'
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
	jeedomUtils.showAlert({
		message: "n'oubliez pas de sauvegarder",
		level: 'warning'
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
	jeedomUtils.showAlert({
		message: "n'oubliez pas de sauvegarder",
		level: 'warning'
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
	jeedomUtils.showAlert({
		message: "n'oubliez pas de sauvegarder",
		level: 'warning'
	});
});


$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo]', function () {
    var valeur = $(this).value();

    // On cache tout d'abord
    $('.periode_histo').hide();

    // Puis on affiche uniquement la bonne section
    if (valeur === 'nbJours') {
        $('.periode_histo.nbJours').show();
    } else if (valeur === 'deDate') {
        $('.periode_histo.deDate').show();
    } else if (valeur === 'deDateAdate') {
        $('.periode_histo.deDateAdate').show();
    }
});

/* Au chargement de la page (ou quand on ouvre la config d'un équipement existant) 
   on déclenche le changement pour afficher la bonne zone selon la valeur déjà sauvegardée */
$('.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo]').trigger('change');


// Fonction qui gère l'affichage pour un graphique donné
function updatePeriodeVisibility(graphNumber) {
	console.log('Mise à jour de l\'affichage de la période pour le graphique ' + graphNumber);
	var selectVal = $('.eqLogicAttr[data-l2key="periode_histo_graph' + graphNumber + '"]').value();
	console.log('Valeur sélectionnée : ' + selectVal);

    // On cache tout d'abord
    $('.periodeBlock' + graphNumber).hide();

    // Puis on affiche uniquement la bonne section
    if (selectVal === 'nbJours') {
        $('.periodeBlock' + graphNumber + '.nbJours').show();
    } else if (selectVal === 'deDate') {
        $('.periodeBlock' + graphNumber + '.deDate').show();
    } else if (selectVal === 'deDateAdate') {
        $('.periodeBlock' + graphNumber + '.deDateAdate').show();
    }

	//si global alors on laisse tout caché
}

// À chaque changement de select
$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo_graph1]', function () {
	updatePeriodeVisibility(1);
});

$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo_graph2]', function () {
	updatePeriodeVisibility(2);
});

$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo_graph3]', function () {
	updatePeriodeVisibility(3);
});

$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo_graph4]', function () {
	updatePeriodeVisibility(4);
});

$(document).on('change', '[data-l2key$="_compare_type"]', function() {
    const val = $(this).value();
    $(this).closest('.form-group').find('.compareMonth').toggle(val === 'prev_year_month');
    $(this).closest('.form-group').find('.compareRollingMonth').toggle(val === 'prev_year');
});


</script>

<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'jeeHistoGraph', 'js', 'jeeHistoGraph'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js'); ?>
