<?php
/**
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
 */

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Sj4webToFreeDeliveryOld extends Module implements WidgetInterface
{
    protected $config_form = false;
    protected $cfgName = '';
    protected $defaults = [];

    public function __construct()
    {
        $this->name = 'sj4webtofreedelivery';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'SJ4WEB.FR';
        $this->need_instance = 0;
        $this->module_key = '932645251d1b47731f9a539963f2b3d7';
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('SJ4WEB - Rest for free delivery', [], 'Modules.Sj4webtofreedelivery.Admin');
        $this->description = $this->trans('Display the left amount for free delivery', [], 'Modules.Sj4webtofreedelivery.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->cfgName = 'SJ4WEB_TFD_';
        $this->defaults = [
            'CUSTOM_STATUS' => 0,
            'CUSTOM_AMOUNT' => 450,
            'txt_color' => '#ffffff',
            'bg_color' => '#E7692A',
            'border_color' => '#AB3E07',
            'TXT' => 'Sj4webToFreeDelivery - module, you can put own text in configuration',
            'EXCLUDED_CATS' => []
        ];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function install()
    {
        parent::install();
        $this->setDefaults();
        $this->registerHook('displayCartAjaxFreeShipp');
        $this->registerHook('displayCartModalContent');
        $this->registerHook('displayRightColumn');
        $this->registerHook('displayHeader');
        return true;
    }

    public function uninstall()
    {
        foreach ($this->defaults as $default => $value) {
            Configuration::deleteByName($this->cfgName . $default);
        }
        return parent::uninstall();
    }

    public function setDefaults()
    {
        foreach ($this->defaults as $default => $value) {
            if ($default === 'TXT') {
                $message_trads = array();
                foreach (Language::getLanguages(false) as $lang) {
                    $message_trads[(int)$lang['id_lang']] = $value;
                }
                Configuration::updateValue($this->cfgName . $default, $message_trads, true);
            } else {
                Configuration::updateValue($this->cfgName . $default, $value);
            }
        }
    }

    public function getContent()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return $this->getWarningMultishopHtml();
        }
        if (Tools::isSubmit('submitModule')) {
            $this->postProcess();
        }
        $this->context->smarty->assign('module_dir', $this->_path);
        return $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        $listCategories = $this->getConfigFormValues()['EXCLUDED_CATS'];

        return [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Modules.Sj4webtofreedelivery.Admin'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Custom free shipping amount status', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'CUSTOM_STATUS',
                        'is_bool' => true,
                        'desc' => $this->trans('By default module use free shipping value definien in Shipping >
                        preferences, but if you set free shipping price indvidual per carrier,
                        then you put same value here', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->trans('Enabled', [], 'Modules.Sj4webtofreedelivery.Admin'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->trans('Disabled', [], 'Modules.Sj4webtofreedelivery.Admin'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->trans('Custom free shipping amount value', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'CUSTOM_AMOUNT',
                        'desc' => $this->trans('Put price with tax ', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'size' => 20,
                        'suffix' => $this->context->currency->getSign(), 3,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->trans('Additional info', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'desc' => $this->trans('For example if you only offer free shipping for one carrier and you want to inform about that', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'TXT',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                    ],
                    [
                        'col' => 6,
                        'type' => 'categories',
                        'label' => $this->trans('Exclude Categories', [], 'Modules.Sj4webtofreedelivery.Admin'),
                        'name' => 'EXCLUDED_CATS',
                        'tree' => [
                            'root_category' => 1,
                            'id' => 'id_category',
                            'name' => 'name_category',
                            'use_checkbox' => true,
                            'selected_categories' => ($listCategories) ? $listCategories : []
                        ],
                        'desc' => $this->trans('Choice product categorie to exclude from to free delivery.', [], 'Modules.Sj4webtofreedelivery.Admin')
                    ]

                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Modules.Sj4webtofreedelivery.Admin'),
                ],
            ],
        ];
    }


    protected function getConfigFormValues()
    {
        $var = array();
        foreach ($this->defaults as $default => $value) {
            if ($default === 'TXT') {
                foreach (Language::getLanguages(false) as $lang) {
                    $var[$default][(int)$lang['id_lang']] = Configuration::get($this->cfgName . $default, (int)$lang['id_lang']);
                }
            } elseif ($default === 'EXCLUDED_CATS') {
                $var[$default] = json_decode(Configuration::get($this->cfgName . $default));
            } else {
                $var[$default] = Configuration::get($this->cfgName . $default);
            }
        }
        return $var;
    }

    protected function postProcess()
    {
        foreach ($this->defaults as $default => $value_def) {
            if ($default === 'TXT') {
                $message_trads = array();
                foreach ($_POST as $key => $value) {
                    if (preg_match('/TXT_/i', $key)) {
                        $id_lang = preg_split('/TXT_/i', $key);
                        $message_trads[(int)$id_lang[1]] = $value;
                    }
                }
                Configuration::updateValue($this->cfgName . $default, $message_trads, true);
            } else if ($default === 'EXCLUDED_CATS') {
                $listCategories = Tools::getValue($default);
                if ($listCategories) {
                    Configuration::updateValue($this->cfgName . $default, json_encode($listCategories));
                }

            } else {
                Configuration::updateValue($this->cfgName . $default, (Tools::getValue($default)));
            }
        }
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get($this->cfgName . 'CUSTOM_AMOUNT')) {
            $free_ship_from = Tools::convertPrice(
                (float)Configuration::get($this->cfgName . 'CUSTOM_AMOUNT'),
                Currency::getCurrencyInstance((int)Context::getContext()->currency->id)
            );
        } else {
            $free_ship_from = Tools::convertPrice(
                (float)Configuration::get('PS_SHIPPING_FREE_PRICE'),
                Currency::getCurrencyInstance((int)Context::getContext()->currency->id)
            );
        }
        Media::addJsDef(array('sj4web_from' => $free_ship_from));
        $this->context->controller->registerStylesheet(
            'sj4webtofreedelivery-style',
            'modules/' . $this->name . '/views/css/front-style.css'
        );
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        if ($this->context->cart->isVirtualCart()) {
            return false;
        }

        $templateFile = 'sj4webtofreedelivery.tpl';
        $assign = $this->getWidgetVariables($hookName, $configuration);

        if ($assign) {
            $this->smarty->assign($assign);
            return $this->fetch('module:' . $this->name . '/views/templates/hook/' . $templateFile);
        }

        return false;
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }
        $hide = false;

        if (Configuration::get($this->cfgName . 'CUSTOM_AMOUNT')) {
            $free_ship_from = Tools::convertPrice(
                (float)Configuration::get($this->cfgName . 'CUSTOM_AMOUNT'),
                Currency::getCurrencyInstance((int)Context::getContext()->currency->id)
            );
        } else {
            $free_ship_from = Tools::convertPrice(
                (float)Configuration::get('PS_SHIPPING_FREE_PRICE'),
                Currency::getCurrencyInstance((int)Context::getContext()->currency->id)
            );
        }
        /** @var $currentCart Cart */
        $currentCart = Context::getContext()->cart;
        if ($currentCart !== null) {
            $currentShippingAdr = $currentCart->id_address_delivery;
            if($currentShippingAdr) {
                $authorized_iso_codes = ['FR'];
                $address = new Address($currentShippingAdr);
                $country = new Country($address->id_country);
                if(!in_array($country->iso_code, $authorized_iso_codes)) {
                    return [];
                }
            }

            $currentShipping = $currentCart->getOrderTotal(true, Cart::ONLY_SHIPPING);
            // $currentCart = Context::getContext()->cart;
            $products = $currentCart->getProducts();
            $excluded_categories = json_decode(Configuration::get($this->cfgName . 'EXCLUDED_CATS'));
            $to_exclude = false;
            if ($excluded_categories) {
                foreach ($products as $tproduct) {
                    $product = new Product($tproduct['id_product']);
                    $pCatIds = $product->getCategories();
                    foreach ($pCatIds as $catId) {
                        if (in_array($catId, $excluded_categories)) {
                            $to_exclude = true;
                            break;
                        }
                    }
                    if ($to_exclude) {
                        break;
                    }
                }
            }

            if (!$currentShipping || $to_exclude) {
                return [];
            }

            $tax_excluded_display = Group::getPriceDisplayMethod(Group::getCurrent()->id);

            if ($tax_excluded_display) {
                $total = Context::getContext()->cart->getOrderTotal(false, Cart::BOTH_WITHOUT_SHIPPING);
            } else {
                $total = Context::getContext()->cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
            }


            if ($free_ship_from == 0) {
                return [];
            }

            if (count(Context::getContext()->cart->getOrderedCartRulesIds(CartRule::FILTER_ACTION_SHIPPING))) {
                return [];
            }

            $priceFormatter = new PriceFormatter();

            if (($free_ship_from - $total) <= 0) {
                $free_ship_remaining = 0;
                $hide = true;
            } else {
                $free_ship_remaining = $priceFormatter->format($free_ship_from - $total);
            }

            $free_ship_from = $priceFormatter->format($free_ship_from);

            return array(
                'free_ship_remaining' => $free_ship_remaining,
                'free_ship_from' => $free_ship_from,
                'hide' => $hide,
                'txt' => Configuration::get($this->cfgName . 'TXT', $this->context->language->id),
            );
        }
        return [];
    }

    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
                $this->trans('You cannot manage module from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit', [], 'Modules.Sj4webtofreedelivery.Admin') .
                '</p>';
        }

        return '';
    }
}
