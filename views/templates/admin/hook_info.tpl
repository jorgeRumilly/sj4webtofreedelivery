<div class="panel">
    <div class="panel-heading">
        {l s='Important information' d='Modules.Sj4webtofreedelivery.Admin'}
    </div>
    <div class="panel-body">
        <div class="alert alert-info">
            <p><strong>{l s='About the custom hook: displayCartAjaxFreeShipp' d='Modules.Sj4webtofreedelivery.Admin'}</strong></p>
            <p>{l s='This hook allows dynamic display of the remaining amount before free shipping or a discount, in areas where no native PrestaShop hook is available.' d='Modules.Sj4webtofreedelivery.Admin'}</p>
            <ul class="mb-2">
                <li>{l s='In the mini cart displayed when hovering over the cart icon' d='Modules.Sj4webtofreedelivery.Admin'}</li>
                <li>{l s='In the cart summary or checkout process (cart-subtotal/cart-summary)' d='Modules.Sj4webtofreedelivery.Admin'}</li>
                <li>{l s='In any manually modified template' d='Modules.Sj4webtofreedelivery.Admin'}</li>
            </ul>
            <p>
                {l s='To use it, add this code to the corresponding .tpl file:' d='Modules.Sj4webtofreedelivery.Admin'}<br><br>
                <code>{ldelim}hook h='displayCartAjaxFreeShipp'{rdelim}</code>
            </p>
        </div>
    </div>
</div>