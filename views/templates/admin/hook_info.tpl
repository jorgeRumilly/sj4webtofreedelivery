<div class="panel">
    <div class="panel-heading">
        {l s='Information importante' d='Modules.Sj4webtofreedelivery.Shop'}
    </div>
    <div class="panel-body">
        <div class="alert alert-info">
            <p><strong>{l s='À propos du hook personnalisé : displayCartAjaxFreeShipp' d='Modules.Sj4webtofreedelivery.Admin'}</strong></p>
            <p>{l s='Ce hook permet d’afficher dynamiquement le montant restant avant livraison gratuite ou remise dans les zones où aucun hook natif PrestaShop n’est disponible.' d='Modules.Sj4webtofreedelivery.Admin'}</p>
            <ul class="mb-2">
                <li>{l s='Dans le mini-panier affiché au survol de l’icône panier' d='Modules.Sj4webtofreedelivery.Admin'}</li>
                <li>{l s='Dans le récapitulatif du panier ou le tunnel de commande (cart-subtotal/cart-summary)' d='Modules.Sj4webtofreedelivery.Admin'}</li>
                <li>{l s='Dans n’importe quel template modifié manuellement' d='Modules.Sj4webtofreedelivery.Admin'}</li>
            </ul>
            <p>
                {l s='Pour l’utiliser, ajoutez ce code dans le fichier .tpl correspondant :' d='Modules.Sj4webtofreedelivery.Admin'}<br><br>
                <code>{ldelim}hook h='displayCartAjaxFreeShipp'{rdelim}</code>
            </p>
        </div>
    </div>
</div>