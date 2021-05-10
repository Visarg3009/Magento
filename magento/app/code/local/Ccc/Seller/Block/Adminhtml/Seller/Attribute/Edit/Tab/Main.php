<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ccc_Seller_Block_Adminhtml_Seller_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return Ccc_Seller_Block_Adminhtml_Seller_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $attributeObject = $this->getAttributeObject();

        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $fieldset->getElements()
            ->searchById('attribute_code')
            ->setData(
                'class',
                'validate-code-event ' . $fieldset->getElements()->searchById('attribute_code')->getData('class')
            )->setData(
                'note',
                $fieldset->getElements()->searchById('attribute_code')->getData('note')
                    . Mage::helper('eav')->__('. Do not use "event" for an attribute code, it is a reserved keyword.')
            );
        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'price',
                'label' => Mage::helper('seller')->__('Price')
            ),
            array(
                'value' => 'media_image',
                'label' => Mage::helper('seller')->__('Media Image')
            )
        );
        if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = array(
                'value' => 'gallery',
                'label' => Mage::helper('seller')->__('Gallery')
            );
        }

        $response = new Varien_Object();
        $response->setTypes(array());

        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_STORE => Mage::helper('seller')->__('Store View'),
            Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_WEBSITE => Mage::helper('seller')->__('Website'),
            Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_GLOBAL => Mage::helper('seller')->__('Global'),
        );

        if (
            $attributeObject->getAttributeCode() == 'status'
            || $attributeObject->getAttributeCode() == 'tax_class_id'
        ) {
            unset($scopes[Ccc_Seller_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('seller')->__('Scope'),
            'title' => Mage::helper('seller')->__('Scope'),
            'note'  => Mage::helper('seller')->__('Declare attribute value saving scope'),
            'values' => $scopes
        ), 'attribute_code');


        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend' => Mage::helper('seller')->__('Frontend Properties')));

        $fieldset->addField('is_searchable', 'select', array(
            'name'     => 'is_searchable',
            'label'    => Mage::helper('seller')->__('Use in Quick Search'),
            'title'    => Mage::helper('seller')->__('Use in Quick Search'),
            'values'   => $yesnoSource,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('seller')->__('Use in Advanced Search'),
            'title' => Mage::helper('seller')->__('Use in Advanced Search'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('seller')->__('Comparable on Front-end'),
            'title' => Mage::helper('seller')->__('Comparable on Front-end'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('seller')->__("Use In Layered Navigation"),
            'title' => Mage::helper('seller')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => Mage::helper('seller')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('seller')->__('No')),
                array('value' => '1', 'label' => Mage::helper('seller')->__('Filterable (with results)')),
                array('value' => '2', 'label' => Mage::helper('seller')->__('Filterable (no results)')),
            ),
        ));

        $fieldset->addField('is_filterable_in_search', 'select', array(
            'name' => 'is_filterable_in_search',
            'label' => Mage::helper('seller')->__("Use In Search Results Layered Navigation"),
            'title' => Mage::helper('seller')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'note' => Mage::helper('seller')->__('Can be used only with catalog input type Dropdown, Multiple Select and Price'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_used_for_promo_rules', 'select', array(
            'name' => 'is_used_for_promo_rules',
            'label' => Mage::helper('seller')->__('Use for Promo Rule Conditions'),
            'title' => Mage::helper('seller')->__('Use for Promo Rule Conditions'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('seller')->__('Position'),
            'title' => Mage::helper('seller')->__('Position in Layered Navigation'),
            'note' => Mage::helper('seller')->__('Position of attribute in layered navigation block'),
            'class' => 'validate-digits',
        ));

        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('seller')->__('Enable WYSIWYG'),
            'title' => Mage::helper('seller')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        $htmlAllowed = $fieldset->addField('is_html_allowed_on_front', 'select', array(
            'name' => 'is_html_allowed_on_front',
            'label' => Mage::helper('seller')->__('Allow HTML Tags on Frontend'),
            'title' => Mage::helper('seller')->__('Allow HTML Tags on Frontend'),
            'values' => $yesnoSource,
        ));
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField('is_visible_on_front', 'select', array(
            'name'      => 'is_visible_on_front',
            'label'     => Mage::helper('seller')->__('Visible on Product View Page on Front-end'),
            'title'     => Mage::helper('seller')->__('Visible on Product View Page on Front-end'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('used_in_product_listing', 'select', array(
            'name'      => 'used_in_product_listing',
            'label'     => Mage::helper('seller')->__('Used in Product Listing'),
            'title'     => Mage::helper('seller')->__('Used in Product Listing'),
            'note'      => Mage::helper('seller')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ));
        $fieldset->addField('used_for_sort_by', 'select', array(
            'name'      => 'used_for_sort_by',
            'label'     => Mage::helper('seller')->__('Used for Sorting in Product Listing'),
            'title'     => Mage::helper('seller')->__('Used for Sorting in Product Listing'),
            'note'      => Mage::helper('seller')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ));



        // define field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
                ->addFieldMap("is_html_allowed_on_front", 'html_allowed_on_front')
                ->addFieldMap("frontend_input", 'frontend_input_type')
                ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
                ->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0')
        );



        return $this;
    }
}
