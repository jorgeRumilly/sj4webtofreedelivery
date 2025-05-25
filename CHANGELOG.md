# Changelog – sj4webtofreedelivery

## [1.0.0] – Version initiale stable

### Ajouts
- Affichage du montant restant pour bénéficier de la **livraison gratuite** ou d'une **remise**
- Paramétrage complet depuis le back-office :
    - Seuils personnalisables pour la livraison et la remise
    - Choix du type de remise (montant fixe ou pourcentage)
    - Montant minimum optionnel pour afficher la remise
    - Messages complémentaires personnalisables
    - Couleurs personnalisables (fond, texte, sous-titre)
- Exclusion de certaines catégories de produits
- Système de hooks activables :
    - `displayCartAjaxFreeShipp` *(hook personnalisé pour mini-panier et blocs sans hook natif)*
    - `displayCartModalContent`
    - `displayRightColumn`
    - `displayReassurance`
- Utilisation du nouveau système de traduction PrestaShop (`trans()`)

### Techniques
- Nécessite PHP ≥ 7.4 (compatible avec PrestaShop 1.7.8 à 8.x)
- Rendu conditionnel selon les règles panier (pays, produits, catégories, remises existantes)
- Code modulaire et évolutif