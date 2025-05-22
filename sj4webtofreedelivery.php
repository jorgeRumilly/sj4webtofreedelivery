<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

// use PrestaShop\PrestaShop\Core\Module\ModuleInterface;

class Sj4webtofreedelivery extends Module
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

        $this->displayName = $this->trans('To Free Delivery', [], 'Modules.Sj4webtofreedelivery.Admin');
        $this->description = $this->trans('Display messages related to free shipping or discount thresholds.', [], 'Modules.Sj4webtofreedelivery.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function install()
    {
        return parent::install()
            && Configuration::updateValue('SJ4WEB_FREE_SHIPPING_ENABLED', 1)
            && Configuration::updateValue('SJ4WEB_FREE_SHIPPING_THRESHOLD', 0)
            && Configuration::updateValue('SJ4WEB_FREE_SHIPPING_INFO', '')
            && Configuration::updateValue('SJ4WEB_EXCLUDED_CATEGORIES', '')
            && Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_THRESHOLD', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_TYPE', 'percent')
            && Configuration::updateValue('SJ4WEB_DISCOUNT_VALUE', 0)
            && Configuration::updateValue('SJ4WEB_DISCOUNT_INFO', '');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit_'.$this->name)) {
            Configuration::updateValue('SJ4WEB_FREE_SHIPPING_ENABLED', Tools::getValue('SJ4WEB_FREE_SHIPPING_ENABLED'));
            Configuration::updateValue('SJ4WEB_FREE_SHIPPING_THRESHOLD', Tools::getValue('SJ4WEB_FREE_SHIPPING_THRESHOLD'));
            Configuration::updateValue('SJ4WEB_FREE_SHIPPING_INFO', Tools::getValue('SJ4WEB_FREE_SHIPPING_INFO'));
            Configuration::updateValue('SJ4WEB_EXCLUDED_CATEGORIES', implode(',', Tools::getValue('SJ4WEB_EXCLUDED_CATEGORIES')));
            Configuration::updateValue('SJ4WEB_DISCOUNT_ENABLED', Tools::getValue('SJ4WEB_DISCOUNT_ENABLED'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_THRESHOLD', Tools::getValue('SJ4WEB_DISCOUNT_THRESHOLD'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_TYPE', Tools::getValue('SJ4WEB_DISCOUNT_TYPE'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_VALUE', Tools::getValue('SJ4WEB_DISCOUNT_VALUE'));
            Configuration::updateValue('SJ4WEB_DISCOUNT_INFO', Tools::getValue('SJ4WEB_DISCOUNT_INFO'));
        }

        return $this->renderForm();
    }

    protected function renderForm()
    {
        $form = new HelperForm();
        $form->module = $this;
        $form->name_controller = $this->name;
        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $form->fields_value = [
            'SJ4WEB_FREE_SHIPPING_ENABLED' => Configuration::get('SJ4WEB_FREE_SHIPPING_ENABLED'),
            'SJ4WEB_FREE_SHIPPING_THRESHOLD' => Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD'),
            'SJ4WEB_FREE_SHIPPING_INFO' => Configuration::get('SJ4WEB_FREE_SHIPPING_INFO'),
            'SJ4WEB_EXCLUDED_CATEGORIES' => explode(',', Configuration::get('SJ4WEB_EXCLUDED_CATEGORIES')),
            'SJ4WEB_DISCOUNT_ENABLED' => Configuration::get('SJ4WEB_DISCOUNT_ENABLED'),
            'SJ4WEB_DISCOUNT_THRESHOLD' => Configuration::get('SJ4WEB_DISCOUNT_THRESHOLD'),
            'SJ4WEB_DISCOUNT_TYPE' => Configuration::get('SJ4WEB_DISCOUNT_TYPE'),
            'SJ4WEB_DISCOUNT_VALUE' => Configuration::get('SJ4WEB_DISCOUNT_VALUE'),
            'SJ4WEB_DISCOUNT_INFO' => Configuration::get('SJ4WEB_DISCOUNT_INFO')
        ];

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Free shipping and discount thresholds', [], 'Modules.Sj4webtofreedelivery.Admin'),
                    'icon' => 'icon-truck'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Enable free shipping threshold', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_FREE_SHIPPING_ENABLED',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Yes', [], 'Modules.Sj4webtofreedelivery.Admin')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('No', [], 'Modules.Sj4webtofreedelivery.Admin')],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Free shipping threshold (€)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_FREE_SHIPPING_THRESHOLD',
                        'class' => 'fixed-width-sm'
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Additional information', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_FREE_SHIPPING_INFO'
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
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Discount threshold (€)', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_THRESHOLD',
                        'class' => 'fixed-width-sm'
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
                        'class' => 'fixed-width-sm'
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Discount complementary message', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'SJ4WEB_DISCOUNT_INFO'
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Sj4webtofreedelivery.Admin'),
                ]
            ]
        ];

        return $form->generateForm([$fields_form]);
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

        $freeEnabled = (bool) Configuration::get('SJ4WEB_FREE_SHIPPING_ENABLED');
        $discountEnabled = (bool) Configuration::get('SJ4WEB_DISCOUNT_ENABLED');

        $freeThreshold = (float) Configuration::get('SJ4WEB_FREE_SHIPPING_THRESHOLD');
        $discountThreshold = (float) Configuration::get('SJ4WEB_DISCOUNT_THRESHOLD');
        $discountType = Configuration::get('SJ4WEB_DISCOUNT_TYPE');
        $discountValue = (float) Configuration::get('SJ4WEB_DISCOUNT_VALUE');

        // Cas 1 : Livraison gratuite
        if ($freeEnabled && $cartTotal < $freeThreshold) {
            $diff = round($freeThreshold - $cartTotal, 2);
            return [
                'type' => 'free_shipping',
                'message' => $this->trans(
                    'Plus que %amount% € avant de bénéficier de la livraison gratuite.',
                    ['%amount%' => number_format($diff, 2, ',', ' ')],
                    'Modules.Sj4webtofreedelivery.Shop'
                ),
                'extra' => Configuration::get('SJ4WEB_FREE_SHIPPING_INFO'),
            ];
        }

        // Cas 2 : Remise virement si le seuil est atteint (ou presque)
        if ($discountEnabled && $cartTotal < $discountThreshold && $cartTotal >= $freeThreshold) {
            $diff = round($discountThreshold - $cartTotal, 2);
            $label = $discountType === 'percent'
                ? $discountValue . '%'
                : number_format($discountValue, 2, ',', ' ') . ' €';

            return [
                'type' => 'discount_waiting',
                'message' => $this->trans(
                    'Plus que %amount% € avant de bénéficier de %discount% sur votre commande.',
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
                    'Vous bénéficiez de %discount% sur votre commande.',
                    ['%discount%' => $label],
                    'Modules.Sj4webtofreedelivery.Shop'
                ),
                'extra' => Configuration::get('SJ4WEB_DISCOUNT_INFO'),
            ];
        }

        return null;
    }

}
