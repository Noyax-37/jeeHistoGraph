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
