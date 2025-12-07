# Documentation du plugin jeeHistoGraph

## Présentation

Le plugin **jeeHistoGraph** permet de créer des graphiques historiques très personnalisés directement dans Jeedom, sans passer par les scénarios ou les widgets complexes.

Un seul équipement peut afficher **jusqu’à 4 graphiques**, chacun contenant **jusqu’à 10 courbes**, avec une configuration fine de l’apparence, du comportement et de la période affichée.

---

## Création d’un équipement

1. Allez dans **Plugins → Monitoring → jeeHistoGraph**
2. Cliquez sur **Ajouter**
3. Donnez un nom à votre équipement (ex: "Conso Maison", "Production Solaire", etc.)

---

## Configuration générale

### Nombre de graphiques
Choisissez entre 1 et 4 graphiques à afficher.

### Disposition des graphiques
- **Automatique** : adapte selon le nombre
- **1 colonne** / **1 ligne** / **2×2** / **2 colonnes** / **2 lignes**
- Dispositions spéciales :
  - `1 grand en haut + 2 petits en bas`
  - `2 petits en haut + 1 grand en bas`
  - `3 graphiques → 2 en haut, 1 centré en bas`

### Période d’affichage globale
Définit la période par défaut pour tous les graphiques (peut être surchargée par graphique) :
- Nombre de jours (avec rafraîchissement automatique)
- À partir d’une date précise
- Entre deux dates
- Aujourd’hui / Cette semaine / Ce mois / Cette année / Toutes les données

---

## Configuration par graphique

Chaque graphique (1 à 4) possède ses propres paramètres :

### Titre et affichage
- Titre personnalisé
- Afficher/masquer le titre et la légende

### Type de courbe par défaut
- Ligne, courbe lisse, aire, colonne, etc.
- Bouton : **"Tout forcer au même type"**

### Empilement (stacking)
- Aucun / Normal / Pourcentage (utile pour les aires ou colonnes)

### Fond du graphique
- Transparent (par défaut)
- Couleur unie
- Dégradé linéaire (couleur début/fin + angle)

### Période spécifique
Possibilité de surcharger la période globale pour ce graphique uniquement.

### Comparaison temporelle
- Aucune
- Même mois des années précédentes
- Années précédentes (avec année glissante si mois de début défini)

### Regroupement des données
Permet d’afficher des moyennes/sommes sur de longues périodes :
- Par minute, heure, jour, semaine, mois, année
- Type : moyenne, somme, min, max

### Navigation
- Barre de navigation (navigator)
- Barre de défilement (scrollbar)
- Boutons de période (30min, 1h, 1j, 1sem, 1mois, 1an, Tout)

---

## Configuration des courbes (10 par graphique)

Pour chaque courbe :

| Champ               | Description |
|---------------------|-----------|
| Libellé             | Si vide → la courbe n’est **pas affichée** |
| Type de courbe      | Hérite du graphique ou forcé (ligne, aire, colonne…) |
| Couleur             | Choix libre |
| Commande            | Sélectionner une commande historisée (info numérique) |
| Unité               | Forcer une unité (ex: kWh au lieu de Wh) |
| Coefficient         | Multiplier la valeur (ex: ×0.001 pour passer de Wh → kWh) |

> Astuce : Bouton **"Remettre les couleurs par défaut"** par graphique

---

## Conseils d’utilisation

- Pour une conso/production solaire : utilisez 4 graphiques (réseau, PV, batterie, conso)
- Si besoin d'inverser les valeurs (négatives / positives) : utilisez un coefficient négatif (ex: -1)
- Pour comparer les années : activez la comparaison "années précédentes"
- Pour éviter les ralentissements sur de longues périodes : activez le **regroupement par jour/mois**

---

## Mise à jour en temps réel

Les courbes qui ne comportent pas de date de fin se mettent à jour automatiquement dès qu’une commande sélectionnée reçoit une nouvelle valeur (ex: teleinfo, onduleur, etc.).

---

## Dépannage

- Le graphique reste vide ? → Vérifiez que le **libellé de la courbe** est renseigné
- Trop de points → Activez un **regroupement** ou limitez la période
- Fond blanc bizarre ? → Cochez "Fond transparent" (recommandé sur thème sombre)

---

Plugin développé par @Noyax-37  
Merci tout particulièrement à @Franck_jeedom et @jpty pour leurs retours et suggestions.

Toute la communauté Jeedom vous remercie !