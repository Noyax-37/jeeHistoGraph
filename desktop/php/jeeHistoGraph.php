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

// Calcul du nombre de graphiques
$nbGraphs = 1;
if (is_object($eqLogic)) {
    $nbGraphs = $eqLogic->getConfiguration('nbGraphs', 1);
}
$nbGraphs = max(1, min(4, $nbGraphs));
?>

<style>
    /* Style commun pour tous les onglets (y compris ceux des graphiques) */
    .nav-tabs > li > a {
        background-color: #555 !important;
        color: #ddd !important;
        border: 1px solid #444 !important;
        border-radius: 4px 4px 0 0;
        margin-right: 4px;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    .nav-tabs > li.active > a,
    .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        background-color: #2ea955ff !important;
        color: white !important;
        font-weight: bold;
        border: 1px solid #15e457ff !important;
        border-bottom-color: transparent !important;
    }

    .nav-tabs > li > a:hover {
        background-color: #777 !important;
        color: white !important;
    }

	.table-jeeHistoGraph {
		width: 100%;
		height: 70px;
		border: 4px double #000; /* Double trait extérieur */
		border-collapse: collapse;
		table-layout: fixed;
		margin: 0 auto;
	}
	.table-jeeHistoGraph td {
		border: 1px solid #000; /* Trait simple intérieur */
		text-align: center;
		vertical-align: middle;
		font-weight: bold;
		font-size: 0.8rem;
		padding: 0px 4px;
		box-sizing: border-box;
		overflow: hidden;
	}

	.table-responsive {
		overflow-x: auto;
	}

	.sticky-left {
		position: sticky;
		left: 0;
		background-color: rgb(var(--panel-bg-color));
		z-index: 10;
	}

	.sticky-left-1 {
		position: sticky;
		left: 80px;
		background-color: rgb(var(--panel-bg-color));
		z-index: 10;
	}

	.sticky-left-2 {
		position: sticky;
		left: 120px;
		background-color: rgb(var(--panel-bg-color));
		z-index: 10;
	}

	.sticky-left.th, /* pour les en-têtes */
	.sticky-left:first-child {
		font-weight: bold;
	}

	.checkbox-cell {
		position: relative; 
		padding: 0 !important; 
		min-height: 30px;
	}

	.checkbox-cell input[type="checkbox"] {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		margin: 0;
		cursor: pointer;
	}

	.checkbox-cell input[type="checkbox"]:hover {
		transform: translate(-50%, -50%) scale(1.1);
	}
</style>

