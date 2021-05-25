<?php
class Ccc_Vendor_Model_Vendor extends Mage_Core_Model_Abstract
{
    /**#@+
     * Configuration pathes for email templates and identities
     */
    const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'vendor/create_account/email_template';
    const XML_PATH_REGISTER_EMAIL_IDENTITY = 'vendor/create_account/email_identity';
    const XML_PATH_REMIND_EMAIL_TEMPLATE = 'vendor/password/remind_email_template';
    const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'vendor/password/forgot_email_template';
    const XML_PATH_FORGOT_EMAIL_IDENTITY = 'vendor/password/forgot_email_identity';
    const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'vendor/create_account/email_domain';
    const XML_PATH_IS_CONFIRM                   = 'vendor/create_account/confirm';
    const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'vendor/create_account/email_confirmation_template';
    const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'vendor/create_account/email_confirmed_template';
    const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'vendor/create_account/generate_human_friendly_id';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE = 'vendor/changed_account/password_or_email_template';
    const XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY = 'vendor/changed_account/password_or_email_identity';
    /**#@-*/

    /**#@+
     * Codes of exceptions related to vendor model
     */
    const EXCEPTION_EMAIL_NOT_CONFIRMED       = 1;
    const EXCEPTION_INVALID_EMAIL_OR_PASSWORD = 2;
    const EXCEPTION_EMAIL_EXISTS              = 3;
    const EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN = 4;
    /**#@-*/

    /**#@+
     * Subscriptions
     */
    const SUBSCRIBED_YES = 'yes';
    const SUBSCRIBED_NO  = 'no';
    /**#@-*/

    const CACHE_TAG = 'vendor';

    /**
     * Minimum Password Length
     */
    const MINIMUM_PASSWORD_LENGTH = 6;

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'vendor';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'vendor';

    /**
     * List of errors
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Assoc array of vendor attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Vendor addresses array
     *
     * @var array
     * @deprecated after 1.4.0.0-rc1
     */
    protected $_addresses = null;

    /**
     * Vendor addresses collection
     *
     * @var Mage_Vendor_Model_Entity_Address_Collection
     */
    protected $_addressesCollection;

    /**
     * Is model deleteable
     *
     * @var boolean
     */
    protected $_isDeleteable = true;

    /**
     * Is model readonly
     *
     * @var boolean
     */
    protected $_isReadonly = false;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Confirmation requirement flag
     *
     * @var boolean
     */
    private static $_isConfirmationRequired;

    const ENTITY = 'vendor';

    protected function _construct()
    {
        parent::_construct();
        $this->_init('vendor/vendor');
    }

    public function checkInGroup($attributeId, $setId, $groupId)
    {
        $resource = Mage::getSingleton('core/resource');

        $readConnection = $resource->getConnection('core_read');
        $readConnection = $resource->getConnection('core_read');

        $query = '
            SELECT * FROM ' .
            $resource->getTableName('eav/entity_attribute')
            . ' WHERE `attribute_id` =' . $attributeId
            . ' AND `attribute_group_id` =' . $groupId
            . ' AND `attribute_set_id` =' . $setId;

        $results = $readConnection->fetchRow($query);

        if ($results) {
            return true;
        }
        return false;
    }
    /**
     * Initialize vendor model
     */

    /**
     * Retrieve vendor sharing configuration model
     *
     * @return Mage_Vendor_Model_Config_Share
     */
    public function getSharingConfig()
    {
        return Mage::getSingleton('vendor/config_share');
    }

