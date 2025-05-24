# sj4webtofreedelivery

**SJ4WEB – Montant restant pour livraison gratuite ou remise**

Ce module PrestaShop affiche un message dynamique indiquant au client combien il lui reste à dépenser pour bénéficier :
- soit de la **livraison gratuite**,
- soit d’une **remise (montant ou pourcentage)** selon les seuils configurés.

## Fonctionnalités

- Affichage conditionnel selon le montant du panier HT (hors frais de port)
- Seuils configurables pour :
    - Livraison gratuite
    - Remise sur montant du panier (fixe ou pourcentage)
- Exclusion possible de certaines catégories de produits
- Affichage du message sur plusieurs hooks :
    - `displayCartAjaxFreeShipp`
    - `displayCartModalContent`
    - `displayRightColumn`
    - `displayReassurance`
- Couleurs personnalisables (fond, texte, sous-titre)
- Message complémentaire pour chaque type d’avantage
- Affichage intelligent selon le pays de livraison (ex : uniquement France)
- Compatible PrestaShop 1.7.4 à 8.x

## Configuration

Dans le back-office du module, vous pouvez :

- Activer ou désactiver chaque seuil (livraison / remise)
- Définir le montant à atteindre
- Définir le type de remise (montant ou pourcentage)
- Exclure des catégories
- Choisir les hooks d’affichage
- Personnaliser les couleurs d’affichage
- Ajouter des messages informatifs complémentaires

## Exemple de rendu

> "Plus que 18,50 € avant de bénéficier de la livraison gratuite."
> "Plus que 22 € avant de bénéficier de 5% sur votre commande."

## Auteur

Développé par **SJ4WEB.FR**