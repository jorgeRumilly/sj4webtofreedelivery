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
        $this->version = '1.0.0';
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
            && Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_THRESHOLD', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_THRESHOLD_FROM', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_TYPE', 'percent')
            && Configuration::updateValue('SJ4WEB_DISCOUNT_VALUE', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_INFO', '')
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
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_ENABLED')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_THRESHOLD')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_THRESHOLD_FROM')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_TYPE')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_VALUE')
            && Configuration::deleteByName('SJ4WEB_DISCOUNT_INFO')
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
            Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', Tools::getValue('SJ4WEB_DISCOUNT_ENABLED'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_THRESHOLD', Tools::getValue('SJ4WEB_DISCOUNT_THRESHOLD'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_THRESHOLD_FROM', Tools::getValue('SJ4WEB_DISCOUNT_THRESHOLD_FROM'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_TYPE', Tools::getValue('SJ4WEB_DISCOUNT_TYPE'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_VALUE', Tools::getValue('SJ4WEB_DISCOUNT_VALUE'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_INFO', Tools::getValue('SJ4WEB_DISCOUNT_INFO'));
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
            'SJ4WEB_EXCLUDED_CATEGORIES' => explode(',', Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES')),
            'SJ4WEB_DISCOUNT_ENABLED' => Configuration::get('SJ4WEB_DISCOUNT_ENABLED'),
            'SJ4WEB_DISCOUNT_THRESHOLD' => Configuration::get('SJ4WEB_DISCOUNT_THRESHOLD'),
            'SJ4WEB_DISCOUNT_THRESHOLD_FROM' => Configuration::get('SJ4WEB_DISCOUNT_THRESHOLD_FROM'),
            'SJ4WEB_DISCOUNT_TYPE' => Configuration::get('SJ4WEB_DISCOUNT_TYPE'),
            'SJ4WEB_DISCOUNT_VALUE' => Configuration::get('SJ4WEB_DISCOUNT_VALUE'),
            'SJ4WEB_DISCOUNT_INFO' => Configuration::get('SJ4WEB_DISCOUNT_INFO'),
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
                        'type' => 'switch',
                        'label' => $this->trans('Enable discount threshold', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                        'desc' => $this->trans('Display remaining amount needed for a discount.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Discount threshold (€)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_THRESHOLD',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->trans('Amount needed for discount.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Minimum cart amount to display (€)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_THRESHOLD_FROM',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->trans('Display message starting from this cart amount. Leave blank or 0 to disable.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->trans('Discount type', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_TYPE',
                        'options' => [
                            'query' => [
                                ['id' => 'percent', 'name' => $this->trans('Percentage', [], 'Modules.Sj4webtofreedelivery.Admin')],
                                ['id' => 'amount', 'name' => $this->trans('Amount', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ],
                            'id' => 'id',
                            'name' => 'name'
                        ]
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Discount value', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_VALUE',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->trans('Discount value (percentage or fixed amount).', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Discount complementary message', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_INFO',
                        'desc' => $this->trans('Add some text here to explain the discount condition, e.g. valid only for professionals.', [], 'Modules.Sj4webtofreedelivery.Admin')
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

        $authorized_iso_codes = ['FR', 'BE'];
        $id_address = (int)$cart->id_address_delivery;
        if ($id_address) {
            $country = new Country((new Address($id_address))->id_country);
            if (!in_array($country->iso_code, $authorized_iso_codes, true)) {
                return [];
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

        $categoryIds = array_merge(...array_map(
            fn($p) => (new Product($p['id_product']))->getCategories(),
            $products
        ));
        $message = $this->getPalletMessage($total, $categoryIds);

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
            'discount_message' => in_array($message['type'], ['discount_active', 'discount_waiting']) ? $message['message'] : null,
            'hide' => false,
            'txt' => $message['extra'] ?? '',
            'color_subtitle' => Configuration::get('SJ4WEB_COLOR_SUBTITLE', null, null, null, '#707070'),
            'color_bg' => Configuration::get('SJ4WEB_COLOR_BG', null, null, null, '#e9e4db'),
            'color_text' => Configuration::get('SJ4WEB_COLOR_TEXT', null, null, null, '#707070'),
        ];
    }

    /**
     * Calcule le message à afficher selon le montant du panier.
     *
     * @param float $cartTotal Montant du panier HT (hors frais de port)
     * @param array $productCategoryIds Liste des IDs de catégories des produits du panier
     * @return array|null Message structuré ou null si rien à afficher
     */
    private function getPalletMessage(float $cartTotal, array $productCategoryIds): ?array
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
        $discountThreshold = (float)Configuration::get('SJ4WEB_DISCOUNT_THRESHOLD');
        $discountType = Configuration::get('SJ4WEB_DISCOUNT_TYPE');
        $discountValue = (float)Configuration::get('SJ4WEB_DISCOUNT_VALUE');
        $minCartFrom = (float)Configuration::get('SJ4WEB_DISCOUNT_THRESHOLD_FROM');

        // Cas 1 : Livraison gratuite
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

        // Cas 2 : Remise virement si le seuil est atteint (ou presque) et que le montant du panier est supérieur au seuil minimum (si défini)
        if ($discountEnabled && $cartTotal < $discountThreshold && $cartTotal >= $freeThreshold
            && ($minCartFrom === 0.0 || $cartTotal >= $minCartFrom)
        ) {
            $diff = round($discountThreshold - $cartTotal, 2);
            $label = $discountType === 'percent'
                ? $discountValue . '%'
                : number_format($discountValue, 2, ',', ' ') . ' €';

            return [
                'type' => 'discount_waiting',
                'message' => $this->trans(
                    'Only %amount% € left to get %discount% off your order.',
                    [
                        '%amount%' => number_format($diff, 2, ',', ' '),
                        '%discount%' => $label,
                    ],
                    'Modules.Sj4webtofreedelivery.Shop'
                ),
                'extra' => Configuration::get('SJ4WEB_DISCOUNT_INFO'),
            ];
        }
        // Cas 3 : Remise active
        if ($discountEnabled && $cartTotal >= $discountThreshold) {
            $label = $discountType === 'percent'
                ? $discountValue . '%'
                : number_format($discountValue, 2, ',', ' ') . ' €';

            return [
                'type' => 'discount_active',
                'message' => $this->trans(
                    'You’re getting %discount% off your order.',
                    ['%discount%' => $label],
                    'Modules.Sj4webtofreedelivery.Shop'
                ),
                'extra' => Configuration::get('SJ4WEB_DISCOUNT_INFO'),
            ];
        }

        return null;
    }

}