    /**
     * Authenticate vendor
     *
     * @param  string $login
     * @param  string $password
     * @throws Mage_Core_Exception
     * @return true
     *
     */
    public function authenticate($login, $password)
    {
        $this->loadByEmail($login);
        if ($this->getConfirmation() && $this->isConfirmationRequired()) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('vendor')->__('This account is not confirmed.'),
                self::EXCEPTION_EMAIL_NOT_CONFIRMED
            );
        }
        if (!$this->validatePassword($password)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('vendor')->__('Invalid login or password.'),
                self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
            );
        }
        Mage::dispatchEvent('vendor_vendor_authenticated', array(
            'model'    => $this,
            'password' => $password,
        ));

        return true;
    }

    /**
     * Load vendor by email
     *
     * @param   string $vendorEmail
     * @return  Mage_Vendor_Model_Vendor
     */
    public function loadByEmail($vendorEmail)
    {
        // echo "<pre>";
        // print_r($this->_getResource());
        // die;
        $this->_getResource()->loadByEmail($this, $vendorEmail);
        return $this;
    }


    /**
     * Processing object before save data
     *
     * @return Mage_Vendor_Model_Vendor
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        $storeId = $this->getStoreId();
        if ($storeId === null) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }

        $this->getGroupId();
        return $this;
    }

    /**
     * Change vendor password
     *
     * @param   string $newPassword
     * @return  Mage_Vendor_Model_Vendor
     */
    public function changePassword($newPassword)
    {
        $this->_getResource()->changePassword($this, $newPassword);
        return $this;
    }

    /**
     * Get full vendor name
     *
     * @return string
     */
    public function getName()
    {
        $name = '';
        $config = Mage::getSingleton('eav/config');
        if ($config->getAttribute('vendor', 'prefix')->getIsVisible() && $this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }
        $name .= $this->getFirstname();
        if ($config->getAttribute('vendor', 'middlename')->getIsVisible() && $this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }
        $name .=  ' ' . $this->getLastname();
        if ($config->getAttribute('vendor', 'suffix')->getIsVisible() && $this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        return $name;
    }

    /**
     * Add address to address collection
     *
     * @param   Mage_Vendor_Model_Address $address
     * @return  Mage_Vendor_Model_Vendor
     */
    public function addAddress(Mage_Vendor_Model_Address $address)
    {
        $this->getAddressesCollection()->addItem($address);
        $this->getAddresses();
        $this->_addresses[] = $address;
        return $this;
    }

    /**
     * Retrieve vendor address by address id
     *
     * @param   int $addressId
     * @return  Mage_Vendor_Model_Address
     */
    public function getAddressById($addressId)
    {
        $address = Mage::getModel('vendor/address')->load($addressId);
        if ($this->getId() == $address->getParentId()) {
            return $address;
        }
        return Mage::getModel('vendor/address');
    }

    /**
     * Getting vendor address object from collection by identifier
     *
     * @param int $addressId
     * @return Mage_Vendor_Model_Address
     */
    public function getAddressItemById($addressId)
    {
        return $this->getAddressesCollection()->getItemById($addressId);
    }

    /**
     * Retrieve not loaded address collection
     *
     * @return Mage_Vendor_Model_Entity_Address_Collection
     */
    public function getAddressCollection()
    {
        return Mage::getResourceModel('vendor/address_collection');
    }

    /**
     * Vendor addresses collection
     *
     * @return Mage_Vendor_Model_Entity_Address_Collection
     */
    public function getAddressesCollection()
    {
        if ($this->_addressesCollection === null) {
            $this->_addressesCollection = $this->getAddressCollection()
                ->setVendorFilter($this)
                ->addAttributeToSelect('*');
            foreach ($this->_addressesCollection as $address) {
                $address->setVendor($this);
            }
        }

        return $this->_addressesCollection;
    }

    /**
     * Retrieve vendor address array
     *
     * @return array
     */
    public function getAddresses()
    {
        $this->_addresses = $this->getAddressesCollection()->getItems();
        return $this->_addresses;
    }

    /**
     * Retrieve all vendor attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = $this->_getResource()
                ->loadAllAttributes($this)
                ->getSortedAttributes();
        }
        return $this->_attributes;
    }

    /**
     * Get vendor attribute model object
     *
     * @param   string $attributeCode
     * @return  Mage_Vendor_Model_Entity_Attribute | null
     */
    public function getAttribute($attributeCode)
    {
        $this->getAttributes();
        if (isset($this->_attributes[$attributeCode])) {
            return $this->_attributes[$attributeCode];
        }
        return null;
    }

    /**
     * Set plain and hashed password
     *
     * @param string $password
     * @return Mage_Vendor_Model_Vendor
     */
    public function setPassword($password)
    {
        $this->setData('password', $password);
        $this->setPasswordHash($this->hashPassword($password));
        $this->setPasswordConfirmation(null);
        return $this;
    }

    /**
     * Hash vendor password
     *
     * @param   string $password
     * @param   int    $salt
     * @return  string
     */
    public function hashPassword($password, $salt = null)
    {
        return $this->_getHelper('core')
            ->getHash(trim($password), !is_null($salt) ? $salt : Mage_Admin_Model_User::HASH_SALT_LENGTH);
    }

    /**
     * Get helper instance
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _getHelper($helperName)
    {
        return Mage::helper($helperName);
    }

    /**
     * Retrieve random password
     *
     * @param   int $length
     * @return  string
     */
    public function generatePassword($length = 8)
    {
        $chars = Mage_Core_Helper_Data::CHARS_PASSWORD_LOWERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_UPPERS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_DIGITS
            . Mage_Core_Helper_Data::CHARS_PASSWORD_SPECIALS;
        return Mage::helper('core')->getRandomString($length, $chars);
    }

    /**
     * Validate password with salted hash
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        $hash = $this->getPasswordHash();

        if (!$hash) {
            return false;
        }
        return Mage::helper('core')->validateHash($password, $hash);
    }


    /**
     * Encrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function encryptPassword($password)
    {
        return Mage::helper('core')->encrypt($password);
    }

    /**
     * Decrypt password
     *
     * @param   string $password
     * @return  string
     */
    public function decryptPassword($password)
    {
        return Mage::helper('core')->decrypt($password);
    }

    /**
     * Retrieve default address by type(attribute)
     *
     * @param   string $attributeCode address type attribute code
     * @return  Mage_Vendor_Model_Address
     */
    public function getPrimaryAddress($attributeCode)
    {
        $primaryAddress = $this->getAddressesCollection()->getItemById($this->getData($attributeCode));

        return $primaryAddress ? $primaryAddress : false;
    }

    /**
     * Get vendor default billing address
     *
     * @return Mage_Vendor_Model_Address
     */
    public function getPrimaryBillingAddress()
    {
        return $this->getPrimaryAddress('default_billing');
    }

    /**
     * Get vendor default billing address
     *
     * @return Mage_Vendor_Model_Address
     */
    public function getDefaultBillingAddress()
    {
        return $this->getPrimaryBillingAddress();
    }

    /**
     * Get default vendor shipping address
     *
     * @return Mage_Vendor_Model_Address
     */
    public function getPrimaryShippingAddress()
    {
        return $this->getPrimaryAddress('default_shipping');
    }

    /**
     * Get default vendor shipping address
     *
     * @return Mage_Vendor_Model_Address
     */
    public function getDefaultShippingAddress()
    {
        return $this->getPrimaryShippingAddress();
    }

    /**
     * Retrieve ids of default addresses
     *
     * @return array
     */
    public function getPrimaryAddressIds()
    {
        $ids = array();
        if ($this->getDefaultBilling()) {
            $ids[] = $this->getDefaultBilling();
        }
        if ($this->getDefaultShipping()) {
            $ids[] = $this->getDefaultShipping();
        }
        return $ids;
    }

    /**
     * Retrieve all vendor default addresses
     *
     * @return array
     */
    public function getPrimaryAddresses()
    {
        $addresses = array();
        $primaryBilling = $this->getPrimaryBillingAddress();
        if ($primaryBilling) {
            $addresses[] = $primaryBilling;
            $primaryBilling->setIsPrimaryBilling(true);
        }

        $primaryShipping = $this->getPrimaryShippingAddress();
        if ($primaryShipping) {
            if ($primaryBilling->getId() == $primaryShipping->getId()) {
                $primaryBilling->setIsPrimaryShipping(true);
            } else {
                $primaryShipping->setIsPrimaryShipping(true);
                $addresses[] = $primaryShipping;
            }
        }
        return $addresses;
    }

    /**
     * Retrieve not default addresses
     *
     * @return array
     */
    public function getAdditionalAddresses()
    {
        $addresses = array();
        $primatyIds = $this->getPrimaryAddressIds();
        foreach ($this->getAddressesCollection() as $address) {
            if (!in_array($address->getId(), $primatyIds)) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     * Check if address is primary
     *
     * @param Mage_Vendor_Model_Address $address
     * @return boolean
     */
    public function isAddressPrimary(Mage_Vendor_Model_Address $address)
    {
        if (!$address->getId()) {
            return false;
        }
        return ($address->getId() == $this->getDefaultBilling()) || ($address->getId() == $this->getDefaultShipping());
    }

    /**
     * Send email with new account related information
     *
     * @param string $type
     * @param string $backUrl
     * @param string $storeId
     * @param string $password
     * @throws Mage_Core_Exception
     * @return Mage_Vendor_Model_Vendor
     */
    public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0', $password = null)
    {
        $types = array(
            'registered'   => self::XML_PATH_REGISTER_EMAIL_TEMPLATE, // welcome email, when confirmation is disabled
            'confirmed'    => self::XML_PATH_CONFIRMED_EMAIL_TEMPLATE, // welcome email, when confirmation is enabled
            'confirmation' => self::XML_PATH_CONFIRM_EMAIL_TEMPLATE, // email with confirmation link
        );
        if (!isset($types[$type])) {
            Mage::throwException(Mage::helper('vendor')->__('Wrong transactional account email type'));
        }

        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId($this->getSendemailStoreId());
        }

        if (!is_null($password)) {
            $this->setPassword($password);
        }

        $this->_sendEmailTemplate(
            $types[$type],
            self::XML_PATH_REGISTER_EMAIL_IDENTITY,
            array('vendor' => $this, 'back_url' => $backUrl),
            $storeId
        );
        $this->cleanPasswordsValidationData();

        return $this;
    }

    /**
     * Check if accounts confirmation is required in config
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        if ($this->canSkipConfirmation()) {
            return false;
        }
        if (self::$_isConfirmationRequired === null) {
            $storeId = $this->getStoreId() ? $this->getStoreId() : null;
            self::$_isConfirmationRequired = (bool)Mage::getStoreConfig(self::XML_PATH_IS_CONFIRM, $storeId);
        }

        return self::$_isConfirmationRequired;
    }

    /**
     * Generate random confirmation key
     *
     * @return string
     */
    public function getRandomConfirmationKey()
    {
        return md5(uniqid());
    }

    /**
     * Send email with new vendor password
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function sendPasswordReminderEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(
            self::XML_PATH_REMIND_EMAIL_TEMPLATE,
            self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('vendor' => $this),
            $storeId
        );

        return $this;
    }

    /**
     * Send info email about changed password or email
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function sendChangedPasswordOrEmail()
    {
        $storeId = $this->getStoreId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(
            self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_TEMPLATE,
            self::XML_PATH_CHANGED_PASSWORD_OR_EMAIL_IDENTITY,
            array('vendor' => $this),
            $storeId,
            $this->getOldEmail()
        );

        return $this;
    }

    /**
     * Send corresponding email template
     *
     * @param string $emailTemplate configuration path of email template
     * @param string $emailSender configuration path of email identity
     * @param array $templateParams
     * @param int|null $storeId
     * @param string|null $vendorEmail
     * @return Mage_Vendor_Model_Vendor
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null, $vendorEmail = null)
    {
        $vendorEmail = ($vendorEmail) ? $vendorEmail : $this->getEmail();
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($vendorEmail, $this->getName());
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig($sender, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
        $mailer->setTemplateParams($templateParams);
        $mailer->send();
        return $this;
    }

    /**
     * Send email with reset password confirmation link
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function sendPasswordResetConfirmationEmail()
    {
        $storeId = Mage::app()->getStore()->getId();
        if (!$storeId) {
            $storeId = $this->_getWebsiteStoreId();
        }

        $this->_sendEmailTemplate(
            self::XML_PATH_FORGOT_EMAIL_TEMPLATE,
            self::XML_PATH_FORGOT_EMAIL_IDENTITY,
            array('vendor' => $this),
            $storeId
        );

        return $this;
    }

    /**
     * Retrieve vendor group identifier
     *
     * @return int
     */
    // public function getGroupId()
    // {
    //     if (!$this->hasData('group_id')) {
    //         $storeId = $this->getStoreId() ? $this->getStoreId() : Mage::app()->getStore()->getId();
    //         $groupId = Mage::getStoreConfig(Mage_Vendor_Model_Group::XML_PATH_DEFAULT_ID, $storeId);
    //         $this->setData('group_id', $groupId);
    //     }
    //     return $this->getData('group_id');
    // }

    /**
     * Retrieve vendor tax class identifier
     *
     * @return int
     */
    public function getTaxClassId()
    {
        if (!$this->getData('tax_class_id')) {
            $this->setTaxClassId(Mage::getModel('vendor/group')->getTaxClassId($this->getGroupId()));
        }
        return $this->getData('tax_class_id');
    }

    /**
     * Check store availability for vendor
     *
     * @param   Mage_Core_Model_Store | int $store
     * @return  bool
     */
    public function isInStore($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $storeId = $store->getId();
        } else {
            $storeId = $store;
        }

        $availableStores = $this->getSharedStoreIds();
        return in_array($storeId, $availableStores);
    }

    /**
     * Retrieve store where vendor was created
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return Mage::app()->getStore($this->getStoreId());
    }

    /**
     * Retrieve shared store ids
     *
     * @return array
     */
    public function getSharedStoreIds()
    {
        $ids = $this->_getData('shared_store_ids');
        if ($ids === null) {
            $ids = array();
            if ((bool)$this->getSharingConfig()->isWebsiteScope()) {
                $ids = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            } else {
                foreach (Mage::app()->getStores() as $store) {
                    $ids[] = $store->getId();
                }
            }
            $this->setData('shared_store_ids', $ids);
        }

        return $ids;
    }

    /**
     * Retrive shared website ids
     *
     * @return array
     */
    public function getSharedWebsiteIds()
    {
        $ids = $this->_getData('shared_website_ids');
        if ($ids === null) {
            $ids = array();
            if ((bool)$this->getSharingConfig()->isWebsiteScope()) {
                $ids[] = $this->getWebsiteId();
            } else {
                foreach (Mage::app()->getWebsites() as $website) {
                    $ids[] = $website->getId();
                }
            }
            $this->setData('shared_website_ids', $ids);
        }
        return $ids;
    }

    /**
     * Set store to vendor
     *
     * @param Mage_Core_Model_Store $store
     * @return Mage_Vendor_Model_Vendor
     */
    public function setStore(Mage_Core_Model_Store $store)
    {
        $this->setStoreId($store->getId());
        $this->setWebsiteId($store->getWebsite()->getId());
        return $this;
    }

    /**
     * Validate vendor attribute values.
     * For existing vendor password + confirmation will be validated only when password is set (i.e. its change is requested)
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        if (!Zend_Validate::is(trim($this->getFirstname()), 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The first name cannot be empty.');
        }

        if (!Zend_Validate::is(trim($this->getLastname()), 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The last name cannot be empty.');
        }

        if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('vendor')->__('Invalid email address "%s".', $this->getEmail());
        }

        $password = $this->getPassword();
        if (!$this->getId() && !Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The password cannot be empty.');
        }
        if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(self::MINIMUM_PASSWORD_LENGTH))) {
            $errors[] = Mage::helper('vendor')
                ->__('The minimum password length is %s', self::MINIMUM_PASSWORD_LENGTH);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('vendor')->__('Please make sure your passwords match.');
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('vendor');
        $attribute = Mage::getModel('vendor/attribute')->loadByCode($entityType, 'dob');
        if ($attribute->getIsRequired() && '' == trim($this->getDob())) {
            $errors[] = Mage::helper('vendor')->__('The Date of Birth is required.');
        }
        $attribute = Mage::getModel('vendor/attribute')->loadByCode($entityType, 'taxvat');
        if ($attribute->getIsRequired() && '' == trim($this->getTaxvat())) {
            $errors[] = Mage::helper('vendor')->__('The TAX/VAT number is required.');
        }
        $attribute = Mage::getModel('vendor/attribute')->loadByCode($entityType, 'gender');
        if ($attribute->getIsRequired() && '' == trim($this->getGender())) {
            $errors[] = Mage::helper('vendor')->__('Gender is required.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Validate vendor attribute values on password reset
     * @return bool
     */
    public function validateResetPassword()
    {
        $errors   = array();
        $password = $this->getPassword();
        if (!Zend_Validate::is($password, 'NotEmpty')) {
            $errors[] = Mage::helper('vendor')->__('The password cannot be empty.');
        }
        if (!Zend_Validate::is($password, 'StringLength', array(self::MINIMUM_PASSWORD_LENGTH))) {
            $errors[] = Mage::helper('vendor')
                ->__('The minimum password length is %s', self::MINIMUM_PASSWORD_LENGTH);
        }
        $confirmation = $this->getPasswordConfirmation();
        if ($password != $confirmation) {
            $errors[] = Mage::helper('vendor')->__('Please make sure your passwords match.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Import vendor data from text array
     *
     * @param array $row
     * @return Mage_Vendor_Model_Vendor
     */
    public function importFromTextArray(array $row)
    {
        $this->resetErrors();
        $line = $row['i'];
        $row = $row['row'];

        $regions = Mage::getResourceModel('directory/region_collection');

        $website = Mage::getModel('core/website')->load($row['website_code'], 'code');

        if (!$website->getId()) {
            $this->addError(Mage::helper('vendor')->__('Invalid website, skipping the record, line: %s', $line));
        } else {
            $row['website_id'] = $website->getWebsiteId();
            $this->setWebsiteId($row['website_id']);
        }

        // Validate Email
        if (empty($row['email'])) {
            $this->addError(Mage::helper('vendor')->__('Missing email, skipping the record, line: %s', $line));
        } else {
            $this->loadByEmail($row['email']);
        }

        if (empty($row['entity_id'])) {
            if ($this->getData('entity_id')) {
                $this->addError(Mage::helper('vendor')->__('The vendor email (%s) already exists, skipping the record, line: %s', $row['email'], $line));
            }
        } else {
            if ($row['entity_id'] != $this->getData('entity_id')) {
                $this->addError(Mage::helper('vendor')->__('The vendor ID and email did not match, skipping the record, line: %s', $line));
            } else {
                $this->unsetData();
                $this->load($row['entity_id']);
                if (isset($row['store_view'])) {
                    $storeId = Mage::app()->getStore($row['store_view'])->getId();
                    if ($storeId) $this->setStoreId($storeId);
                }
            }
        }

        if (empty($row['website_code'])) {
            $this->addError(Mage::helper('vendor')->__('Missing website, skipping the record, line: %s', $line));
        }

        if (empty($row['group'])) {
            $row['group'] = 'General';
        }

        if (empty($row['firstname'])) {
            $this->addError(Mage::helper('vendor')->__('Missing first name, skipping the record, line: %s', $line));
        }
        if (empty($row['lastname'])) {
            $this->addError(Mage::helper('vendor')->__('Missing last name, skipping the record, line: %s', $line));
        }

        if (!empty($row['password_new'])) {
            $this->setPassword($row['password_new']);
            unset($row['password_new']);
            if (!empty($row['password_hash'])) unset($row['password_hash']);
        }

        $errors = $this->getErrors();
        if ($errors) {
            $this->unsetData();
            $this->printError(implode('<br />', $errors));
            return;
        }

        foreach ($row as $field => $value) {
            $this->setData($field, $value);
        }

        if (!$this->validateAddress($row, 'billing')) {
            $this->printError(Mage::helper('vendor')->__('Invalid billing address for (%s)', $row['email']), $line);
        } else {
            // Handling billing address
            $billingAddress = $this->getPrimaryBillingAddress();
            if (!$billingAddress  instanceof Mage_Vendor_Model_Address) {
                $billingAddress = Mage::getModel('vendor/address');
            }

            $regions->addRegionNameFilter($row['billing_region'])->load();
            if ($regions) foreach ($regions as $region) {
                $regionId = intval($region->getId());
            }

            $billingAddress->setFirstname($row['firstname']);
            $billingAddress->setLastname($row['lastname']);
            $billingAddress->setCity($row['billing_city']);
            $billingAddress->setRegion($row['billing_region']);
            if (isset($regionId)) {
                $billingAddress->setRegionId($regionId);
            }
            $billingAddress->setCountryId($row['billing_country']);
            $billingAddress->setPostcode($row['billing_postcode']);
            if (isset($row['billing_street2'])) {
                $billingAddress->setStreet(array($row['billing_street1'], $row['billing_street2']));
            } else {
                $billingAddress->setStreet(array($row['billing_street1']));
            }
            if (isset($row['billing_telephone'])) {
                $billingAddress->setTelephone($row['billing_telephone']);
            }

            if (!$billingAddress->getId()) {
                $billingAddress->setIsDefaultBilling(true);
                if ($this->getDefaultBilling()) {
                    $this->setData('default_billing', '');
                }
                $this->addAddress($billingAddress);
            } // End handling billing address
        }

        if (!$this->validateAddress($row, 'shipping')) {
            $this->printError(Mage::helper('vendor')->__('Invalid shipping address for (%s)', $row['email']), $line);
        } else {
            // Handling shipping address
            $shippingAddress = $this->getPrimaryShippingAddress();
            if (!$shippingAddress instanceof Mage_Vendor_Model_Address) {
                $shippingAddress = Mage::getModel('vendor/address');
            }

            $regions->addRegionNameFilter($row['shipping_region'])->load();

            if ($regions) foreach ($regions as $region) {
                $regionId = intval($region->getId());
            }

            $shippingAddress->setFirstname($row['firstname']);
            $shippingAddress->setLastname($row['lastname']);
            $shippingAddress->setCity($row['shipping_city']);
            $shippingAddress->setRegion($row['shipping_region']);
            if (isset($regionId)) {
                $shippingAddress->setRegionId($regionId);
            }
            $shippingAddress->setCountryId($row['shipping_country']);
            $shippingAddress->setPostcode($row['shipping_postcode']);
            if (isset($row['shipping_street2'])) {
                $shippingAddress->setStreet(array($row['shipping_street1'], $row['shipping_street2']));
            } else {
                $shippingAddress->setStreet(array($row['shipping_street1']));
            }
            if (!empty($row['shipping_telephone'])) {
                $shippingAddress->setTelephone($row['shipping_telephone']);
            }

            if (!$shippingAddress->getId()) {
                $shippingAddress->setIsDefaultShipping(true);
                $this->addAddress($shippingAddress);
            }
            // End handling shipping address
        }
        if (!empty($row['is_subscribed'])) {
            $isSubscribed = (bool)strtolower($row['is_subscribed']) == self::SUBSCRIBED_YES;
            $this->setIsSubscribed($isSubscribed);
        }
        unset($row);
        return $this;
    }

    /**
     * Unset subscription
     *
     * @return Mage_Vendor_Model_Vendor
     */
    function unsetSubscription()
    {
        if (isset($this->_isSubscribed)) {
            unset($this->_isSubscribed);
        }
        return $this;
    }

    /**
     * Clean all addresses
     *
     * @return Mage_Vendor_Model_Vendor
     */
    function cleanAllAddresses()
    {
        $this->_addressesCollection = null;
        $this->_addresses           = null;
    }

    /**
     * Add error
     *
     * @return Mage_Vendor_Model_Vendor
     */
    function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Retreive errors
     *
     * @return array
     */
    function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Reset errors array
     *
     * @return Mage_Vendor_Model_Vendor
     */
    function resetErrors()
    {
        $this->_errors = array();
        return $this;
    }

    /**
     * Print error
     *
     * @param $error
     * @param $line
     * @return boolean
     */
    function printError($error, $line = null)
    {
        if ($error == null) {
            return false;
        }

        $liStyle = 'background-color: #FDD; ';
        echo '<li style="' . $liStyle . '">';
        echo '<img src="' . Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif') . '" class="v-middle"/>';
        echo $error;
        if ($line) {
            echo '<small>, Line: <b>' . $line . '</b></small>';
        }
        echo '</li>';
    }

    /**
     * Validate address
     *
     * @param array $data
     * @param string $type
     * @return bool
     */
    function validateAddress(array $data, $type = 'billing')
    {
        $fields = array('city', 'country', 'postcode', 'telephone', 'street1');
        $usca   = array('US', 'CA');
        $prefix = $type ? $type . '_' : '';

        if ($data) {
            foreach ($fields as $field) {
                if (!isset($data[$prefix . $field])) {
                    return false;
                }
                if (
                    $field == 'country'
                    && in_array(strtolower($data[$prefix . $field]), array('US', 'CA'))
                ) {

                    if (!isset($data[$prefix . 'region'])) {
                        return false;
                    }

                    $region = Mage::getModel('directory/region')
                        ->loadByName($data[$prefix . 'region']);
                    if (!$region->getId()) {
                        return false;
                    }
                    unset($region);
                }
            }
            unset($data);
            return true;
        }
        return false;
    }

    /**
     * Prepare vendor for delete
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * Get vendor created at date timestamp
     *
     * @return int|null
     */
    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return Varien_Date::toTimestamp($date);
        }
        return null;
    }

    /**
     * Reset all model data
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function reset()
    {
        $this->setData(array());
        $this->setOrigData();
        $this->_attributes = null;

        return $this;
    }

    /**
     * Checks model is deleteable
     *
     * @return boolean
     */
    public function isDeleteable()
    {
        return $this->_isDeleteable;
    }

    /**
     * Set is deleteable flag
     *
     * @param boolean $value
     * @return Mage_Vendor_Model_Vendor
     */
    public function setIsDeleteable($value)
    {
        $this->_isDeleteable = (bool)$value;
        return $this;
    }

    /**
     * Checks model is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_isReadonly;
    }

    /**
     * Set is readonly flag
     *
     * @param boolean $value
     * @return Mage_Vendor_Model_Vendor
     */
    public function setIsReadonly($value)
    {
        $this->_isReadonly = (bool)$value;
        return $this;
    }

    /**
     * Check whether confirmation may be skipped when registering using certain email address
     *
     * @return bool
     */
    public function canSkipConfirmation()
    {
        return $this->getId() && $this->hasSkipConfirmationIfEmail()
            && strtolower($this->getSkipConfirmationIfEmail()) === strtolower($this->getEmail());
    }

    /**
     * Clone current object
     */
    public function __clone()
    {
        $newAddressCollection = $this->getPrimaryAddresses();
        $newAddressCollection = array_merge($newAddressCollection, $this->getAdditionalAddresses());
        $this->setId(null);
        $this->cleanAllAddresses();
        foreach ($newAddressCollection as $address) {
            $this->addAddress(clone $address);
        }
    }

    /**
     * Return Entity Type instance
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        return $this->_getResource()->getEntityType();
    }

    /**
     * Return Entity Type ID
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        $entityTypeId = $this->getData('entity_type_id');
        if (!$entityTypeId) {
            $entityTypeId = $this->getEntityType()->getId();
            $this->setData('entity_type_id', $entityTypeId);
        }
        return $entityTypeId;
    }

    /**
     * Get either first store ID from a set website or the provided as default
     *
     * @param int|string|null $storeId
     *
     * @return int
     */
    protected function _getWebsiteStoreId($defaultStoreId = null)
    {
        if ($this->getWebsiteId() != 0 && empty($defaultStoreId)) {
            $storeIds = Mage::app()->getWebsite($this->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Change reset password link token
     *
     * Stores new reset password link token
     *
     * @param string $newResetPasswordLinkToken
     * @return Mage_Vendor_Model_Vendor
     */
    public function changeResetPasswordLinkToken($newResetPasswordLinkToken)
    {
        if (!is_string($newResetPasswordLinkToken) || empty($newResetPasswordLinkToken)) {
            throw Mage::exception(
                'Mage_Core',
                Mage::helper('vendor')->__('Invalid password reset token.'),
                self::EXCEPTION_INVALID_RESET_PASSWORD_LINK_TOKEN
            );
        }
        $this->_getResource()->changeResetPasswordLinkToken($this, $newResetPasswordLinkToken);
        return $this;
    }

    /**
     * Check if current reset password link token is expired
     *
     * @return boolean
     */
    public function isResetPasswordLinkTokenExpired()
    {
        $resetPasswordLinkToken = $this->getRpToken();
        $resetPasswordLinkTokenCreatedAt = $this->getRpTokenCreatedAt();

        if (empty($resetPasswordLinkToken) || empty($resetPasswordLinkTokenCreatedAt)) {
            return true;
        }

        $tokenExpirationPeriod = Mage::helper('vendor')->getResetPasswordLinkExpirationPeriod();

        $currentDate = Varien_Date::now();
        $currentTimestamp = Varien_Date::toTimestamp($currentDate);
        $tokenTimestamp = Varien_Date::toTimestamp($resetPasswordLinkTokenCreatedAt);
        if ($tokenTimestamp > $currentTimestamp) {
            return true;
        }

        $hoursDifference = floor(($currentTimestamp - $tokenTimestamp) / (60 * 60));
        if ($hoursDifference >= $tokenExpirationPeriod) {
            return true;
        }

        return false;
    }

    /**
     * Clean password's validation data (password, password_confirmation)
     *
     * @return Mage_Vendor_Model_Vendor
     */
    public function cleanPasswordsValidationData()
    {
        $this->setData('password', null);
        $this->setData('password_confirmation', null);
        return $this;
    }
}
