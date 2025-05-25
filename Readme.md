# sj4webtofreedelivery

**SJ4WEB – Amount left to unlock free shipping or discount**

Ce module PrestaShop affiche un message dynamique indiquant au client combien il lui reste à dépenser pour bénéficier :
- soit de la **livraison gratuite**,
- soit d’une **remise (montant ou pourcentage)** selon les seuils configurés.

## Fonctionnalités

- Affichage conditionnel selon le montant du panier HT (hors frais de port)
- Seuils configurables pour :
  - Livraison gratuite
  - Remise sur montant du panier (fixe ou pourcentage)
  - **Affichage de la remise à partir d’un montant minimum optionnel**
- Exclusion possible de certaines catégories de produits
- Affichage du message sur plusieurs hooks :
  - `displayCartAjaxFreeShipp` *(hook personnalisé)*
  - `displayCartModalContent`
  - `displayRightColumn`
  - `displayReassurance`
- Couleurs personnalisables (fond, texte, sous-titre)
- Message complémentaire pour chaque type d’avantage
- Affichage intelligent selon le pays de livraison (ex : uniquement France)
- Compatible PrestaShop 1.7.4 à 8.x
- Utilise le nouveau système de traduction (`trans()`)

## Utilisation du hook personnalisé `displayCartAjaxFreeShipp`

Le hook `displayCartAjaxFreeShipp` est fourni par le module pour permettre l'affichage du message dans des zones non couvertes par les hooks natifs de PrestaShop, comme :
- Le mini-panier affiché au survol de l’icône panier (souvent géré par le thème)
- Le récapitulatif du panier (`cart-subtotal`, `cart-summary`) dans le tunnel de commande
- N’importe quel template personnalisé où l’on souhaite injecter dynamiquement le message

### Intégration manuelle :
Ajoutez ce code dans le fichier `.tpl` souhaité :
```smarty
{hook h='displayCartAjaxFreeShipp'}
```

## Configuration

Dans le back-office du module, vous pouvez :

- Activer ou désactiver chaque seuil (livraison / remise)
- Définir le montant à atteindre
- Définir le type de remise (montant ou pourcentage)
- Définir un montant minimum à partir duquel le message de remise peut s’afficher
- Exclure des catégories
- Choisir les hooks d’affichage
- Personnaliser les couleurs d’affichage
- Ajouter des messages informatifs complémentaires

## Exemple de rendu

> "Plus que 18,50 € avant de bénéficier de la livraison gratuite."  
> "Plus que 22 € avant de bénéficier de 5% sur votre commande."

## Installation
> ℹ️ **Prérequis** : PHP **≥ 7.4**  
> Compatible avec PrestaShop **1.7.8 à 8.x**

1. Téléversez le dossier du module `sj4webtofreedelivery` dans le répertoire `/modules` de votre installation PrestaShop.
2. Connectez-vous au back-office de PrestaShop.
3. Accédez à **Modules > Module Manager**.
4. Recherchez **SJ4WEB – Amount left to unlock free shipping or discount**.
5. Cliquez sur **Installer**.
6. Configurez les options selon vos besoins.

## Traduction

Le module est compatible avec le **système moderne de traduction de PrestaShop**.  
Par défaut, le nom du module et ses textes sont affichés en anglais.

Pour traduire les libellés du front-office ou du back-office :

1. Accédez à **International > Traductions**
2. Choisissez :
  - Type de traduction : *Modules installés*
  - Sélectionnez le module : *sj4webtofreedelivery*
  - Choisissez la langue : *Français (ou autre)*
3. Traduisez les chaînes selon vos besoins.

## Auteur

Développé par **SJ4WEB.FR**