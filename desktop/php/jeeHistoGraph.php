<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('jeeHistoGraph');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
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

							<legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>

							<div class="form-group Colors">
								<label class="col-sm-2 control-label">{{}}</label>
								<label class="col-sm-2 control-label">{{}}</label>
								<a class="btn btn-warning tooltips col-sm-2"  id="btTeleinfoRazCouleurs"><i class="fas fa-medkit"></i>{{ Couleurs par défaut}}</a>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">{{Courbe}}</label>
								<label class="col-sm-2 control-label pull-left">{{Libellé affiché}}</label>
								<label class="col-sm-1 control-label pull-left">{{Couleur}}</label>
								<label class="col-sm-6 control-label pull-left">{{Commande à afficher}}</label>
							</div>

							<?php
								//création du tableau des paramètres des index
								$index=array('01','02','03','04','05','06','07','08','09','10');
								$color = 0;
								$tableau = '';
								foreach($index as $numindex){
									$color += 1;
									$tableau.='<div class="form-group">';
										$tableau.='<label class="col-sm-2 control-label">{{Courbe '.$numindex.'}} :</label>';
										$tableau.='<div class="col-sm-2">';
											$tableau.='<input type="text" class="eqLogicAttr configKey" data-l1key="configuration" data-l2key="index'.$numindex.'_nom" placeholder="..."/>';
										$tableau.='</div>';
										$tableau.='<div class="col-sm-1">';
											$tableau.='<input type="color" class="eqLogicAttr configKey" id="favcolor'.$color.'"  data-l1key="configuration" data-l2key="color'.$color.'" name="favcolor'.$color.'">';
										$tableau.='</div>';
										$tableau.='<div class="col-sm-6 input-group">';
											$tableau.='<input class="eqLogicAttr form-control input-sm" data-l1key="configuration" data-l2key="cmdGraphe'.$numindex.'"></input>';
											$tableau.='<a class="btn btn-default listEquipementInfo cursor btn-sm input-group-addon" data-input="cmdGraphe'.$numindex.'"><i class="fas fa-list-alt"></i></a>';
										$tableau.='</div>';
									$tableau.='</div>';
								}
							?>
							<?php echo $tableau ?>

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

//création du tableau des paramètres des couleurs par défaut
$colordefaut = ['#FF4500','#00FF7F','#1E90FF','#FFD700','#FF69B4',
                    '#00CED1','#ADFF2F','#FF1493','#00BFFF','#FFA500'];

$('#btTeleinfoRazCouleurs').on('click', function () {
    $('#favcolor1').value($colordefaut[0]);
    $('#favcolor2').value($colordefaut[1]);
    $('#favcolor3').value($colordefaut[2]);
    $('#favcolor4').value($colordefaut[3]);
    $('#favcolor5').value($colordefaut[4]);
    $('#favcolor6').value($colordefaut[5]);
    $('#favcolor7').value($colordefaut[6]);
    $('#favcolor8').value($colordefaut[7]);
    $('#favcolor9').value($colordefaut[8]);
    $('#favcolor10').value($colordefaut[9]);
});

//$('#favcolor14').value('#00FF00');
//    document.getElementById("favcolor14").value = "#00FF00";
//    $('.eqLogicAttr[data-l1key=configuration][data-l2key=color14]').value("#00FF00");
//eqLogicAttr configKey" id="favcolor14"  data-l1key="configuration" data-l2key="color14


</script>


<!-- Inclusion du fichier javascript du plugin (dossier, nom_du_fichier, extension_du_fichier, id_du_plugin) -->
<?php include_file('desktop', 'jeeHistoGraph', 'js', 'jeeHistoGraph'); ?>
<!-- Inclusion du fichier javascript du core - NE PAS MODIFIER NI SUPPRIMER -->
<?php include_file('core', 'plugin.template', 'js'); ?>
