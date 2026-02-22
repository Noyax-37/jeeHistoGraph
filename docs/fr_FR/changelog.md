# Changelog plugin jeeHistoGraph

**IMPORTANT**

S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.
Lien vers: 
- Forum Jeedom : https://community.jeedom.com/tag/plugin-jeehistograph
- Issues GitHub : https://github.com/Noyax-37/jeeHistoGraph/issues
- changelog : https://github.com/Noyax-37/jeeHistoGraph/blob/main/docs/fr_FR/changelog.md
- documentation : https://github.com/Noyax-37/jeeHistoGraph/blob/main/docs/fr_FR/index.md
- changelog_beta : https://github.com/Noyax-37/jeeHistoGraph/blob/develop/docs/fr_FR/changelog.md
- documentation_beta : https://github.com/Noyax-37/jeeHistoGraph/blob/develop/docs/fr_FR/index.md

Si vous souhaitez me payer un café par Paypal: [Offrir un café](https://www.paypal.com/donate/?hosted_button_id=JD64LAEUMUWMU)

# v2.22
- ajut de la possibilité de ne pas permettre le refresh auto introduit dans la v2.21

# v2.21
- correction d'un bug concernant les graphiques de type timeline
- ajout des ériodes de regroupement 'par 5 minutes', 'par 15 minutes' et 'par 30 minutes' dans 'affichage des données'
- ajout d'un option 'mois en cours' pour les graphiques de comparaison temporelle
- correction d'une erreur qui empêchait la duplication d'équipement
- refresh automatique lorsque l'on revient sur un graph sans recharger la page (onglet non visible, graph non visible sur la page, ...) si l'option d'actualisation est activée

# v2.20
- meilleure gestion des mises à jour automatiques des graphiques
- modification pour modifier l'ordre des séries => maintenant c'est du glisser / déposer à la souris
- correction du bug du naigator qui rétrécissait lors des mises à jour automatiques des graphiques avec maintien de la période d'affichage (en v2.12)

# v2.12
- gestion des mises à jour automatiques des graphiques améliorée, reste un problème avec le 'navigator' qui rétrécit lorsque les données sont mises à jour avec maintient de la période d'affichage 

# v2.11
- correction de l'update des courbeslorsqu'on change l'ordre d'affichage
- correction pour l'affichage des crosshairs entre graphiques
- ajout de la possibilité de bloquer les updates automatiques des graphiques (les graphiques avec une date de fin définie ne se mettent pas à jour automatiquement, cette option permet de bloquer les updates pour les graphiques sans date de fin)
- ajout d'une option pour garder ou pousser dehors les premières données lors d'un update
- ajout d'options supplémentaires pour le range selector (dernières 30 secondes, 1 minute, 5 minutes, 15 minutes)

# v2.10
- ajout de l'ordre d'affichage des courbes dans le tableau de configuration
- meilleur placement de la liste de zoom et du bouton "reset zoom" 
- ajout de nouvelles options de période prédéfinies pour le zoom rapide (dernières 5 min, 15 min, 30 min, 1h, 6h, 12h)

# v2.09
- corection défaut d'affichage des valeurs identiques en update
- ajout de l'option "simples points" pour le type de courbe

# v2.08
- corection erreur affichage de certaines valeurs en update
- correction erreur d'affichage des dates pour certaines configurations de la timeline

# v2.07
- correction d'une erreur de saisie qui empéchait les graph de type timeline de s'afficher
- correction pour l'heure des graphiques de comparaison temporelle (oubli lors du passage en V2.04)
- meilleure gestion des fins de courbes sans données
- corrections de plusieurs problèmes de dates

# v2.06
- meilleure gestion des fonds en couleur pour les graphiques 3D
- réorganisation du code, plus lisible pour moi mais invisible pour l'utilisateur

# v2.05
- ajout de l'option d'affichage ou non de la courbe d'un graph et non plus dépendant de l'attibution d'un libellé
- ajout de maxi / mini par unité (valeurs communes à toutes les courbes d'une même unité sur un même graphique)
- ajout d'une option de seuil d'attention (ligne horizontale fixe) par courbe (même couleur que la courbe)
- couleur par défaut de highchart définies avec les couleurs actuelles (verion non à jour dans jeedom)
- possibilité de reporter les crosshairs entre graphiques, il faut au moins sélectionner 2 graphiques affichés pour que l'option soit active

# v2.04
- correction d'un bug d'affichage des dates

# v2.03
 - correction du refresh widget qui ne fonctionnait plus
 
# v2.02
- ajout d'une option d'alignement du titre du graphique (gauche, centre, droite)
- suppression des options de disposition "2 colonnes" et "2 lignes" (inutiles, déjà pris en compte dans les autres options)
- permettre d'alterner le positionnement des axes Y (gauche/droite) en fonction de l'ordre d'apparition des unités
- possibilité de choisir soit les couleurs par défaut d'Highcharts soit des couleurs personnalisées pour chaque courbe (option par graphique)

# v2.01
- création auto de la commande 'refresh' (oubli de ma part)
- correction d'un bug d'affichage des boutons du range selector lorsque le titre du graphique n'était pas affiché
- ajout des graphiques 3D
- ajout des options de zoom sur les axes X et Y (indépendamment)

# v2.00
- Descente de certaines options du niveau graphique vers le niveau courbe: emplilement (stacking), affichage des données avec le mode de calcul associé.
- réorganisation de l'ordre des colonnes du tableau des courbes
<br>
=> Si vous aviez utilisé des options qui sont descendues de niveau il faudra reprendre la configuration de l'équipement

# v1.20
- ajout option « variation » pour l’affichage de la variation d’une valeur dans le temps. 
ATTENTION: la variation est calculée pour TOUS les points d’une commande, pour avoir la variation de cette commande sur un intervalle déterminé par « regroupement » alors il faut donc demander à faire la somme de ces variations. Par exemple « Affichage des données = par heure » + « Valeur à afficher : somme » + V coché => variation par heure
- correction d’un bug lorsque les unités étaient vides
- modifications dans la page de configuration

# v1.10
- modification du positionnement des onglets
- quelques modifications esthétiques

# v1.01

- mise à jour de la documentation
- mise à jour du readme
- correction de la mise à jour auto des courbes qui posaient qq soucis dans certains cas
- correction de la mise à jour des courbes de type timeLine
- réorganisation très légère de la page de configuration d'un équipement
- ajout d'une option pour ne pas afficher l'infobulle

# v0.16
... corrections mineures et améliorations diverses

# V0.6
* plugin proposé pour le market Jeedom
