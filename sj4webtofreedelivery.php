<?php

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}


class Sj4webtofreedelivery extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'sj4webtofreedelivery';
        $this->tab = 'front_office_features';
        $this->version = '2.0.0';
        $this->author = 'SJ4WEB.FR';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('SJ4WEB – Amount left to unlock free shipping or discount', [], 'Modules.Sj4webtofreedelivery.Admin');
        $this->description = $this->trans('Display a message as the customer gets closer to earning free shipping or a discount.', [], 'Modules.Sj4webtofreedelivery.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7.8', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayCartAjaxFreeShipp')
            && $this->registerHook('displayCartModalContent')
            && $this->registerHook('displayReassurance')
            && $this->registerHook('displayRightColumn')
            && $this->registerHook('displayHeader')
            && Configuration::updateValue('SJ4WEB_FREE_SHIPPING_ENABLED', 1)
            && Configuration::updateValue('SJ4WEB_FREE_SHIPPING_THRESHOLD', 0)
            && Configuration::updateValue('SJ4WEB_FREE_SHIPPING_INFO', '')
            && Configuration::updateValue('SJ4WEB_EXCLUDED_CATEGORIES', '')
            && Configuration::updateValue('SJ4WEB_ALLOWED_COUNTRIES', 'FR,BE')
            && Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', 1)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_MIN_DISPLAY', 0)
            && Configuration::updateValue('SJ4WEB_MESSAGE_BEFORE_TIER', 'Plus que {amount}€ pour bénéficier de {discount} de remise')
            && Configuration::updateValue('SJ4WEB_MESSAGE_AFTER_TIER', 'Vous bénéficiez de {discount} de remise')
            && Configuration::updateValue('SJ4WEB_MESSAGE_BETWEEN_TIERS', 'Vous bénéficiez de {discount} de remise, plus que {amount}€ pour {next_discount}')
            && Configuration::updateValue('SJ4WEB_MESSAGE_TIER_INFO', '')
            && Configuration::updateValue('SJ4WEB_COLOR_BG', '#e9e4db')
            && Configuration::updateValue('SJ4WEB_COLOR_TEXT', '#707070')
            && Configuration::updateValue('SJ4WEB_COLOR_SUBTITLE', '#707070')
            && Configuration::updateValue('SJ4WEB_HOOK_REASSURANCE_ENABLED', 0)
            && Configuration::updateValue('SJ4WEB_HOOK_CARTMODAL_ENABLED', 0)
            && Configuration::updateValue('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED', 0);
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName('SJ4WEB_FREE_SHIPPING_ENABLED')
            && Configuration::deleteByName('SJ4WEB_FREE_SHIPPING_THRESHOLD')
            && Configuration::deleteByName('SJ4WEB_FREE_SHIPPING_INFO')
            && Configuration::deleteByName('SJ4WEB_EXCLUDED_CATEGORIES')
            && Configuration::deleteByName('SJ4WEB_ALLOWED_COUNTRIES')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_ENABLED')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_MIN_DISPLAY')
            && Configuration::deleteByName('SJ4WEB_MESSAGE_BEFORE_TIER')
            && Configuration::deleteByName('SJ4WEB_MESSAGE_AFTER_TIER')
            && Configuration::deleteByName('SJ4WEB_MESSAGE_BETWEEN_TIERS')
            && Configuration::deleteByName('SJ4WEB_MESSAGE_TIER_INFO')
            && Configuration::deleteByName('SJ4WEB_COLOR_BG')
            && Configuration::deleteByName('SJ4WEB_COLOR_TEXT')
            && Configuration::deleteByName('SJ4WEB_COLOR_SUBTITLE')
            && Configuration::deleteByName('SJ4WEB_HOOK_REASSURANCE_ENABLED')
            && Configuration::deleteByName('SJ4WEB_HOOK_CARTMODAL_ENABLED')
            && Configuration::deleteByName('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit_' . $this->name)) {
            Configuration::updateValue('SJ4WEB_FREE_SHIPPING_ENABLED', Tools::getValue('SJ4WEB_FREE_SHIPPING_ENABLED'));
            Configuration::updateValue('SJ4WEB_FREE_SHIPPING_THRESHOLD', Tools::getValue('SJ4WEB_FREE_SHIPPING_THRESHOLD'));
            Configuration::updateValue('SJ4WEB_FREE_SHIPPING_INFO', Tools::getValue('SJ4WEB_FREE_SHIPPING_INFO'));
            $excludedCats = Tools::getValue('SJ4WEB_EXCLUDED_CATEGORIES');
            Configuration::updateValue('SJ4WEB_EXCLUDED_CATEGORIES', is_array($excludedCats) ? implode(',', $excludedCats) : '');
            $allowedCountries = Tools::getValue('SJ4WEB_ALLOWED_COUNTRIES');
            Configuration::updateValue('SJ4WEB_ALLOWED_COUNTRIES', is_array($allowedCountries) ? implode(',', $allowedCountries) : 'FR,BE');
            Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', Tools::getValue('SJ4WEB_DISCOUNT_ENABLED'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_MIN_DISPLAY', Tools::getValue('SJ4WEB_DISCOUNT_MIN_DISPLAY'));
            Configuration::updateValue('SJ4WEB_MESSAGE_BEFORE_TIER', Tools::getValue('SJ4WEB_MESSAGE_BEFORE_TIER'));
            Configuration::updateValue('SJ4WEB_MESSAGE_AFTER_TIER', Tools::getValue('SJ4WEB_MESSAGE_AFTER_TIER'));
            Configuration::updateValue('SJ4WEB_MESSAGE_BETWEEN_TIERS', Tools::getValue('SJ4WEB_MESSAGE_BETWEEN_TIERS'));
            Configuration::updateValue('SJ4WEB_MESSAGE_TIER_INFO', Tools::getValue('SJ4WEB_MESSAGE_TIER_INFO'));
            Configuration::updateValue('SJ4WEB_COLOR_BG', Tools::getValue('SJ4WEB_COLOR_BG'));
            Configuration::updateValue('SJ4WEB_COLOR_TEXT', Tools::getValue('SJ4WEB_COLOR_TEXT'));
            Configuration::updateValue('SJ4WEB_COLOR_SUBTITLE', Tools::getValue('SJ4WEB_COLOR_SUBTITLE'));
            Configuration::updateValue('SJ4WEB_HOOK_REASSURANCE_ENABLED', Tools::getValue('SJ4WEB_HOOK_REASSURANCE_ENABLED'));
            Configuration::updateValue('SJ4WEB_HOOK_CARTMODAL_ENABLED', Tools::getValue('SJ4WEB_HOOK_CARTMODAL_ENABLED'));
            Configuration::updateValue('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED', Tools::getValue('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED'));
        }
        $infoTpl = $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/hook_info.tpl');
        return $infoTpl . $this->renderForm();
    }

    protected function renderForm()
    {
        $form = new HelperForm();
        $form->module = $this;
        $form->name_controller = $this->name;
        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $form->submit_action = 'submit_' . $this->name;
        $form->fields_value = [
            'SJ4WEB_FREE_SHIPPING_ENABLED' => Configuration::get('SJ4WEB_FREE_SHIPPING_ENABLED'),
            'SJ4WEB_FREE_SHIPPING_THRESHOLD' => Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD'),
            'SJ4WEB_FREE_SHIPPING_INFO' => Configuration::get('SJ4WEB_FREE_SHIPPING_INFO'),
            'SJ4WEB_EXCLUDED_CATEGORIES[]' => explode(',', Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES')),
            'SJ4WEB_ALLOWED_COUNTRIES[]' => explode(',', Configuration::get('SJ4WEB_ALLOWED_COUNTRIES', null, null, null, 'FR,BE')),
            'SJ4WEB_DISCOUNT_ENABLED' => Configuration::get('SJ4WEB_DISCOUNT_ENABLED'),
            'SJ4WEB_DISCOUNT_MIN_DISPLAY' => Configuration::get('SJ4WEB_DISCOUNT_MIN_DISPLAY'),
            'SJ4WEB_MESSAGE_BEFORE_TIER' => Configuration::get('SJ4WEB_MESSAGE_BEFORE_TIER'),
            'SJ4WEB_MESSAGE_AFTER_TIER' => Configuration::get('SJ4WEB_MESSAGE_AFTER_TIER'),
            'SJ4WEB_MESSAGE_BETWEEN_TIERS' => Configuration::get('SJ4WEB_MESSAGE_BETWEEN_TIERS'),
            'SJ4WEB_MESSAGE_TIER_INFO' => Configuration::get('SJ4WEB_MESSAGE_TIER_INFO'),
            'SJ4WEB_COLOR_BG' => Configuration::get('SJ4WEB_COLOR_BG', null, null, null, '#e9e4db'),
            'SJ4WEB_COLOR_TEXT' => Configuration::get('SJ4WEB_COLOR_TEXT', null, null, null, '#707070'),
            'SJ4WEB_COLOR_SUBTITLE' => Configuration::get('SJ4WEB_COLOR_SUBTITLE', null, null, null, '#707070'),
            'SJ4WEB_HOOK_REASSURANCE_ENABLED' => Configuration::get('SJ4WEB_HOOK_REASSURANCE_ENABLED'),
            'SJ4WEB_HOOK_CARTMODAL_ENABLED' => Configuration::get('SJ4WEB_HOOK_CARTMODAL_ENABLED'),
            'SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED' => Configuration::get('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED'),
        ];

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Free shipping and discount thresholds', [], 'Modules.Sj4webtofreedelivery.Admin'),
                    'icon' => 'icon-truck'
                ],
                'input' => [
                    [
                        'type' => 'color',
                        'label' => $this->trans('Background color', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_COLOR_BG',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->trans('Text color', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_COLOR_TEXT',
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->trans('Subtitle text color', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_COLOR_SUBTITLE',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable free shipping threshold', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_FREE_SHIPPING_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                        'desc' => $this->trans('Enable to display how much more is needed for free shipping.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Free shipping threshold (€)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_FREE_SHIPPING_THRESHOLD',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->trans('Amount needed to get free delivery.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Additional information', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_FREE_SHIPPING_INFO',
                        'desc' => $this->trans('Add some text here to explain the threshold, e.g. offer valid for deliveries in France only.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'categories',
                        'label' => $this->trans('Excluded categories', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_EXCLUDED_CATEGORIES',
                        'tree' => [
                            'id' => 'id_category',
                            'name' => 'name_category',
                            'selected_categories' => explode(',', Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES'))
                        ]
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Allowed countries', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_ALLOWED_COUNTRIES',
                        'multiple' => true,
                        'options' => [
                            'query' => Country::getCountries($this->context->language->id),
                            'id' => 'iso_code',
                            'name' => 'name'
                        ],
                        'desc' => $this->trans('Select countries where messages should be displayed. Default: FR, BE', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable discount tiers messages', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                        'desc' => $this->trans('Display messages about discount tiers from sj4web_paymentdiscount module.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Minimum cart amount to display discount messages (€)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_MIN_DISPLAY',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->trans('Start displaying discount tier messages from this cart amount. Leave blank or 0 to display from start.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Message BEFORE tier (not reached)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_MESSAGE_BEFORE_TIER',
                        'desc' => $this->trans('Tokens: {amount} = remaining amount, {discount} = discount value, {threshold} = tier threshold. Example: "Plus que {amount}€ pour bénéficier de {discount} de remise"', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Message AFTER tier (tier reached, no next tier)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_MESSAGE_AFTER_TIER',
                        'desc' => $this->trans('Tokens: {discount} = current discount. Example: "Vous bénéficiez de {discount} de remise"', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Message BETWEEN tiers (tier reached, next tier exists)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_MESSAGE_BETWEEN_TIERS',
                        'desc' => $this->trans('Tokens: {discount} = current discount, {amount} = amount to next tier, {next_discount} = next discount. Example: "Vous bénéficiez de {discount}, plus que {amount}€ pour {next_discount}"', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Additional tier information', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_MESSAGE_TIER_INFO',
                        'desc' => $this->trans('Optional extra text to display with tier messages (e.g., "selon moyen de paiement").', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Display on hook Reassurance', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_HOOK_REASSURANCE_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                        'desc' => $this->trans('Enable to display the message on the displayReassurance hook.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Display on hook CartModal', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_HOOK_CARTMODAL_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                        'desc' => $this->trans('Enable to display the message on the displayCartModalContent hook.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Display on hook RightColumn', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                        'desc' => $this->trans('Enable to display the message on the displayRightColumn hook.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Sj4webtofreedelivery.Admin'),
                ]
            ]
        ];

        return $form->generateForm([$fields_form]);
    }

    public function hookDisplayCartAjaxFreeShipp($params)
    {
        return $this->renderWidget('displayCartAjaxFreeShipp', $params);
    }

    public function hookDisplayReassurance($params)
    {
        $displayInHook = (bool)Configuration::get('SJ4WEB_HOOK_REASSURANCE_ENABLED');
        if ($displayInHook) {
            return $this->renderWidget('displayReassurance', $params);
        }
        return '';
    }

    public function hookDisplayCartModalContent($params)
    {
        $displayInHook = (bool)Configuration::get('SJ4WEB_HOOK_CARTMODAL_ENABLED');
        if ($displayInHook) {
            return $this->renderWidget('displayCartModalContent', $params);
        }
        return '';
    }

    public function hookDisplayRightColumn($params)
    {
        $displayInHook = (bool)Configuration::get('SJ4WEB_HOOK_RIGHTCOLUMN_ENABLED');
        if ($displayInHook) {
            return $this->renderWidget('displayRightColumn', $params);
        }
        return '';
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'sj4webtofreedelivery-style',
            'modules/' . $this->name . '/views/css/front-style.css'
        );
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName === null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if ($this->context->cart->isVirtualCart()) {
            return false;
        }

        $variables = $this->getWidgetVariables($hookName, $configuration);

        if (!empty($variables)) {
            $this->context->smarty->assign($variables);
            return $this->fetch('module:' . $this->name . '/views/templates/hook/sj4webtofreedelivery.tpl');
        }

        return false;
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $context = $this->context;
        $cart = $context->cart;

        if (!$cart || $cart->isVirtualCart()) {
            return [];
        }

        // Vérifier les pays autorisés (configurable)
        $allowedCountries = Configuration::get('SJ4WEB_ALLOWED_COUNTRIES', null, null, null, 'FR,BE');
        $authorized_iso_codes = array_filter(explode(',', $allowedCountries));

        if (!empty($authorized_iso_codes)) {
            $id_address = (int)$cart->id_address_delivery;
            if ($id_address) {
                $country = new Country((new Address($id_address))->id_country);
                if (!in_array($country->iso_code, $authorized_iso_codes, true)) {
                    return [];
                }
            }
        }

        if (count($cart->getOrderedCartRulesIds(CartRule::FILTER_ACTION_SHIPPING))) {
            return [];
        }

        $products = $cart->getProducts();
        $excluded = array_map('intval', explode(',', (Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES')) ?? ''));
        if (count($excluded) === 1 && $excluded[0] === 0) {
            $excluded = [];
        }
        foreach ($products as $prod) {
            $product = new Product($prod['id_product']);
            $cats = $product->getCategories();
            if (array_intersect($excluded, $cats)) {
                return [];
            }
        }

        $taxExcl = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        $total = $cart->getOrderTotal(!$taxExcl, Cart::BOTH_WITHOUT_SHIPPING);
        $discount = $cart->getOrderTotal(!$taxExcl, Cart::ONLY_DISCOUNTS);

        $categoryIds = array_merge(...array_map(
            fn($p) => (new Product($p['id_product']))->getCategories(),
            $products
        ));
        $message = $this->getPalletMessage($total, $categoryIds, $discount);

        if (!$message) {
            return [];
        }

        $priceFormatter = new PriceFormatter();

        return [
            'free_ship_remaining' => $message['type'] === 'free_shipping'
                ? $priceFormatter->format(Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD') - $total)
                : null,
            'free_ship_from' => $priceFormatter->format((float)Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD')),
            'free_ship_message' => $message['type'] === 'free_shipping' ? $message['message'] : null,
            'discount_message' => in_array($message['type'], ['discount_active', 'discount_waiting', 'discount_active_between']) ? $message['message'] : '',
            'hide' => false,
            'txt' => $message['extra'] ?? '',
            'color_subtitle' => Configuration::get('SJ4WEB_COLOR_SUBTITLE', null, null, null, '#707070'),
            'color_bg' => Configuration::get('SJ4WEB_COLOR_BG', null, null, null, '#e9e4db'),
            'color_text' => Configuration::get('SJ4WEB_COLOR_TEXT', null, null, null, '#707070'),
        ];
    }

    /**
     * Récupère les paliers de remise depuis le module sj4web_paymentdiscount
     *
     * @return array Liste des paliers triés par threshold croissant
     */
    private function getTiersFromPaymentDiscountModule(): array
    {
        // Vérifier si le module paymentdiscount existe et est actif
        if (!Module::isInstalled('sj4web_paymentdiscount') || !Module::isEnabled('sj4web_paymentdiscount')) {
            return [];
        }

        // Vérifier si la table existe
        $tableName = _DB_PREFIX_ . 'sj4web_payment_discount_rule';
        $tableExists = Db::getInstance()->executeS("SHOW TABLES LIKE '" . $tableName . "'");
        if (empty($tableExists)) {
            return [];
        }

        // Récupérer les paliers actifs avec leurs messages personnalisés (optionnels)
        $sql = 'SELECT `threshold`, `trigger_threshold`, `voucher_code`, `name`, `message_before`, `message_after`, `message_between`
                FROM `' . $tableName . '`
                WHERE `active` = 1
                ORDER BY `threshold` ASC';

        $result = Db::getInstance()->executeS($sql);

        if (empty($result)) {
            return [];
        }

        // Enrichir chaque palier avec le pourcentage depuis le CartRule
        foreach ($result as &$tier) {
            $tier['discount_percent'] = $this->getDiscountPercentFromVoucher($tier['voucher_code']);
        }

        return $result;
    }

    /**
     * Récupère le pourcentage de remise depuis un CartRule PrestaShop
     *
     * @param string $voucherCode Code du bon de réduction
     * @return float Pourcentage de remise (ex: 5.0 pour 5%)
     */
    private function getDiscountPercentFromVoucher($voucherCode)
    {
        if (empty($voucherCode)) {
            return 0;
        }

        // Récupérer le CartRule depuis son code
        $cartRuleId = (int)Db::getInstance()->getValue(
            'SELECT `id_cart_rule` FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `code` = "' . pSQL($voucherCode) . '"'
        );

        if (!$cartRuleId) {
            return 0;
        }

        try {
            $cartRule = new CartRule($cartRuleId);

            // Le pourcentage de remise est dans reduction_percent
            return (float)$cartRule->reduction_percent;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Vérifie si un BR du module payment_discount est appliqué dans le panier
     *
     * @param Cart $cart
     * @param array $tiers Liste des paliers
     * @return array|null Informations du palier appliqué ou null
     */
    private function getAppliedDiscountVoucher($cart, $tiers): ?array
    {
        if (empty($tiers) || !$cart || !$cart->id) {
            return null;
        }

        // Récupérer tous les voucher codes des paliers
        $tierVoucherCodes = array_column($tiers, 'voucher_code');

        // Récupérer les CartRules du panier
        $cartRules = $cart->getCartRules(CartRule::FILTER_ACTION_ALL);

        foreach ($cartRules as $cartRule) {
            $code = $cartRule['code'];

            // Vérifier si ce code correspond à un de nos paliers
            if (in_array($code, $tierVoucherCodes, true)) {
                // Trouver le palier correspondant
                foreach ($tiers as $tier) {
                    if ($tier['voucher_code'] === $code) {
                        return $tier;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Remplace les tokens dans une phrase
     *
     * @param string $template Phrase avec tokens
     * @param array $data Données pour remplacement
     * @return string Phrase avec tokens remplacés
     */
    private function replaceTokens(string $template, array $data): string
    {
        $replacements = [
            '{amount}' => isset($data['amount']) ? number_format((float)$data['amount'], 2, ',', ' ') : '',
            '{discount}' => $data['discount'] ?? '',
            '{next_discount}' => $data['next_discount'] ?? '',
            '{threshold}' => isset($data['threshold']) ? number_format((float)$data['threshold'], 2, ',', ' ') : '',
            '{next_threshold}' => isset($data['next_threshold']) ? number_format((float)$data['next_threshold'], 2, ',', ' ') : '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Récupère un message avec fallback : message du palier ou message global
     *
     * @param array|null $tier Le palier (peut contenir message_before/message_after)
     * @param string $messageType 'before' ou 'after'
     * @param string $globalConfigKey Clé de config pour le message global
     * @return string Le message à utiliser
     */
    private function getTierMessageWithFallback($tier, string $messageType, string $globalConfigKey): string
    {
        // Vérifier si le palier a un message personnalisé
        if ($tier && !empty($tier['message_' . $messageType])) {
            return $tier['message_' . $messageType];
        }

        // Fallback sur le message global
        return Configuration::get($globalConfigKey);
    }

    /**
     * Calcule le message à afficher selon le montant du panier.
     *
     * @param float $cartTotal Montant du panier HT (hors frais de port)
     * @param array $productCategoryIds Liste des IDs de catégories des produits du panier
     * @return array|null Message structuré ou null si rien à afficher
     */
    private function getPalletMessage(float $cartTotal, array $productCategoryIds, $cartDiscount = 0.00): ?array
    {
        // Vérifie si une des catégories est exclue
        $excluded = array_filter(array_map('intval', explode(',', Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES'))));
        foreach ($productCategoryIds as $catId) {
            if (in_array((int)$catId, $excluded, true)) {
                return null;
            }
        }

        $freeEnabled = (bool)Configuration::get('SJ4WEB_FREE_SHIPPING_ENABLED');
        $discountEnabled = (bool)Configuration::get('SJ4WEB_DISCOUNT_ENABLED');
        $freeThreshold = (float)Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD');

        // ===== PRIORITÉ 1 : Livraison gratuite =====
        if ($freeEnabled && $cartTotal < $freeThreshold) {
            $diff = round($freeThreshold - $cartTotal, 2);
            return [
                'type' => 'free_shipping',
                'message' => $this->trans(
                    'Only %amount% € left to get free shipping.',
                    ['%amount%' => number_format($diff, 2, ',', ' ')],
                    'Modules.Sj4webtofreedelivery.Shop'
                ),
                'extra' => Configuration::get('SJ4WEB_FREE_SHIPPING_INFO'),
            ];
        }

        // ===== PRIORITÉ 2 : Paliers de remise multi-niveaux =====
        if (!$discountEnabled) {
            return null;
        }

        // Récupérer les paliers depuis le module payment_discount
        $tiers = $this->getTiersFromPaymentDiscountModule();
        if (empty($tiers)) {
            return null;
        }

        // Vérifier le montant minimum d'affichage
        $minDisplay = (float)Configuration::get('SJ4WEB_DISCOUNT_MIN_DISPLAY');
        if ($minDisplay > 0 && $cartTotal < $minDisplay) {
            return null;
        }

        // Ajouter la remise actuelle au total si applicable (pour le calcul de palier)
        $effectiveTotal = $cartTotal + $cartDiscount;

        // Filtrer les paliers selon trigger_threshold
        // Ne garder que les paliers dont le trigger_threshold (ou threshold si null) est atteint
        $tiers = array_filter($tiers, function ($tier) use ($effectiveTotal) {
            $triggerThreshold = isset($tier['trigger_threshold']) && $tier['trigger_threshold'] > 0
                ? (float)$tier['trigger_threshold']
                : (float)$tier['threshold'];
            return $effectiveTotal >= $triggerThreshold;
        });

        // Détecter si un BR du module est déjà appliqué
        $appliedVoucher = $this->getAppliedDiscountVoucher($this->context->cart, $tiers);

        // Trouver le palier actuel et le palier suivant
        $currentTier = null;
        $nextTier = null;

        foreach ($tiers as $index => $tier) {
            if ($effectiveTotal >= (float)$tier['threshold']) {
                $currentTier = $tier;
                // Chercher le palier suivant
                if (isset($tiers[$index + 1])) {
                    $nextTier = $tiers[$index + 1];
                }
            } else {
                // Premier palier non atteint = c'est le prochain
                if ($nextTier === null) {
                    $nextTier = $tier;
                }
                break;
            }
        }

        $tierInfo = Configuration::get('SJ4WEB_MESSAGE_TIER_INFO');

        // ===== CAS 1 : Aucun palier atteint, afficher le prochain =====
        if ($currentTier === null && $nextTier !== null) {
            $diff = round((float)$nextTier['threshold'] - $effectiveTotal, 2);
            $discountLabel = $nextTier['discount_percent'] . '%';

            // Utiliser le message du palier si présent, sinon message global
            $messageTemplate = $this->getTierMessageWithFallback(
                $nextTier,
                'before',
                'SJ4WEB_MESSAGE_BEFORE_TIER'
            );

            $message = $this->replaceTokens(
                $messageTemplate,
                [
                    'amount' => $diff,
                    'discount' => $discountLabel,
                    'threshold' => $nextTier['threshold']
                ]
            );

            return [
                'type' => 'discount_waiting',
                'message' => $message,
                'extra' => $tierInfo,
            ];
        }

        // ===== CAS 2 : Palier atteint ET BR appliqué =====
        if ($appliedVoucher !== null && $currentTier !== null) {
            $discountLabel = $appliedVoucher['discount_percent'] . '%';

            // Cas 2A : Il existe un palier suivant
            if ($nextTier !== null && $nextTier !== $currentTier) {

                $diff = round((float)$nextTier['threshold'] - $effectiveTotal, 2);
                $nextDiscountLabel = $nextTier['discount_percent'] . '%';

                // Utiliser message_between du palier actuel si défini, sinon message global
                $messageTemplate = !empty($currentTier['message_between'])
                    ? $currentTier['message_between']
                    : Configuration::get('SJ4WEB_MESSAGE_BETWEEN_TIERS');

                $message = $this->replaceTokens(
                    $messageTemplate,
                    [
                        'discount' => $discountLabel,
                        'amount' => $diff,
                        'next_discount' => $nextDiscountLabel,
                        'next_threshold' => $nextTier['threshold']
                    ]
                );

                if (empty($message)) {
                    $first_message = $this->getTierMessageWithFallback(
                        $currentTier,
                        'after',
                        'SJ4WEB_MESSAGE_AFTER_TIER'
                    );
                    $first_message = $this->replaceTokens(
                        $first_message,
                        [
                            'discount' => $discountLabel
                        ]
                    );
                    $second_message = $this->getTierMessageWithFallback(
                        $nextTier,
                        'before',
                        'SJ4WEB_MESSAGE_BEFORE_TIER'
                    );
                    $second_message = $this->replaceTokens(
                        $second_message,
                        [
                            'amount' => $diff,
                            'discount' => $nextDiscountLabel,
                            'threshold' => $nextTier['threshold']
                        ]
                    );
                    $message = $first_message . ' ' . $second_message;
                }

                return [
                    'type' => 'discount_active_between',
                    'message' => $message,
                    'extra' => $tierInfo,
                ];
            }

            // Cas 2B : Palier maximum atteint
            // Utiliser le message du palier si présent, sinon message global
            $messageTemplate = $this->getTierMessageWithFallback(
                $appliedVoucher,
                'after',
                'SJ4WEB_MESSAGE_AFTER_TIER'
            );

            $message = $this->replaceTokens(
                $messageTemplate,
                [
                    'discount' => $discountLabel
                ]
            );

            return [
                'type' => 'discount_active',
                'message' => $message,
                'extra' => $tierInfo,
            ];
        }

        // ===== CAS 3 : Palier atteint mais BR PAS appliqué =====
        // Cela signifie que le client ne remplit pas les conditions (groupe, pays, etc.)
        // Dans ce cas, on affiche le palier suivant s'il existe
        if ($currentTier !== null && $appliedVoucher === null && $nextTier !== null) {
            $diff = round((float)$nextTier['threshold'] - $effectiveTotal, 2);
            $nextDiscountLabel = $nextTier['discount_percent'] . '%';

            // Utiliser le message du palier suivant si présent, sinon message global
            $messageTemplate = $this->getTierMessageWithFallback(
                $nextTier,
                'before',
                'SJ4WEB_MESSAGE_BEFORE_TIER'
            );

            $message = $this->replaceTokens(
                $messageTemplate,
                [
                    'amount' => $diff,
                    'discount' => $nextDiscountLabel,
                    'threshold' => $nextTier['threshold']
                ]
            );

            return [
                'type' => 'discount_waiting',
                'message' => $message,
                'extra' => $tierInfo,
            ];
        }

        return null;
    }

}
