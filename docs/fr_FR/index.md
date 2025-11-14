# Documentation du plugin jeeHistoGraph

## Présentation

`jeeHistoGraph` est un plugin Jeedom permettant d'afficher **jusqu'à 4 graphiques** sur un même équipement, chacun pouvant contenir **jusqu'à 10 courbes**.  
Idéal pour regrouper des données hétérogènes (températures, consommations, capteurs, etc.) dans un seul widget clair et interactif.

---

## Fonctionnalités principales

| Fonctionnalité | Description |
|----------------|-----------|
| **Multi-graphiques** | 1 à 4 graphiques indépendants |
| **Multi-courbes** | Jusqu’à 10 courbes par graphique |
| **Zoom interactif** | 30 min, 1h, 1j, 1sem, 1mois, 1an, ou tout |
| **Défilement temporel** | Curseur pour naviguer dans la période |
| **Mise à jour temps réel** | Les nouvelles valeurs s’ajoutent automatiquement |
| **Personnalisation** | Couleurs, titres, légende, empilement |
| **Responsive** | S’adapte à toutes les tailles de widget |

---

## Configuration de l’équipement

### Onglet "Équipement"

#### Paramètres généraux
- **Nom de l’équipement** : nom affiché
- **Objet parent** : emplacement dans l’arborescence
- **Catégorie** : pour le filtrage
- **Activer / Visible** : état du widget

#### Configuration des graphiques
| Champ | Description |
|------|-------------|
| **Nombre de graphique(s)** | 1 à 4 |
| **Période affichée (jours)** | Charge les X derniers jours d’historique |
| **Période de zoom par défaut** | Zoom affiché au chargement |
| **Afficher la légende** | Active/désactive la légende |
| **Empilement** | Aucun / Normal / Pourcentage (uniquement en aire) |
| **Nb max points par courbe** | 500 par défaut – à réduire si ralentissements |

---

### Configuration d’un graphique (1 à 4)

Pour chaque graphique :
1. **Titre du graphique** : affiché en haut
2. **Bouton "Couleurs par défaut"** : réinitialise les couleurs
3. **Courbes (1 à 10)** :
   - **Libellé** : **obligatoire** pour afficher la courbe
   - **Couleur** : personnalisable via le sélecteur
   - **Commande** : cliquez sur l’icône pour sélectionner une commande numérique avec historique

> **Attention** : Si le libellé est vide, la courbe **n’apparaît pas**, même si la commande est valide.

---

## Affichage sur le dashboard

Le widget affiche :
- Un **sélecteur de zoom** en haut
- Un **curseur temporel** pour naviguer dans la période
- La **plage horaire actuelle** (ex: `14/11/2025 10:30 → 14/11/2025 11:30`)
- Les **graphiques** en grille responsive :
  - 1 graphique → pleine largeur
  - 2 graphiques → empilés
  - 3 graphiques → 2 en haut, 1 en bas
  - 4 graphiques → grille 2×2

---

## Conseils d’optimisation

- **Réduisez `Nb max points par courbe`** si le widget est lent
- **Limitez la période affichée** si vous avez beaucoup de données
- **Utilisez des commandes avec historique activé**
- **Évitez les commandes à très haute fréquence** sur de longues périodes

---

## Support

- **Forum Jeedom** : [Recherche "jeeHistoGraph"](https://community.jeedom.com)
- **GitHub** : [github.com/votre-nom/jeeHistoGraph](https://github.com/votre-nom/jeeHistoGraph)

---

**Plugin développé pour la communauté Jeedom**