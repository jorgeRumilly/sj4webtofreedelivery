<?php
/**
 * Script de migration vers la version 2.0.0
 *
 * Migration des anciennes configurations v1.0.0 vers le nouveau système multi-paliers
 *
 * @author SJ4WEB.FR
 * @version 2.0.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Fonction exécutée lors de l'upgrade vers la version 2.0.0
 *
 * @param Module $module Instance du module
 * @return bool True si succès, false sinon
 */
function upgrade_module_2_0_0($module)
{
    // ===== ÉTAPE 1 : Sauvegarder les anciennes valeurs qu'on veut conserver =====
    $keepColors = [
        'bg' => Configuration::get('SJ4WEB_COLOR_BG'),
        'text' => Configuration::get('SJ4WEB_COLOR_TEXT'),
        'subtitle' => Configuration::get('SJ4WEB_COLOR_SUBTITLE'),
    ];

    $keepHooks = [
        'reassurance' => Configuration::get('SJ4WEB_HOOK_REASSURANCE_ENABLED'),
        'cartmodal' => Configuration::get('SJ4WEB_HOOK_CARTMODAL_ENABLED'),
        'rightcolumn' => Configuration::get('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED'),
    ];

    $keepFreeShipping = [
        'enabled' => Configuration::get('SJ4WEB_FREE_SHIPPING_ENABLED'),
        'threshold' => Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD'),
        'info' => Configuration::get('SJ4WEB_FREE_SHIPPING_INFO'),
    ];

    $keepExcludedCategories = Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES');

    // ===== ÉTAPE 2 : Supprimer les anciennes configurations obsolètes =====
    Configuration::deleteByName('SJ4WEB_DISCOUNT_THRESHOLD');
    Configuration::deleteByName('SJ4WEB_DISCOUNT_THRESHOLD_FROM');
    Configuration::deleteByName('SJ4WEB_DISCOUNT_TYPE');
    Configuration::deleteByName('SJ4WEB_DISCOUNT_VALUE');
    Configuration::deleteByName('SJ4WEB_DISCOUNT_INFO');

    // ===== ÉTAPE 3 : Créer les nouvelles configurations v2.0.0 =====

    // Pays autorisés (nouveau)
    if (!Configuration::get('SJ4WEB_ALLOWED_COUNTRIES')) {
        Configuration::updateValue('SJ4WEB_ALLOWED_COUNTRIES', 'FR,BE');
    }

    // Remise multi-paliers activée par défaut
    if (!Configuration::get('SJ4WEB_DISCOUNT_ENABLED')) {
        Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', 1);
    }

    // Montant minimum d'affichage (nouveau)
    if (!Configuration::get('SJ4WEB_DISCOUNT_MIN_DISPLAY')) {
        Configuration::updateValue('SJ4WEB_DISCOUNT_MIN_DISPLAY', 0);
    }

    // Messages par défaut avec tokens (nouveaux)
    if (!Configuration::get('SJ4WEB_MESSAGE_BEFORE_TIER')) {
        Configuration::updateValue(
            'SJ4WEB_MESSAGE_BEFORE_TIER',
            'Plus que {amount}€ pour bénéficier de {discount} de remise'
        );
    }

    if (!Configuration::get('SJ4WEB_MESSAGE_AFTER_TIER')) {
        Configuration::updateValue(
            'SJ4WEB_MESSAGE_AFTER_TIER',
            'Vous bénéficiez de {discount} de remise'
        );
    }

    if (!Configuration::get('SJ4WEB_MESSAGE_BETWEEN_TIERS')) {
        Configuration::updateValue(
            'SJ4WEB_MESSAGE_BETWEEN_TIERS',
            'Vous bénéficiez de {discount} de remise, plus que {amount}€ pour {next_discount}'
        );
    }

    if (!Configuration::get('SJ4WEB_MESSAGE_TIER_INFO')) {
        Configuration::updateValue('SJ4WEB_MESSAGE_TIER_INFO', '');
    }

    // ===== ÉTAPE 4 : Restaurer les valeurs sauvegardées =====

    // Couleurs
    if ($keepColors['bg']) {
        Configuration::updateValue('SJ4WEB_COLOR_BG', $keepColors['bg']);
    }
    if ($keepColors['text']) {
        Configuration::updateValue('SJ4WEB_COLOR_TEXT', $keepColors['text']);
    }
    if ($keepColors['subtitle']) {
        Configuration::updateValue('SJ4WEB_COLOR_SUBTITLE', $keepColors['subtitle']);
    }

    // Hooks
    Configuration::updateValue('SJ4WEB_HOOK_REASSURANCE_ENABLED', $keepHooks['reassurance']);
    Configuration::updateValue('SJ4WEB_HOOK_CARTMODAL_ENABLED', $keepHooks['cartmodal']);
    Configuration::updateValue('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED', $keepHooks['rightcolumn']);

    // Livraison gratuite
    Configuration::updateValue('SJ4WEB_FREE_SHIPPING_ENABLED', $keepFreeShipping['enabled']);
    if ($keepFreeShipping['threshold']) {
        Configuration::updateValue('SJ4WEB_FREE_SHIPPING_THRESHOLD', $keepFreeShipping['threshold']);
    }
    if ($keepFreeShipping['info']) {
        Configuration::updateValue('SJ4WEB_FREE_SHIPPING_INFO', $keepFreeShipping['info']);
    }

    // Catégories exclues
    if ($keepExcludedCategories) {
        Configuration::updateValue('SJ4WEB_EXCLUDED_CATEGORIES', $keepExcludedCategories);
    }

    // ===== ÉTAPE 5 : Log de migration (optionnel) =====
    PrestaShopLogger::addLog(
        'Module sj4webtofreedelivery: Migration vers v2.0.0 effectuée avec succès',
        1, // Severity: informative
        null,
        'Module',
        $module->id
    );

    return true;
}