<div class="row row-overflow">
	<!-- Page d'accueil du plugin -->
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
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
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic">';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
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
	</div>

	<!-- Page de configuration de l'équipement -->
	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex;">
			<span class="input-group-btn">
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span></a>
				<a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span></a>
				<a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
				<a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>

		<!-- Barre d'onglets principale (Équipement + Graphiques) -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="eqlogictab" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>

			<?php
			for ($g = 1; $g <= 4; $g++) {
				$display = ($g <= $nbGraphs) ? '' : 'style="display:none;"';
				echo '<li role="presentation" class="graph-tab-li" data-graph="' . $g . '" ' . $display . '>';
				echo '<a href="#graph' . $g . 'tab" aria-controls="graph' . $g . 'tab" role="tab" data-toggle="tab"><i class="fas fa-chart-line"></i> {{Graphique ' . $g . '}}</a>';
				echo '</li>';
			}
			?>
		</ul>

		<div class="tab-content">
			<!-- Onglet Équipement (configuration générale) -->
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
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
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											echo '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Catégorie}}</label>
								<div class="col-sm-6">
									<?php
									foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
										echo '<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '">' . $value['name'] . '</label>';
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

							<div class="form-group col-lg-12" style="border: 6px solid grey; border-style: double; padding: 2px;">
								<legend><i class="fas fa-sliders-h"></i> {{Configuration commune aux graphiques}}</legend>
								<div class="form-group">
									<label class="col-sm-6 control-label">{{Nombre de graphique(s)}}</label>
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
									<label class="col-sm-6 control-label">{{Disposition des graphiques}}</label>
									<div class="col-sm-5">
										<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graphLayout">
											<option value="auto">{{Automatique (selon le nombre)}}</option>
											<option value="1col">{{1 colonne (empilés verticalement)}}</option>
											<option value="1row">{{1 ligne (côte à côte)}}</option>
											<option value="2x2">{{Grille 2×2}}</option>
											<option value="1big-2small">{{1 grand en haut + 2 petits en bas}}</option>
											<option value="2small-1big">{{2 petits en haut + 1 grand en bas}}</option>
											<option value="3grid">{{3 graphiques → 2 en haut, 1 en bas centré}}</option>
										</select>
									</div>
								</div>

								<div class="col-lg-12" style="border: 1px solid grey; padding: 2px;">
									<div class="form-group">
										<label class="col-sm-6 control-label">{{Période d'affichage}}
											<sup><i class="fas fa-question-circle tooltips" title="{{Définit la période affichée par défaut sur les graphiques. Peut être modifiée dynamiquement via le menu de chaque graphique.}}"></i></sup>
										</label>
										<div class="col-sm-5">
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

									<div class="form-group periode_histo nbJours">
										<label class="col-sm-3 control-label">{{Période affichée en jour(s)}}</label>
										<div class="col-sm-3">
											<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo" placeholder="1">
										</div>
										<label class="col-sm-3 control-label pull-left">{{(avec actualisation auto)}}</label>
									</div>

									<div class="form-group periode_histo deDate" style="display:none;">
										<label class="col-sm-2 control-label">{{De date début}}</label>
										<div class="col-sm-3">
											<input type="datetime-local" value="" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo">
										</div>
										<label class="col-sm-3 control-label pull-left">{{à maintenant (actualisation auto)}}</label>
									</div>

									<div class="form-group periode_histo deDateAdate" style="display:none;">
										<label class="col-sm-2 control-label">{{Date de début}}</label>
										<div class="col-sm-3">
											<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo_2dates">
										</div>
										<label class="col-sm-1 control-label">{{à }}</label>
										<div class="col-sm-3">
											<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_fin_histo_2dates">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-6 control-label">{{Reporter les crosshairs entre graphique: }}
										<sup>
											<i class="fas fa-question-circle tooltips" title="{{reporter les lignes verticales et horizontales qui suivent la position de la souris entre tous les graphiques sélectionnés}}"></i>
										</sup>
									</label>
									<div class="col-sm-1"> G1 :
										<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph1_crosshair">
									</div>
									<div class="col-sm-1"> G2 :
										<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph2_crosshair">
									</div>
									<div class="col-sm-1"> G3 :
										<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph3_crosshair">
									</div>
									<div class="col-sm-1"> G4 :
										<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph4_crosshair">
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-3 control-label"></label>
									<div class="col-sm-6">
										<br>
										<a class="btn btn-info" id="bt_resetAllBg"><i class="fas fa-undo"></i> {{Remettre tous les fonds transparents}}</a>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<legend><i class="fas fa-info"></i> {{Informations}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Description}}</label>
								<div class="col-sm-6">
									<textarea class="form-control eqLogicAttr autogrow" data-l1key="comment"></textarea>
								</div>
							</div>
							<div class="form-group">
								<br><br><br><br>
								<label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Explications succinctes}}:</label>
								<br><br>
								<label class="col-lg-12 control-label pull-left">{{Nombre de graphique(s): }}{{choisir le nombre de graphique voulu à afficher, de 1 à 4 }}</label>
								<br><br>
								<label class="col-lg-12 control-label pull-left">{{Reporter les crosshairs : reporter les lignes qui suivent le pointeur de la souris sur les graphiques sélectionnés. La réciproque est vraie}}</label>
								<br><br>
								<label class="col-lg-12 control-label pull-left">{{Disposition des graphiques: choisir la disposition des grphiques en fonction du nombre }}</label>
								<label class="col-lg-12 control-label pull-left">{{- **Automatique** : s'adapte selon le nombre. Tout l'espace pour 1 graphique, l'un au dessus de l'autre pour 2, 2 petits en haut 1 qui prend l'espace en bas si 3 et 2x2 pour 4.}}</label>
								<div class="container my-5">
									<div class="row">
										<!-- Tableau 1 : 1 seule cellule -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr>
													<td>Gr 1</td>
												</tr>
											</table>
										</div>

										<!-- Tableau 2 : 2 cellules l'une sous l'autre -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr><td>Gr 1</td></tr>
												<tr><td>Gr 2</td></tr>
											</table>
										</div>

										<!-- Tableau 3 : 2 en haut, 1 fusionnée en bas -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr>
													<td>Gr 1</td>
													<td>Gr 2</td>
												</tr>
												<tr>
													<td colspan="2">Gr 3</td>
												</tr>
											</table>
										</div>

										<!-- Tableau 4 : 2x2 -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr>
													<td>Gr 1</td>
													<td>Gr 2</td>
												</tr>
												<tr>
													<td>Gr 3</td>
													<td>Gr 4</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
								<label class="col-lg-12 control-label pull-left">{{- **1 colonne** / **1 ligne** / **2×2**}}</label>
								<label class="col-lg-12 control-label pull-left">{{- Dispositions spéciales :}}</label>
								<label class="col-lg-12 control-label pull-left">{{- `1 grand en haut + 2 petits en bas`}}</label>
								<div class="container my-5">
									<div class="row">
										<!-- Tableau : 1 fusionnée en haut, 2 en bas -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr>
													<td colspan="2">Gr 1</td>
												</tr>
												<tr>
													<td>Gr 2</td>
													<td>Gr 3</td>
												</tr>
											</table>
										</div>
									</div>
								</div>
								<label class="col-lg-12 control-label pull-left">{{- `2 petits en haut + 1 grand en bas`}}</label>
								<div class="container my-5">
									<div class="row">
										<!-- Tableau : 2 en haut, 1 fusionnée en bas -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr>
													<td>Gr 1</td>
													<td>Gr 2</td>
												</tr>
												<tr>
													<td colspan="2">Gr 3</td>
												</tr>
											</table>
										</div>
									</div>
								</div>								
								<label class="col-lg-12 control-label pull-left">{{- `3 graphiques de taille identique → 2 en haut, 1 centré en bas`}}</label>
								<div class="container my-5">
									<div class="row">
										<!-- Tableau : 2 en haut, 1 fusionnée en bas -->
										<div class="col-sm-3 mb-4">
											<table class="table-jeeHistoGraph">
												<tr>
													<td colspan="2">Gr 1</td>
													<td colspan="2">Gr 2</td>
												</tr>
												<tr>
													<td></td>
													<td colspan="2">Gr 3</td>
													<td></td>
												</tr>
											</table>
										</div>
									</div>
								</div>											
								<br>
								<label class="col-lg-12 control-label pull-left">{{Remettre tous les fonds transparents: }}{{permet de remettre le fond de tous les graphiques transparents en un seul clic}}</label>
							</div>
						</div>
					</fieldset>
				</form>
			</div>
			<!-- Fin onglet Equipement -->

			<!-- Onglet des commandes de l'équipement -->
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a>
				<br><br>
				<div class="table-responsive">
					<table id="table_cmd" class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
								<th style="min-width:200px;width:350px;">{{Nom}}</th>
								<th>{{Type}}</th>
								<th style="min-width:260px;">{{Options}}</th>
								<th>{{Etat}}</th>
								<th style="min-width:80px;width:200px;">{{Actions}}</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div><!-- /.tabpanel #commandtab-->			

			<?php for ($g = 1; $g <= 4; $g++) { 
				$display = ($g <= $nbGraphs) ? '' : 'style="display:none;"';
			?>
			<div role="tabpanel" class="tab-pane" id="graph<?= $g ?>tab" <?= $display ?>>
				<form class="form-horizontal">
					<fieldset>
						<div class="graphConfig col-lg-12" data-graph="<?= $g ?>">
							<div class="col-lg-8">
								<div class="form-group col-lg-12" style="border: 6px solid grey; border-style: double; padding: 2px;">
									<legend><i class="fas fa-chart-line"></i> {{Graphique}} <?= $g ?></legend>

									<div class="form-group">
										<label class="col-sm-6 control-label"><b>{{Titre graphique}} <?= $g ?> :</b></label>
										<div class="col-sm-6">
											<input type="text" class="eqLogicAttr configKey form-control" data-l1key="configuration" data-l2key="titleGraph<?= $g ?>"/>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-6 control-label">{{Afficher le titre :}}</label>
										<div class="col-sm-3">
											<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_showTitle" checked>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-7 control-label">{{Positionnement du titre si affiché:}}</label>
										<div class="col-sm-2">
											<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_title_align">
												<option value="center" selected>{{Centré}}</option>
												<option value="left">{{Gauche}}</option>
												<option value="right">{{Droite}}</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-6 control-label">{{Afficher la légende :}}</label>
										<div class="col-sm-3">
											<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_showLegend" checked>
										</div>
									</div>

									<!-- Paramètres des courbes -->
									<div class="form-group" style="border: 1px solid grey;">
										<div class="col-sm-2"></div>
										<span><b><i class="fas fa-chart-bar"></i> {{Paramètres des courbes}}</b></span>

										<div class="col-sm-2"></div>
										<div class="form-group graph<?= $g ?>_nbPointsTimeLine" style="display:none;">
											<div class="form-group">
												<label class="col-sm-6 control-label">{{Paramètres pour graphique "ligne de temps": }} <sup><i class="fas fa-question-circle tooltips" title="{{Affiche des options si 'ligne de temps' est sélectionné en 'type de courbe'}}"></i></sup></label>
											</div>										
											<div class="col-sm-2"></div>
											<div class="form-group">
												<label class="col-sm-6 control-label">{{Nombre max de datas: }} <sup><i class="fas fa-question-circle tooltips" title="{{Nombre de datas limité à 300 mais peut être descendu si ralentissements}}"></i></sup></label>
												<div class="col-sm-2">
													<input type="number" max="300" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_nbPointsTimeLine" placeholder="300">
												</div>
											</div>
											<div class="col-sm-2"></div>
											<div class="form-group">
												<label class="col-sm-6 control-label">{{Affichage vertical}} 
													<sup>
														<i class="fas fa-question-circle tooltips" title="{{ si coché la ligne de temps sera affichée verticalement}}">
														</i>
													</sup>
												</label>
												<div class="col-sm-1">
													<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_inverted" checked>
												</div>
											</div>
											<div class="col-sm-2"></div>
											<div class="form-group">
												<label class="col-sm-6 control-label">{{Affichage de tous les labels}} 
													<sup>
														<i class="fas fa-question-circle tooltips" title="{{Si décoché seuls les labels qui ne se chevauchent pas seront affichés et si coché alors tous les labels apparaitront et se chevaucheront si manque de place}}"></i>
													</sup>
												</label>
												<div class="col-sm-1">
													<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_dataLabels_overlaps" checked>
												</div>
											</div>
											<div class="col-sm-2"></div>
											<div class="form-group">
												<label class="col-sm-6 control-label">{{Affichage de l'infobulle}} 
													<sup>
														<i class="fas fa-question-circle tooltips" title="{{Si décoché l'infobulle n'apparaitra plus au survol d'un point}}"></i>
													</sup>
												</label>
												<div class="col-sm-1">
													<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_tooltip_enabled" checked>
												</div>
											</div>
											<div class="col-sm-2"></div>
											<div class="form-group">
												<label class="col-sm-6 control-label">{{Référence à la valeur précédente dans l'infobulle}} 
													<sup>
														<a id="bt_openRefPrecHelp"><i class="fas fa-question-circle tooltips" title="{{Cliquer pour plus d'infos}}"></i></a>
													</sup>
												</label>
												<div class="col-sm-1">
													<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_show_refPrec" checked>
												</div>
											</div>
											<br>
										</div>

										<div class="form-group Colors">
											<label class="col-sm-6 control-label">{{RAZ couleurs courbes : }}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Remet les couleurs des courbes de ce graphique aux couleurs par défaut du plugin et décoche la case défaut si besoin}}"></i>
												</sup>
											</label>
											<div class="col-sm-4">
												<a class="btn btn-warning tooltips btjeeHistoGraphRazCouleurs" data-graph="<?= $g ?>"><i class="fas fa-medkit"></i> {{Remettre les couleurs du plugin par défaut}}</a>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-6 control-label">{{Afficher l'axe Y (valeurs) : }}</label>
											<div class="col-sm-3">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_show_yAxis" checked>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-7 control-label">{{Alterner axe Y gauche/droite : }}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Si coché les axes Y ne portant pas les mêmes unités seront alternativement à gauche et à droite}}"></i>
												</sup>
											</label>
											<div class="col-sm-3">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_alternate_yAxis" checked>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-6 control-label">{{Graphique 3D : }}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Attention: si cette option est sélectionnée les barres de navigation et de défilement ne pourront pas être affichées}}"></i>
												</sup>
											</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr bg3dCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_3D_enabled">
											</div>
										</div>

										<div class="form-group 3dGraph<?= $g ?>">
											<label class="col-sm-8 control-label">{{Angle de rotation Alpha}}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Angle selon un axe horizontal de 0 à 360}}"></i>
												</sup>
											</label>
											<div class="col-sm-2">
												<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_3D_alpha" placeholder="{{15}}">
											</div>
										</div>

										<div class="form-group 3dGraph<?= $g ?>">
											<label class="col-sm-8 control-label">{{Angle de rotation Béta}}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Angle selon un axe vertical de 0 à 360}}"></i>
												</sup>
											</label>
											<div class="col-sm-2">
												<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_3D_beta" placeholder="{{15}}">
											</div>
										</div>

										<div class="form-group 3dGraph<?= $g ?>">
											<label class="col-sm-8 control-label">{{Profondeur du graphique}}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Profondeur de la vue 3D}}"></i>
												</sup>
											</label>
											<div class="col-sm-2">
												<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_3D_depth" placeholder="{{25}}">
											</div>
										</div>
