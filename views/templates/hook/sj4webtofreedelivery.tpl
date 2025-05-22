{*
* 2025 SJ4WEB.FR
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement
*
* @author    SJ4WEB.FR <contact@sj4web.fr>
* @copyright 2025 SJ4WEB.FR
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}

<div class="alert alert-info sj4webtofreedelivery {if $hide}hidden-xs-up{/if}" role="alert">
    <div class="sj4webtofreedelivery-title"><strong>{l s='Spend' d='Modules.Sj4webtofreedelivery.Shop'} <span class="stfd-remaining-price">{$free_ship_remaining}</span> {l s='more to get free shipping!' d='Modules.Sj4webtofreedelivery.Shop'}</strong>
        {if isset($txt) && $txt != ''}{$txt nofilter}{/if} {* HTML, cannot escape*}
    </div>
</div>