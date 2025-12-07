# jeeHistoGraph - Graphiques historiques avancés pour Jeedom

**jeeHistoGraph** est un plugin Jeedom permettant d'afficher jusqu’à **4 graphiques indépendants** sur un seul équipement, avec **jusqu’à 10 courbes par graphique**.  
Il est basé sur **Highcharts Stock** et offre une configuration dans l’interface Jeedom.

Idéal pour :
- Suivi de consommation/production solaire
- Comparaison d’historiques (années précédentes, même mois, etc.)
- Affichage multi-graphiques sur une seule tuile

![](https://raw.githubusercontent.com/ton-pseudo/jeeHistoGraph/main/resources/screenshot_dashboard.png)
*(exemple d’affichage avec 4 graphiques en grille 2×2)*

---

### Fonctionnalités principales

- Jusqu’à **4 graphiques** sur un même équipement
- Jusqu’à **10 courbes** par graphique
- Choix du **type de courbe** par graphique ou par courbe (ligne, spline, aire, colonne…)
- **Empilement** (stacking) normal ou en pourcentage
- **Comparaison temporelle** :
  - Même mois des années précédentes
  - Toutes les années précédentes (avec rolling year)
- **Regroupement des données** (moyenne, somme, min, max) par minute/heure/jour/semaine/mois/année
- Période d’affichage configurable **globalement ou par graphique** :
  - Derniers X jours
  - Depuis une date
  - Entre deux dates
  - Aujourd’hui / Cette semaine / Ce mois / Cette année / Toutes les données
- **Fond personnalisable** pour chaque graphique : transparent, couleur unie ou dégradé
- **Disposition des graphiques** flexible (auto, 1 ligne, 1 colonne, 2×2, 1 grand + 2 petits, etc.)
- Barre de navigation, scrollbar et boutons de période (comme dans les graphiques Jeedom natifs)
- Mise à jour en temps réel des courbes (quand les commandes sont actualisées)
- Compatible dashboard, vues, mobile

---

### Captures d’écran

| Configuration | Dashboard |
|---------------|---------|
| ![Configuration](https://raw.githubusercontent.com/ton-pseudo/jeeHistoGraph/main/resources/screenshot_config.png) | ![Dashboard](https://raw.githubusercontent.com/ton-pseudo/jeeHistoGraph/main/resources/screenshot_dashboard.png) |

---

### Installation

1. Depuis le Market Jeedom ou via GitHub
2. Activer le plugin
3. Créer un équipement **jeeHistoGraph**
4. Configurer vos courbes et graphiques
5. Définir l'affichage sur un dashboard

---

### Support & Contributions

- Forum Jeedom : [lien à venir]
- Issues GitHub : https://github.com/Noyax-37/jeeHistoGraph/issues

Contributions bienvenues (améliorations, traductions, correctifs) !

Merci plus particulièrement à @Franck_jeedom et @jpty pour leurs retours et suggestions.
---

### Licence

**GPL v3** – Comme Jeedom

> Développé pour la communauté Jeedom