<!--
										<div class="form-group 3dGraph<?= $g ?>">
											<label class="col-sm-8 control-label">{{Distance de vision}}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{Distance entre le point de vue et le graphique}}"></i>
												</sup>
											</label>
											<div class="col-sm-2">
												<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_3D_view_distance" placeholder="{{25}}">
											</div>
										</div>
			-->
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Permettre zoom par souris axe des x : }}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{une fois le zoom réalisé on peut naviguer dans le graphique en maintenant shift enfoncé + bouton gauche de la souris}}"></i>
												</sup>
											</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_zoom_axe_x" checked>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-6 control-label">{{Permettre zoom par souris axe des y : }}
												<sup>
													<i class="fas fa-question-circle tooltips" title="{{une fois le zoom réalisé on peut naviguer dans le graphique en maintenant shift enfoncé + bouton gauche de la souris}}"></i>
												</sup>
											</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_zoom_axe_y" checked>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-6 control-label">{{Fond transparent : }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr bgTransparentCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_transparent" checked>
											</div>
										</div>

										<div class="bgOptions" style="display:none;">
											<div class="col-lg-12 bgColorInput">
												<label class="col-sm-7 control-label">{{Couleur unie}}</label>
												<div class="col-sm-2">
													<input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_couleur" value="#ffffff">
												</div>
											</div>

											<div class="col-lg-12 form-group gradientOptions" style="margin-top:10px;display:none;">
												<label class="col-sm-7 control-label">{{Utiliser un dégradé}}</label>
												<div class="col-sm-4">
													<input type="checkbox" class="eqLogicAttr gradientCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_enabled">
												</div>
											</div>

											<div class="gradientControls" style="display:none;margin-left:20px;">
												<!-- Dégradé controls (inchangés) -->
												<div class="form-group">
													<label class="col-sm-7 control-label">{{Couleur début}}</label>
													<div class="col-sm-1">
														<input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_start" value="#001f3f">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-7 control-label">{{Couleur fin}}</label>
													<div class="col-sm-1">
														<input type="color" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_bg_gradient_end" value="#007bff">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-7 control-label">{{Angle}}</label>
													<div class="col-sm-2">
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
													<label class="col-sm-7 control-label">{{Aperçu}}</label>
													<div class="col-sm-1">
														<div class="gradientPreview" style="width:100px;height:100px;border:2px solid #ccc;border-radius:8px;background:linear-gradient(90deg,#001f3f 0%,#007bff 100%);"></div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Gestion de la période -->
									<div class="form-group">
										<div class="col-sm-2"></div>
										<span><b><i class="fas fa-calendar-times"></i> {{Gestion de la période à afficher}}</b></span>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Période d'affichage}}</label>
											<div class="col-sm-4">
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

										<div class="form-group periodeBlock<?= $g ?> nbJours" style="display:none;">
											<label class="col-sm-6 control-label">{{Période affichée en jour(s)}}</label>
											<div class="col-sm-2">
												<input type="number" min="1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="delai_histo_graph<?= $g ?>" placeholder="{{Global}}">
											</div>
										</div>

										<div class="form-group periodeBlock<?= $g ?> deDate" style="display:none;">
											<label class="col-sm-2 control-label">{{De date début}}</label>
											<div class="col-sm-3">
												<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo_graph<?= $g ?>">
											</div>
											<label class="col-sm-3 control-label pull-left">{{à maintenant (actualisation auto)}}</label>
										</div>

										<div class="form-group periodeBlock<?= $g ?> deDateAdate" style="display:none;">
											<label class="col-sm-2 control-label">{{Date de début}}</label>
											<div class="col-sm-3">
												<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_debut_histo_2dates_graph<?= $g ?>">
											</div>
											<label class="col-sm-1 control-label">{{à }}</label>
											<div class="col-sm-3">
												<input type="datetime-local" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="date_fin_histo_2dates_graph<?= $g ?>">
											</div>
										</div>
									</div>

									<!-- Regroupements / comparaisons -->
									<div class="form-group" style="border: 1px solid grey; padding: 2px;">
										<div class="col-sm-2"></div>
										<span><b><i class="fas fa-arrows-alt"></i> {{Gestion comparaisons par période pour une même commande:}}</b></span>

										<div class="form-group">
											<div class="row">
												<label class="col-sm-6 control-label">{{Comparaison temporelle :}}</label>
												<div class="col-sm-4">
													<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_compare_type">
														<option value="none">{{Aucune}}</option>
														<option value="prev_year_month">{{Même mois des années précédentes}}</option>
														<option value="prev_year">{{Années précédentes}}</option>
													</select>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12 compareRollingMonth" style="display:none;">
													<label class="col-sm-7 control-label">{{Mois de début}}</label>
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
												<div class="col-lg-12 compareMonth" style="display:none;">
													<label class="col-sm-7 control-label">{{Mois à comparer}}</label>
													<select class="col-sm-3 eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_compare_month">
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
										</div>									
									</div>

									<!-- Navigation -->
									<div class="form-group">
										<div class="col-sm-2"></div>
										<span><b><i class="fas fa-glasses"></i> {{Aide navigation}}</b></span>

										<div class="form-group">
											<div class="row">
												<label class="col-sm-6 control-label">{{Type d'infobulle: }}</label>
												<div class="col-sm-5">
													<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="tooltip<?= $g ?>">
														<option value="sans">{{aucune infobulle}}</option>
														<option value="normal">{{une infobulle par point}}</option>
														<option value="regroup">{{une seule infobulle par date commune}}</option>
														<option value="multi">{{une infobulle par courbe par date commune}}</option>
													</select>
												</div>
											</div>
										</div>

										<div class="form-group">
											<label class="col-sm-6 control-label">{{Afficher la barre de navigation: }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_navigator" checked>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Afficher la barre de défilement: }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_barre" checked>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-6 control-label">{{Afficher les boutons: }}</label>
											<div class="col-sm-1">
												<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="graph<?= $g ?>_buttons" checked>
											</div>
										</div>
											<div class="col-lg-12" style="text-align:center;">
												<a class="btn btn-info" id="bt_openNavigatorHelp"><i class="fas fa-info-circle"></i> {{À quoi correspondent les options de navigation ?}}</a>
											</div>
									</div>

									<br>
								</div>
							</div>

							<div class="col-lg-12">
								<div class="form-group col-lg-12" style="border: 6px solid grey; border-style: double; padding: 2px;">

									<!-- Tableau des courbes -->
									<div class="form-group col-lg-12">
										<div class="table-responsive">
											<table class="table table-bordered table-condensed" style="width: 100%; table-layout: fixed;">
												<thead>
													<tr>
														<th class="text-center sticky-left" style="width: 80px;">{{Courbe}}</th>
														<th class="text-center sticky-left-1" style="width: 40px;">{{Aff?}}
															<sup>
																<i class="fas fa-question-circle tooltips" title="{{Cochez pour afficher la courbe dans le graphique}}"></i>
															</sup>
														</th>
														<th class="text-center sticky-left-2" style="width: 140px;">{{Libellé}}
															<sup>
																<i class="fas fa-question-circle tooltips" title="{{Nom de la courbe dans le graphique, laisser vide pour utiliser le nom de la commande}}"></i>
															</sup>
														</th>
														<th class="text-center" style="width: 400px;">{{Commande}}</th>
														<th class="text-center" style="width: 400px;">
															<div>{{Type de courbe par défaut (ci-dessous) ou par courbe}}</div>
															<div>
																<select class="eqLogicAttr form-control graphTypeSelect" data-l1key="configuration" data-l2key="graph<?= $g ?>_type" id="graphType<?= $g ?>">
																	<option value="line">{{Ligne classique}}</option>
																	<option value="spline">{{Courbe lisse}}</option>
																	<option value="areaspline">{{Aire lisse}}</option>
																	<option value="area">{{Aire}}</option>
																	<option value="column">{{Colonne}}</option>
																	<option value="bar">{{Barre}}</option>
																	<option value="scatter">{{Simples points}}</option>
																	<option value="timeline">{{Ligne de temps (permet les valeurs alphanumériques)}}</option>
																</select>
															</div>
														</th>
														<th class="text-center" style="width: 120px;">
															<div>{{Empilement}}
																<sup>
																	<i class="fas fa-question-circle tooltips" title="{{Courbe à inclure dans l'empilement après avoir choisi le type}}"></i>
																</sup>
															</div>
															<div>
																<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="stacking_graph<?= $g ?>">
																	<option value="null">{{Aucun}}</option>
																	<option value="normal">{{Normal}}</option>
																	<option value="percent">{{Pourcentage}}</option>
																</select>
															</div>															
														</th>
														<th class="text-center" style="width: 130px;">{{Affichage des données}}</th>
														<th class="text-center" style="width: 130px;">{{Opération à appliquer}}												
															<sup>
																<i class="fas fa-question-circle tooltips" title="{{N'a aucun effet si l'affichage des données est sur 'Toutes'}}"></i>
															</sup>
														</th>
														<th class="text-center" style="width: 60px;">{{Esc.}}														
															<sup>
																<i class="fas fa-question-circle tooltips" title="{{Courbe en escalier, ne fonctionne pas avec tous les types de courbe}}"></i>
															</sup>
														</th>
														<th class="text-center" style="width: 60px;">{{Var.}}														
															<sup>
																<i class="fas fa-question-circle tooltips" title="{{Variation}}"></i>
															</sup>
														</th>
														<th class="text-center" style="width: 60px;">
															<div>
																{{Couleur}}
																<sup>
																	<i class="fas fa-question-circle tooltips" title="{{Permet de choisir la couleur de la courbe}}"></i>
																</sup>
															</div>
															<div>
																{{Défaut}}
																<sup>
																	<i class="fas fa-question-circle tooltips" title="{{Applique la couleur par défaut de Highcharts, décocher pour sélectioner celles que vous souhaitez}}"></i>
																</sup>
																<input type="checkbox" class="eqLogicAttr stairStepCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_default_color">
															</div>
														</th>
														<th class="text-center" style="width: 60px;">{{Unité}}</th>
														<th class="text-center" style="width: 60px;">{{Coef}}</th>
														<th class="text-center" style="width: 60px;">{{Mini}}
																<sup>
																	<i class="fas fa-question-circle tooltips" title="{{Attention, cette valeur s'applique à TOUTES les courbes ayant la même unité. 
																		Si plusieurs valeurs sont saisies pour la même unité alors c'est la plus petite qui est conservée}}"></i>
																</sup>
														</th>
														<th class="text-center" style="width: 60px;">{{Maxi}}
																<sup>
																	<i class="fas fa-question-circle tooltips" title="{{Attention, cette valeur s'applique à TOUTES les courbes ayant la même unité. 
																		Si plusieurs valeurs sont saisies pour la même unité alors c'est la plus grande qui est conservée}}"></i>
																</sup>
														</th>
														<th class="text-center" style="width: 60px;">{{Seuil}}
																<sup>
																	<i class="fas fa-question-circle tooltips" title="{{Définit un seuil d'attention (ligne horizontale fixe)}}"></i>
																</sup>
														</th>
													</tr>
												</thead>
												<tbody>
													<?php for ($i = 1; $i <= 10; $i++) { 
														$index = str_pad($i, 2, '0', STR_PAD_LEFT);
													?>
													<tr>
														<td class="text-center sticky-left">{{Courbe <?= $index ?>}}</td>
														<td class="text-center sticky-left-1">
															<input type="checkbox" class="eqLogicAttr stairStepCheckbox" data-l1key="configuration" data-l2key="display_graph<?= $g ?>_curve<?= $i ?>">
														</td>
														<td class="sticky-left-2"><input type="text" class="eqLogicAttr configKey form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_index<?= $index ?>_nom" placeholder="..."/></td>
														<td>
															<div class="input-group">
																<input class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="graph<?= $g ?>_cmdGraphe<?= $index ?>">
																<a class="btn btn-default listEquipementInfo cursor input-group-addon" data-input="graph<?= $g ?>_cmdGraphe<?= $index ?>"><i class="fas fa-list-alt"></i></a>
															</div>
														</td>
														<td>
															<select class="eqLogicAttr form-control curveTypeSelect<?= $g ?>" data-l1key="configuration" data-l2key="graph<?= $g ?>_curve<?= $i ?>_type">
																<option value="inherit_curve" selected>{{Config globale}}</option>
																<option value="line">{{Ligne}}</option>
																<option value="spline">{{Courbe lisse}}</option>
																<option value="areaspline">{{Aire lisse}}</option>
																<option value="area">{{Aire}}</option>
																<option value="column">{{Colonne}}</option>
																<option value="bar">{{Barre}}</option>
																<option value="scatter">{{Simples points}}</option>
															</select>
														</td>
														<td class="text-center checkbox-cell">
															<input type="checkbox" class="eqLogicAttr stairStepCheckbox" data-l1key="configuration" data-l2key="stacking_graph<?= $g ?>_curve<?= $i ?>">
														</td>
														<td>
															<select class="eqLogicAttr form-control graphRegroup" data-l1key="configuration" data-l2key="graph<?= $g ?>_curve<?= $i ?>_regroup">
																<option value="aucun">{{Toutes}}</option>
																<option value="minute">{{par minute}}</option>
																<option value="hour">{{par heure}}</option>
																<option value="day">{{par jour}}</option>
																<option value="week">{{par semaine}}</option>
																<option value="month">{{par mois}}</option>
																<option value="year">{{par année}}</option>
															</select>
														</td>
														<td>
															<select class="eqLogicAttr form-control graphTypeRegroup" data-l1key="configuration" data-l2key="graph<?= $g ?>_curve<?= $i ?>_typeRegroup">
																<option value="aucun">{{Pas d'opération}}</option>
																<option value="average">{{moyenne}}</option>
																<option value="sum">{{somme}}</option>
																<option value="low">{{mini}}</option>
																<option value="high">{{maxi}}</option>
															</select>
														</td>
														<td class="text-center checkbox-cell">
															<input type="checkbox" class="eqLogicAttr stairStepCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_curve<?= $i ?>_stairStep">
														</td>
														<td class="text-center checkbox-cell">
															<input type="checkbox" class="eqLogicAttr VariationCheckbox" data-l1key="configuration" data-l2key="graph<?= $g ?>_curve<?= $i ?>_variation">
														</td>
														<td><input type="color" class="eqLogicAttr configKey inputColor" id="favcolor_g<?= $g ?>_c<?= $i ?>" data-l1key="configuration" data-l2key="graph<?= $g ?>_color<?= $i ?>" value="#FF4500"></td>
														<td><input type="text" class="eqLogicAttr configKey form-control" placeholder="Unité" title="A compléter si besoin de changement d unité, laisser vide sinon" data-l1key="configuration" data-l2key="graph<?= $g ?>_unite<?= $i ?>"></td>
														<td><input type="text" class="eqLogicAttr configKey form-control" placeholder="coef" title="coefficient à appliquer" data-l1key="configuration" data-l2key="graph<?= $g ?>_coef<?= $i ?>"></td>
														<td><input type="text" class="eqLogicAttr configKey form-control" placeholder="mini" title="Valeur mini" data-l1key="configuration" data-l2key="graph<?= $g ?>_mini<?= $i ?>"></td>
														<td><input type="text" class="eqLogicAttr configKey form-control" placeholder="maxi" title="Valeur maxi" data-l1key="configuration" data-l2key="graph<?= $g ?>_maxi<?= $i ?>"></td>
														<td><input type="text" class="eqLogicAttr configKey form-control" placeholder="seuil" title="Seuil d'attention" data-l1key="configuration" data-l2key="graph<?= $g ?>_plotlines<?= $i ?>"></td>
													</tr>
													<?php } ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-8">
							<div class="form-group col-lg-12">
								<legend><i class="fas fa-info"></i> {{Informations}}</legend>
								<label class="col-sm-4 control-label">{{Description}}</label>
								<div class="col-sm-6">
									<textarea class="form-control eqLogicAttr autogrow" data-l1key="comment"></textarea>
								</div>
							</div>
							<div class="form-group">
								<br/><br><br><br>
								<legend><label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Explications succinctes}}:</label></legend>
								<br><br>
								<label class="col-lg-12 control-label pull-left">{{Titre du graphique: }}{{facultatif, donner un nom à votre graphique }}</label>
								<label class="col-lg-12 control-label pull-left">{{Afficher le titre, la légende: }}{{afficher ou non ces infos sur votre graphique}}</label>
								<label class="col-lg-12 control-label pull-left">{{Positionnement du titre si affiché: }}{{choisir l'emplacement du titre sur le graphique}}</label>
								<br/><br><br>
								<label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Paramètres de courbes}}</label>
								<label class="col-lg-12 control-label pull-left">{{Type de courbe par défaut : }}{{choisir le type de courbe voulue (ligne, aire, ...). S'applique à toutes les courbes du graphique qui n'ont pas de type sélectionné individuellement}}</label>
								<label class="col-lg-12 control-label pull-left">{{RAZ couleurs courbes : permet de remettre toutes les couleurs de courbes par défaut}}</label>
								<label class="col-lg-12 control-label pull-left">{{Afficher l'axe Y (valeurs) : afficher ou non l'axe des valeurs (vertical en général)}}</label>
								<label class="col-lg-12 control-label pull-left">{{Alterner axe Y gauche/droite : si plusieurs unités sont utilisées, permet d'alterner les axes Y gauche et droite pour chaque courbe}}</label>
								<label class="col-lg-12 control-label pull-left">{{Fond transparent : si déselectionné permet de choisir une couleur de fond pour le graphique}}</label>
								<br/><br><br>
								<label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Gestion de la période à afficher}}</label>
								<label class="col-lg-12 control-label pull-left">{{Période d'affichage : applique la période du paramètre choisi sur la page "équipement" ou le personnalise pour ce graphique}}</label>
								<br/><br><br>
								<label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Gestion des comparaisons par période pour une même commande}}</label>
								<label class="col-lg-12 control-label pull-left">{{Comparaison temporelle : permet de comparer d'une année sur l'autre soit la totalité de l'année soit un mois en particulier pour une commande disposant de plusieurs années d'enregistrement}}</label>
								<br/><br><br>
								<label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Aide navigation}}</label>
								<label class="col-lg-12 control-label pull-left">{{Type d'infobulle: permet de définir la façon dont les infobulles seront ou non affichées}}</label>
								<label class="col-lg-12 control-label pull-left">{{Afficher la barre de navigation, de défilement ou les boutons: voir l'aide en cliquant sur le bouton}}</label>
								<br/><br><br>
								<label class="col-lg-12 control-label pull-left" style="text-decoration:underline">{{Tableau des courbes}}</label>
								<br/><br>
								<label class="col-lg-12 control-label pull-left">{{courbe: information purement indicative donnant le numéro de la courbe}}</label>
								<label class="col-lg-12 control-label pull-left">{{Afficher: affiche ou non la courbe}}</label>
								<label class="col-lg-12 control-label pull-left">{{libellé: nom donné à la courbe}}</label>
								<label class="col-lg-12 control-label pull-left">{{Type de courbe : permet de choisir individuellement le type de courbe ou d'appliquer le paramètre global du graphique. Tous les types ne peuvent pas être mixés.}}</label>
								<label class="col-lg-12 control-label pull-left">{{Empilement: permet de positionner les courbes l'une au dessus de l'autre. Choisir d'abord le type d'empilement souhaité (normal ou en %) puis cocher les cases}}</label>
								<label class="col-lg-12 control-label pull-left">{{*** ATTENTION 1: positionner les courbes à empiler les unes après les autres sinon cela ne fonctionnera pas}}</label>
								<label class="col-lg-12 control-label pull-left">{{*** ATTENTION 2: si les données des courbes ne sont pas regroupées par le même intervalle temporel (voir ci dessous) alors cela ne fonctionnera pas non plus}}</label>
								<label class="col-lg-12 control-label pull-left">{{Affichage des données : permet de faire des regroupement par intervalles temporels (minute, heure, ...) et d'y appliquer une opération (moyenne, somme, ...)}}</label>
								<label class="col-lg-12 control-label pull-left">{{Opération à appliquer : appliquer une opération (moyenne, somme, ...) aux données pour lesquelles le choix "Affichage des données" n'est pas "Toutes"}}</label>
								<label class="col-lg-12 control-label pull-left">{{Esc.: affiche graphiquement la courbe en escalier pour le passage d'une valeur à la suivante}}</label>
								<label class="col-lg-12 control-label pull-left">{{Var.: affiche graphiquement la courbe en variation (delta) d'une valeur à la suivante. }}</label>
								<label class="col-sm-11 control-label pull-left">{{*** ATTENTION: la variation est calculée pour TOUS les points d'une commande, pour avoir la variation de cette commande sur un intervalle déterminé par "regroupement" 
																						alors il faut donc demander à faire la somme de ces variations. Par exemple "Affichage des données = par heure" + "Valeur à afficher : somme" + V coché => variation par heure}}</label>
								<label class="col-lg-12 control-label pull-left">{{Couleur : permet de choisir la couleur pour chaque courbe}}</label>
								<label class="col-lg-12 control-label pull-left">{{Commande: sélectionner ici la commande qui servira à alimenter les données de la courbe}}</label>
								<label class="col-lg-12 control-label pull-left">{{Unité : permet d'ajouter une unité si la commande n'en dispose pas ou de transformer l'unité en fonction du coefficient (passage des W en kW par exemple)}}</label>
								<label class="col-lg-12 control-label pull-left">{{Coefficient: permet d'appliquer un coefficient aux valeurs de la commande pour passer d'une unité à une autre. Des coefficients négatifs peuvent être appliqués}}</label>
								<label class="col-lg-12 control-label pull-left">{{mini: valeur minimale des données à afficher. Attention, cette valeur s'applique à TOUTES les courbes ayant la même unité. 
																					Si plusieurs valeurs sont saisies pour la même unité alors c'est la plus petite qui est conservée}}</label>
								<label class="col-lg-12 control-label pull-left">{{maxi: valeur maximale des données à afficher. Attention, cette valeur s'applique à TOUTES les courbes ayant la même unité. 
																					Si plusieurs valeurs sont saisies pour la même unité alors c'est la plus grande qui est conservée}}</label>
								<label class="col-lg-12 control-label pull-left">{{Seuil: permet de définir un seuil d'attention (ligne horizontale fixe, aura la même couleur que la courbe)}}</label>								
							</div>
						</div>						
					</fieldset>
				</form>
			</div>
			<?php } ?>

		</div><!-- /.tab-content -->
	</div><!-- /.eqLogic -->


	<!-- Modale d'aide pour le navigator / scrollbar / boutons -->
	<div class="modal fade" id="md_navigatorHelp" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">
						<i class="fas fa-info-circle"></i> {{À quoi correspondent ces options de navigation ?}}
					</h4>
				</div>
				<div class="modal-body">
					<div style="display:flex; flex-wrap:wrap; gap:20px; justify-content:center; align-items:flex-start;">
						<!-- Navigator -->
						<div style="text-align:center; flex:1; min-width:250px;">
							<p style="margin:5px 0;"><strong>{{Barre de navigation (navigator)}}</strong></p>
							<img src="plugins/jeeHistoGraph/desktop/images/navigator.jpg" 
								style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
							<small style="color:#666; display:block; margin-top:5px;">
								{{Vue miniature du graphique complet – permet de zoomer en glissant les poignées grises}}
							</small>
						</div>

						<!-- Scrollbar -->
						<div style="text-align:center; flex:1; min-width:250px;">
							<p style="margin:5px 0;"><strong>{{Barre de défilement}}</strong></p>
							<img src="plugins/jeeHistoGraph/desktop/images/scrollbar.jpg" 
								style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
							<small style="color:#666; display:block; margin-top:5px;">
								{{Permet de se déplacer horizontalement dans le temps}}
							</small>
						</div>

						<!-- Boutons -->
						<div style="text-align:center; flex:1; min-width:250px;">
							<p style="margin:5px 0;"><strong>{{Boutons de période}}</strong></p>
							<img src="plugins/jeeHistoGraph/desktop/images/range_buttons.jpg" 
								style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
							<small style="color:#666; display:block; margin-top:5px;">
								{{Permet de passer rapidement à une période prédéfinie (1j, 1s, 1m…)}}
							</small>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{Fermer}}</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modale d'aide pour le référence précédente -->
	<div class="modal fade" id="md_refPrecHelp" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">
						<i class="fas fa-info-circle"></i> {{Si coché alors va apparaitre dans l'infobulle: 'valeur précédente' → 'valeur du point' + date sinon uniquement la date}}
					</h4>
				</div>
				<div class="modal-body">
					<div style="display:flex; flex-wrap:wrap; gap:20px; justify-content:center; align-items:flex-start;">
						<!-- Checked -->
						<div style="text-align:center; flex:1; min-width:250px;">
							<p style="margin:5px 0;"><strong>{{Exemple, si coché :}}</strong></p>
							<img src="plugins/jeeHistoGraph/desktop/images/refPrecChecked.png" 
								style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
						</div>

						<!-- Unchecked -->
						<div style="text-align:center; flex:1; min-width:250px;">
							<p style="margin:5px 0;"><strong>{{Exemple, si décoché :}}</strong></p>
							<img src="plugins/jeeHistoGraph/desktop/images/refPrecUnChecked.png" 
								style="max-width:100%; height:auto; border:2px solid #ccc; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.1);" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">{{Fermer}}</button>
				</div>
			</div>
		</div>
	</div>

</div><!-- /.row row-overflow -->


<script>
document.querySelector('.nbgraphs').addEventListener('change', function() {
    const nb = parseInt(this.value);
    document.querySelectorAll('.graph-tab-li').forEach((el, i) => {
        el.style.display = (i + 1 <= nb) ? '' : 'none';
    });
    document.querySelectorAll('[id^="graph"][id$="tab"]').forEach((el, i) => {
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
            if (input) {
                input.value = color;
                input.dispatchEvent(new Event('change'));
            }
        });

        const defaultColorCheckbox = document.querySelector(
            `[data-l2key="graph${graph}_default_color"]`
        );

		const $colors = $(`.graphConfig[data-graph="${graph}"] input[type=color][data-l2key*="color"]`);
		
		$colors.prop('disabled', this.checked)
			.css('opacity', this.checked ? '0.45' : '1')
			.css('cursor', this.checked ? 'not-allowed' : 'pointer');
        
        if (defaultColorCheckbox) {
            defaultColorCheckbox.checked = false;
            defaultColorCheckbox.dispatchEvent(new Event('change'));
        }
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
    const $colorInput = $checkbox.closest('.form-group').find('[data-l2key="graph' + graphNum + '_bg_couleur"]').closest('.bgColorInput');
    
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
        const $colorInput = $(this).find('[data-l2key="graph' + graphNum + '_bg_couleur"]').closest('.bgColorInput');
        
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

// modification des périodes histo
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

// affichage nbPointsTimeLine si timeLine sélectionnée
$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=graph1_type]', function () {
    var valeur = $(this).value();

	console.log("Changement de type de graphique : " + valeur);
    // On cache tout d'abord
    $('.graph1_nbPointsTimeLine').hide();

    // Puis on affiche uniquement la bonne section
    if (valeur === 'timeline') {
        $('.graph1_nbPointsTimeLine').show();
    }
});

$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=graph2_type]', function () {
    var valeur = $(this).value();

	console.log("Changement de type de graphique : " + valeur);
    // On cache tout d'abord
    $('.graph2_nbPointsTimeLine').hide();

    // Puis on affiche uniquement la bonne section
    if (valeur === 'timeline') {
        $('.graph2_nbPointsTimeLine').show();
    }
});

$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=graph3_type]', function () {
    var valeur = $(this).value();

	console.log("Changement de type de graphique : " + valeur);
    // On cache tout d'abord
    $('.graph3_nbPointsTimeLine').hide();

    // Puis on affiche uniquement la bonne section
    if (valeur === 'timeline') {
        $('.graph3_nbPointsTimeLine').show();
    }
});

$(document).on('change', '.eqLogicAttr[data-l1key=configuration][data-l2key=graph4_type]', function () {
    var valeur = $(this).value();

	console.log("Changement de type de graphique : " + valeur);
    // On cache tout d'abord
    $('.graph4_nbPointsTimeLine').hide();

    // Puis on affiche uniquement la bonne section
    if (valeur === 'timeline') {
        $('.graph4_nbPointsTimeLine').show();
    }
});

/* Au chargement de la page (ou quand on ouvre la config d'un équipement existant) */
$('.eqLogicAttr[data-l1key=configuration][data-l2key=periode_histo]').trigger('change');


// Fonction qui gère l'affichage pour un graphique donné
function updatePeriodeVisibility(graphNumber) {
	var selectVal = $('.eqLogicAttr[data-l2key="periode_histo_graph' + graphNumber + '"]').value();

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

// Gestion de l'affichage des options 3D lorsqu'on coche/décoche "Graphique 3D"
$(document).on('change', '.bg3dCheckbox', function() {
    const $checkbox = $(this);
    const graphNum = $checkbox.data('l2key').match(/graph(\d+)_3D_enabled/)[1];

    // On récupère tous les éléments qui ont la classe 3dGraph + numéro du graphique
    const $options3d = $('.3dGraph' + graphNum);

    if ($checkbox.is(':checked')) {
        $options3d.show();
    } else {
        $options3d.hide();
    }
});

// Au chargement de la page : on applique l'état actuel de chaque checkbox 3D
$(function() {
    $('.bg3dCheckbox').each(function() {
        $(this).trigger('change');
    });
});

// affichage de la modale d'aide navigator
$(document).on('click', '#bt_openNavigatorHelp', function() {
	console.log("Ouverture modale aide navigator");
    $('#md_navigatorHelp').modal('show');
});

// affichage de la modale d'aide référence précédente
$(document).on('click', '#bt_openRefPrecHelp', function() {
	console.log("Ouverture modale aide référence précédente");
    $('#md_refPrecHelp').modal('show');
});

// Gestion de l'activation/désactivation des inputs couleurs selon la case "couleur par défaut"
$(document).on('change', '[data-l2key$="_default_color"]', function() {
    const g = this.dataset.l2key.match(/graph(\d+)/)?.[1];
    if (!g) return;
    
    const $colors = $(`.graphConfig[data-graph="${g}"] input[type=color][data-l2key*="color"]`);
    
    $colors.prop('disabled', this.checked)
           .css('opacity', this.checked ? '0.45' : '1')
           .css('cursor', this.checked ? 'not-allowed' : 'pointer');
});

// Appliquer au démarrage
$('[data-l2key$="_default_color"]').trigger('change');

// Gestion de l'affichage de l'option "Alterner axe Y gauche/droite" selon la case "Afficher l'axe Y"
$(document).on('change', '[data-l2key$="_show_yAxis"]', function() {
    const g = this.dataset.l2key.match(/graph(\d+)/)?.[1];
    if (!g) return;
    
    const $alternate = $(`[data-l2key="graph${g}_alternate_yAxis"]`).closest('.form-group');
    $alternate.toggle(this.checked);
});

// Appliquer au chargement
$('[data-l2key$="_show_yAxis"]').trigger('change');

// ───────────────────────────────────────────────────────────────
// Masquer barre de navigation + défilement quand Graphique 3D est activé
// ───────────────────────────────────────────────────────────────
$(document).on('change', '[data-l2key$="_3D_enabled"]', function() {
    const g = this.dataset.l2key.match(/graph(\d+)/)?.[1];
    if (!g) return;

    // Les deux lignes à masquer quand 3D est coché
    const $navigatorLine = $(`[data-l2key="graph${g}_navigator"]`).closest('.form-group');
    const $scrollbarLine  = $(`[data-l2key="graph${g}_barre"]`).closest('.form-group');

    // Si 3D activé → on cache, sinon on affiche
    const is3D = this.checked;
    $navigatorLine.toggle(!is3D);
    $scrollbarLine.toggle(!is3D);
});

// Appliquer l'état actuel au chargement
$('[data-l2key$="_3D_enabled"]').trigger('change');

</script>



<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'jeeHistoGraph', 'js', 'jeeHistoGraph'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js'); ?>