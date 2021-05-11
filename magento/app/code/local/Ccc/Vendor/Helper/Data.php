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
 * @package     Mage_Vendor
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Vendor Data Helper
 *
 * @category   Mage
 * @package    Mage_Vendor
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Ccc_Vendor_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Query param name for last url visited
     */
    const REFERER_QUERY_PARAM_NAME = 'referer';

    /**
     * Route for vendor account login page
     */
    const ROUTE_ACCOUNT_LOGIN = 'vendor/account/login';

    /**
     * Config name for Redirect Vendor to Account Dashboard after Logging in setting
     */
    const XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD = 'vendor/startup/redirect_dashboard';

    /**
     * Config paths to VAT related vendor groups
     */
    const XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP = 'vendor/create_account/viv_intra_union_group';
    const XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP = 'vendor/create_account/viv_domestic_group';
    const XML_PATH_CUSTOMER_VIV_INVALID_GROUP = 'vendor/create_account/viv_invalid_group';
    const XML_PATH_CUSTOMER_VIV_ERROR_GROUP = 'vendor/create_account/viv_error_group';

    /**
     * Config path to option that enables/disables automatic group assignment based on VAT
     */
    const XML_PATH_CUSTOMER_VIV_GROUP_AUTO_ASSIGN = 'vendor/create_account/viv_disable_auto_group_assign_default';

    /**
     * Config path to support email
     */
    const XML_PATH_SUPPORT_EMAIL = 'trans_email/ident_support/email';

    /**
     * WSDL of VAT validation service
     *
     */
    const VAT_VALIDATION_WSDL_URL = 'http://ec.europa.eu/taxation_customs/vies/services/checkVatService?wsdl';

    /**
     * Configuration path to expiration period of reset password link
     */
    const XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD
        = 'default/vendor/password/reset_link_expiration_period';

    /**
     * Configuration path to require admin password on vendor password change
     */
    const XML_PATH_CUSTOMER_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD
        = 'vendor/password/require_admin_user_to_change_user_password';

    /**
     * Configuration path to password forgotten flow change
     */
    const XML_PATH_CUSTOMER_FORGOT_PASSWORD_FLOW_SECURE = 'admin/security/forgot_password_flow_secure';
    const XML_PATH_CUSTOMER_FORGOT_PASSWORD_EMAIL_TIMES = 'admin/security/forgot_password_email_times';
    const XML_PATH_CUSTOMER_FORGOT_PASSWORD_IP_TIMES    = 'admin/security/forgot_password_ip_times';

    /**
     * VAT class constants
     */
    const VAT_CLASS_DOMESTIC    = 'domestic';
    const VAT_CLASS_INTRA_UNION = 'intra_union';
    const VAT_CLASS_INVALID     = 'invalid';
    const VAT_CLASS_ERROR       = 'error';

    /**
     * Vendor groups collection
     *
     * @var Mage_Vendor_Model_Entity_Group_Collection
     */
    protected $_groups;

    /**
     * Check vendor is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return Mage::getSingleton('vendor/session')->isLoggedIn();
    }

    /**
     * Retrieve logged in vendor
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function getVendor()
    {
        if (empty($this->_vendor)) {
            $this->_vendor = Mage::getSingleton('vendor/session')->getVendor();
        }
        return $this->_vendor;
    }

    /**
     * Retrieve vendor groups collection
     *
     * @return Mage_Vendor_Model_Entity_Group_Collection
     */
    public function getGroups()
    {
        if (empty($this->_groups)) {
            $this->_groups = Mage::getModel('vendor/group')->getResourceCollection()
                ->setRealGroupsFilter()
                ->load();
        }
        return $this->_groups;
    }

    /**
     * Retrieve current (logged in) vendor object
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function getCurrentVendor()
    {
        return $this->getVendor();
    }

    /**
     * Retrieve full vendor name from provided object
     *
     * @param Varien_Object $object
     * @return string
     */
    public function getFullVendorName($object = null)
    {
        $name = '';
        if (is_null($object)) {
            $name = $this->getVendorName();
        } else {
            $config = Mage::getSingleton('eav/config');

            if (
                $config->getAttribute('vendor', 'prefix')->getIsVisible()
                && (
                    $object->getPrefix()
                    || $object->getVendorPrefix()
                    )
                ) {
                    $name .= ($object->getPrefix() ? $object->getPrefix() : $object->getVendorPrefix()) . ' ';
            }

            $name .= $object->getFirstname() ? $object->getFirstname() : $object->getVendorFirstname();

            if ($config->getAttribute('vendor', 'middlename')->getIsVisible()
                && (
                    $object->getMiddlename()
                    || $object->getVendorMiddlename()
                    )
                ) {
                    $name .= ' ' . (
                        $object->getMiddlename()
                        ? $object->getMiddlename()
                        : $object->getVendorMiddlename()
                    );
            }

            $name .= ' ' . (
                $object->getLastname()
                ? $object->getLastname()
                : $object->getVendorLastname()
            );

            if ($config->getAttribute('vendor', 'suffix')->getIsVisible()
                && (
                    $object->getSuffix()
                    || $object->getVendorSuffix()
                    )
                ) {
                    $name .= ' ' . (
                        $object->getSuffix()
                        ? $object->getSuffix()
                        : $object->getVendorSuffix()
                    );
            }
        }
        return $name;
    }

    /**
     * Retrieve current vendor name
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->getVendor()->getName();
    }

    /**
     * Check vendor has address
     *
     * @return bool
     */
    public function vendorHasAddresses()
    {
        return count($this->getVendor()->getAddresses()) > 0;
    }

    /**************************************************************************
     * Vendor urls
     */

    /**
     * Retrieve vendor login url
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_getUrl(self::ROUTE_ACCOUNT_LOGIN, $this->getLoginUrlParams());
    }

    /**
     * Retrieve parameters of vendor login url
     *
     * @return array
     */
    public function getLoginUrlParams()
    {
        $params = array();

        $referer = $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME);

        if (!$referer && !Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD)
            && !Mage::getSingleton('vendor/session')->getNoReferer()
        ) {
            $referer = Mage::getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true));
            $referer = Mage::helper('core')->urlEncode($referer);
        }

        if ($referer) {
            $params = array(self::REFERER_QUERY_PARAM_NAME => $referer);
        }

        return $params;
    }

    /**
     * Retrieve vendor login POST URL
     *
     * @return string
     */
    public function getLoginPostUrl()
    {
        $params = array();
        if ($this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)) {
            $params = array(
                self::REFERER_QUERY_PARAM_NAME => $this->_getRequest()->getParam(self::REFERER_QUERY_PARAM_NAME)
            );
        }
        return $this->_getUrl('vendor/account/loginPost', $params);
    }

    /**
     * Retrieve vendor logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_getUrl('vendor/account/logout');
    }

    /**
     * Retrieve vendor dashboard url
     *
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->_getUrl('vendor/account');
    }

    /**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getAccountUrl()
    {
        return $this->_getUrl('vendor/account');
    }

    /**
     * Retrieve vendor register form url
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_getUrl('vendor/account/create');
    }

    /**
     * Retrieve vendor register form post url
     *
     * @return string
     */
    public function getRegisterPostUrl()
    {
        return $this->_getUrl('vendor/account/createpost');
    }

    /**
     * Retrieve vendor account edit form url
     *
     * @return string
     */
    public function getEditUrl()
    {
        return $this->_getUrl('vendor/account/edit');
    }

    /**
     * Retrieve vendor edit POST URL
     *
     * @return string
     */
    public function getEditPostUrl()
    {
        return $this->_getUrl('vendor/account/editpost');
    }

    /**
     * Retrieve url of forgot password page
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_getUrl('vendor/account/forgotpassword');
    }

    /**
     * Check is confirmation required
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        return $this->getVendor()->isConfirmationRequired();
    }

    /**
     * Retrieve confirmation URL for Email
     *
     * @param string $email
     * @return string
     */
    public function getEmailConfirmationUrl($email = null)
    {
        return $this->_getUrl('vendor/account/confirmation', array('email' => $email));
    }

    /**
     * Check whether vendors registration is allowed
     *
     * @return bool
     */
    public function isRegistrationAllowed()
    {
        $result = new Varien_Object(array('is_allowed' => true));
        Mage::dispatchEvent('vendor_registration_is_allowed', array('result' => $result));
        return $result->getIsAllowed();
    }

    /**
     * Retrieve name prefix dropdown options
     *
     * @return array|bool
     */
    public function getNamePrefixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions(
            Mage::helper('vendor/address')->getConfig('prefix_options', $store)
        );
    }

    /**
     * Retrieve name suffix dropdown options
     *
     * @return array|bool
     */
    public function getNameSuffixOptions($store = null)
    {
        return $this->_prepareNamePrefixSuffixOptions(
            Mage::helper('vendor/address')->getConfig('suffix_options', $store)
        );
    }

    /**
     * Unserialize and clear name prefix or suffix options
     *
     * @param string $options
     * @return array|bool
     */
    protected function _prepareNamePrefixSuffixOptions($options)
    {
        $options = trim($options);
        if (empty($options)) {
            return false;
        }
        $result = array();
        $options = explode(';', $options);
        foreach ($options as $value) {
            $value = $this->escapeHtml(trim($value));
            $result[$value] = $value;
        }
        return $result;
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * Retrieve vendor reset password link expiration period in days
     *
     * @return int
     */
    public function getResetPasswordLinkExpirationPeriod()
    {
        return (int) Mage::getConfig()->getNode(self::XML_PATH_CUSTOMER_RESET_PASSWORD_LINK_EXPIRATION_PERIOD);
    }

    /**
     * Retrieve is require admin password on vendor password change
     *
     * @return bool
     */
    public function getIsRequireAdminUserToChangeUserPassword()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_REQUIRE_ADMIN_USER_TO_CHANGE_USER_PASSWORD);
    }

    /**
     * Get default vendor group id
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return int
     */
    public function getDefaultVendorGroupId($store = null)
    {
        return (int)Mage::getStoreConfig(Ccc_Vendor_Model_Group::XML_PATH_DEFAULT_ID, $store);
    }

    /**
     * Retrieve forgot password flow secure type
     *
     * @return int
     */
    public function getVendorForgotPasswordFlowSecure()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_CUSTOMER_FORGOT_PASSWORD_FLOW_SECURE);
    }

    /**
     * Retrieve forgot password requests to times per 24 hours from 1 e-mail
     *
     * @return int
     */
    public function getVendorForgotPasswordEmailTimes()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_CUSTOMER_FORGOT_PASSWORD_EMAIL_TIMES);
    }

    /**
     * Retrieve forgot password requests to times per hour from 1 IP
     *
     * @return int
     */
    public function getVendorForgotPasswordIpTimes()
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_CUSTOMER_FORGOT_PASSWORD_IP_TIMES);
    }

    /**
     * Retrieve vendor group ID based on his VAT number
     *
     * @param string $vendorCountryCode
     * @param Varien_Object $vatValidationResult
     * @param Mage_Core_Model_Store|string|int $store
     * @return null|int
     */
    public function getVendorGroupIdBasedOnVatNumber($vendorCountryCode, $vatValidationResult, $store = null)
    {
        $groupId = null;

        $vatClass = $this->getVendorVatClass($vendorCountryCode, $vatValidationResult, $store);

        $vatClassToGroupXmlPathMap = array(
            self::VAT_CLASS_DOMESTIC => self::XML_PATH_CUSTOMER_VIV_DOMESTIC_GROUP,
            self::VAT_CLASS_INTRA_UNION => self::XML_PATH_CUSTOMER_VIV_INTRA_UNION_GROUP,
            self::VAT_CLASS_INVALID => self::XML_PATH_CUSTOMER_VIV_INVALID_GROUP,
            self::VAT_CLASS_ERROR => self::XML_PATH_CUSTOMER_VIV_ERROR_GROUP
        );

        if (isset($vatClassToGroupXmlPathMap[$vatClass])) {
            $groupId = (int)Mage::getStoreConfig($vatClassToGroupXmlPathMap[$vatClass], $store);
        }

        return $groupId;
    }

    /**
     * Send request to VAT validation service and return validation result
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return Varien_Object
     */
    public function checkVatNumber($countryCode, $vatNumber, $requesterCountryCode = '', $requesterVatNumber = '')
    {
        // Default response
        $gatewayResponse = new Varien_Object(array(
            'is_valid' => false,
            'request_date' => '',
            'request_identifier' => '',
            'request_success' => false
        ));

        if (!extension_loaded('soap')) {
            Mage::logException(Mage::exception('Mage_Core',
                Mage::helper('core')->__('PHP SOAP extension is required.')));
            return $gatewayResponse;
        }

        if (!$this->canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)) {
            return $gatewayResponse;
        }

        try {
            $soapClient = $this->_createVatNumberValidationSoapClient();

            $requestParams = array();
            $requestParams['countryCode'] = $countryCode;
            $requestParams['vatNumber'] = str_replace(array(' ', '-'), array('', ''), $vatNumber);
            $requestParams['requesterCountryCode'] = $requesterCountryCode;
            $requestParams['requesterVatNumber'] = str_replace(array(' ', '-'), array('', ''), $requesterVatNumber);

            // Send request to service
            $result = $soapClient->checkVatApprox($requestParams);

            $gatewayResponse->setIsValid((boolean) $result->valid);
            $gatewayResponse->setRequestDate((string) $result->requestDate);
            $gatewayResponse->setRequestIdentifier((string) $result->requestIdentifier);
            $gatewayResponse->setRequestSuccess(true);
        } catch (Exception $exception) {
            $gatewayResponse->setIsValid(false);
            $gatewayResponse->setRequestDate('');
            $gatewayResponse->setRequestIdentifier('');
        }

        return $gatewayResponse;
    }

    /**
     * Check if parameters are valid to send to VAT validation service
     *
     * @param string $countryCode
     * @param string $vatNumber
     * @param string $requesterCountryCode
     * @param string $requesterVatNumber
     *
     * @return boolean
     */
    public function canCheckVatNumber($countryCode, $vatNumber, $requesterCountryCode, $requesterVatNumber)
    {
        $result = true;
        /** @var $coreHelper Mage_Core_Helper_Data */
        $coreHelper = Mage::helper('core');

        if (!is_string($countryCode)
            || !is_string($vatNumber)
            || !is_string($requesterCountryCode)
            || !is_string($requesterVatNumber)
            || empty($countryCode)
            || !$coreHelper->isCountryInEU($countryCode)
            || empty($vatNumber)
            || (empty($requesterCountryCode) && !empty($requesterVatNumber))
            || (!empty($requesterCountryCode) && empty($requesterVatNumber))
            || (!empty($requesterCountryCode) && !$coreHelper->isCountryInEU($requesterCountryCode))
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get VAT class
     *
     * @param string $vendorCountryCode
     * @param Varien_Object $vatValidationResult
     * @param Mage_Core_Model_Store|string|int|null $store
     * @return null|string
     */
    public function getVendorVatClass($vendorCountryCode, $vatValidationResult, $store = null)
    {
        $vatClass = null;

        $isVatNumberValid = $vatValidationResult->getIsValid();

        if (is_string($vendorCountryCode)
            && !empty($vendorCountryCode)
            && $vendorCountryCode === Mage::helper('core')->getMerchantCountryCode($store)
            && $isVatNumberValid
        ) {
            $vatClass = self::VAT_CLASS_DOMESTIC;
        } elseif ($isVatNumberValid) {
            $vatClass = self::VAT_CLASS_INTRA_UNION;
        } else {
            $vatClass = self::VAT_CLASS_INVALID;
        }

        if (!$vatValidationResult->getRequestSuccess()) {
            $vatClass = self::VAT_CLASS_ERROR;
        }

        return $vatClass;
    }

    /**
     * Get validation message that will be displayed to user by VAT validation result object
     *
     * @param Mage_Vendor_Model_Address $vendorAddress
     * @param bool $vendorGroupAutoAssignDisabled
     * @param Varien_Object $validationResult
     * @return Varien_Object
     */
    public function getVatValidationUserMessage($vendorAddress, $vendorGroupAutoAssignDisabled, $validationResult)
    {
        $message = '';
        $isError = true;
        $vendorVatClass = $this->getVendorVatClass($vendorAddress->getCountryId(), $validationResult);
        $groupAutoAssignDisabled = Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_VIV_GROUP_AUTO_ASSIGN);

        $willChargeTaxMessage    = $this->__('You will be charged tax.');
        $willNotChargeTaxMessage = $this->__('You will not be charged tax.');

        if ($validationResult->getIsValid()) {
            $message = $this->__('Your VAT ID was successfully validated.');
            $isError = false;

            if (!$groupAutoAssignDisabled && !$vendorGroupAutoAssignDisabled) {
                $message .= ' ' . ($vendorVatClass == self::VAT_CLASS_DOMESTIC
                    ? $willChargeTaxMessage
                    : $willNotChargeTaxMessage);
            }
        } else if ($validationResult->getRequestSuccess()) {
            $message = sprintf(
                $this->__('The VAT ID entered (%s) is not a valid VAT ID.') . ' ',
                $this->escapeHtml($vendorAddress->getVatId())
            );
            if (!$groupAutoAssignDisabled && !$vendorGroupAutoAssignDisabled) {
                $message .= $willChargeTaxMessage;
            }
        }
        else {
            $contactUsMessage = sprintf($this->__('If you believe this is an error, please contact us at %s'),
                Mage::getStoreConfig(self::XML_PATH_SUPPORT_EMAIL));

            $message = $this->__('Your Tax ID cannot be validated.') . ' '
                . (!$groupAutoAssignDisabled && !$vendorGroupAutoAssignDisabled
                    ? $willChargeTaxMessage . ' ' : '')
                . $contactUsMessage;
        }

        $validationMessageEnvelope = new Varien_Object();
        $validationMessageEnvelope->setMessage($message);
        $validationMessageEnvelope->setIsError($isError);

        return $validationMessageEnvelope;
    }

    /**
     * Create SOAP client based on VAT validation service WSDL
     *
     * @param boolean $trace
     * @return SoapClient
     */
    protected function _createVatNumberValidationSoapClient($trace = false)
    {
        return new SoapClient(self::VAT_VALIDATION_WSDL_URL, array('trace' => $trace));
    }
}
