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

<div class="alert alert-info sj4webtofreedelivery" role="alert" style="background-color: {$color_bg|escape:'html'}; border-color: {$color_bg|escape:'html'}; color: {$color_text|escape:'html'};">
    {if $free_ship_remaining}
        <div class="sj4webtofreedelivery-title">
            <strong>{$free_ship_message nofilter}</strong>
            {if isset($txt) && $txt != ''}
                <div class="sj4webtofreedelivery-title" style="color: {$color_subtitle|escape:'html'};">
                    <small class="text-muted">{$txt nofilter}</small>
                </div>
            {/if}
        </div>
    {/if}

    {if $discount_message}
        <div class="sj4webtofreedelivery-discount">
            <strong>{$discount_message nofilter}</strong>
            {if isset($txt) && $txt != ''}
                <div class="sj4webtofreedelivery-title" style="color: {$color_subtitle|escape:'html'};">
                    <small class="text-muted">{$txt nofilter}</small>
                </div>
            {/if}
        </div>
    {/if}
</div>
