# jeeHistoGraph - Graphiques multi-courbes personnalisables pour Jeedom

*Plugin Jeedom permettant d'afficher jusqu'à **4 graphiques indépendants**, chacun avec **jusqu'à 10 courbes**, avec zoom, déplacement temporel et mise à jour en temps réel.*

---

## Fonctionnalités

- **4 graphiques** maximum par équipement
- **10 courbes** maximum par graphique
- **Jusqu'à 40 courbes** au total
- **Zoom prédéfini** : 30 min, 1h, 1j, 1sem, 1mois, 1an, ou tout
- **Défilement temporel** avec curseur
- **Mise à jour en temps réel** des courbes
- **Couleurs personnalisables** + bouton de réinitialisation
- **Titre par graphique**
- **Légende activable/désactivable**
- **Empilement** (normal ou pourcentage) pour les graphiques en aire
- **Limitation du nombre de points** (évite les ralentissements)
- **Responsive** : s’adapte à la taille du widget

---

## Captures d’écran

> *(À venir)*

---

## Installation

1. Téléchargez le plugin via le **Market Jeedom**

Activez le plugin dans Jeedom → Plugins → Gestion des plugins
Cliquez sur "Ajouter" pour créer un nouvel équipement


Configuration
1. Paramètres généraux

Nombre de graphiques : 1 à 4
Période affichée (jours) : données chargées (ex: 7 jours)
Zoom par défaut : période initiale affichée
Afficher la légende : oui/non
Empilement : aucun, normal, pourcentage (uniquement en aire)
Nb max points par courbe : 500 par défaut (ajustez pour les performances)

2. Configuration des graphiques
Pour chaque graphique (1 à 4) :

Titre du graphique
10 courbes possibles :
Libellé : obligatoire pour afficher la courbe
Couleur : personnalisable (clic sur le sélecteur)
Commande : sélectionnez une commande numérique avec historique

Bouton "Couleurs par défaut" : réinitialise les 10 couleurs du graphique

Note : Une courbe sans libellé ne s’affiche pas, même si la commande est valide.

Affichage sur le dashboard

Le widget s’adapte automatiquement à la taille
Contrôles en haut :
Sélecteur de zoom
Curseur temporel (défilement dans la période)
Plage horaire affichée



Performances & Bonnes pratiques

Limitez le nombre de points si vous avez plus de 10 courbes ou une longue période
Utilisez des commandes avec historique activé
Évitez les commandes à haute fréquence (>1 point/seconde) sur de longues périodes


Support & Contribution

Forum Jeedom : Recherche "jeeHistoGraph"
GitHub Issues : [github.com](https://github.com/Noyax-37/jeeHistoGraph/issues)


[Changelog]/docs/fr_FR/changelog.md


Version initiale
4 graphiques × 10 courbes
Zoom + déplacement temporel
Mise à jour en temps réel
Couleurs personnalisables
