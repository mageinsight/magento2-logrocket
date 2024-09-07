<?php

namespace MageInsight\LogRocket\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Get the LogRocket Config from magento.
 */
class LogRocketConfig implements ArgumentInterface
{
    const LOGROCKET_CONFIG_STATUS = 'logrocket/general/status';

    const LOGROCKET_CONFIG_APPID = 'logrocket/general/appId';

    const LOGROCKET_CONFIG_RESTRICTED_CLASSES = 'logrocket/general/restriction_classes';

    const LOGROCKET_CONFIG_RESTRICT_INPUT_FIELDS = 'logrocket/general/restrict_input_fields';
    
    const LOGROCKET_CODE = 'logrocket/general/code';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @var CurrentCustomer
     */
    private $currentCustomer;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfigInterface,
        CurrentCustomer $currentCustomer
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->currentCustomer = $currentCustomer;
    }

    public function getConfig($path, $websiteId = null)
    {
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        }

        return $this->scopeConfigInterface->getValue(
            $path,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    public function getCurrentWebsiteId() {
        return $this->storeManager->getStore()->getWebsiteId();
    }

    /**
     * Check if cookie restriction mode is enabled for this store
     *
     * @return bool
     * @since 100.1.3
     */
    public function isCookieRestrictionModeEnabled()
    {
        return $this->scopeConfigInterface->getValue(\Magento\Cookie\Helper\Cookie::XML_PATH_COOKIE_RESTRICTION);
    }

    public function isEnabled() {
        return $this->getConfig(self::LOGROCKET_CONFIG_STATUS);
    }

    public function getCode() {
        if ($this->isEnabled()) {
            return $this->getConfig(self::LOGROCKET_CODE);
        }

        return false;
    }

    public function getAppId() {
        if ($this->isEnabled()) {
            return $this->getConfig(self::LOGROCKET_CONFIG_APPID);
        }

        return false;
    }

    public function getCustomerId() {
        if ($this->isEnabled()) {
            return $this->currentCustomer->getCustomerId();
        }
    }

    public function getDomData() {
        $dom = [
            'privateAttributeBlocklist' => ['data-intl-tel-input-id'],
            'privateClassNameBlocklist' => explode(',', $this->getConfig(self::LOGROCKET_CONFIG_RESTRICTED_CLASSES)),
        ];

        if ($this->getConfig(self::LOGROCKET_CONFIG_RESTRICT_INPUT_FIELDS)) {
            $dom['inputSanitizer'] = true;
        }

        return $dom;
    }
}