<?php
/**
 * Brevo API Helper
 */
declare(strict_types=1);

namespace Beljic\Brevo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 *
 * Provides methods to retrieve Brevo API configuration settings.
 */
class Data extends AbstractHelper
{
    /**
     * Brevo API configuration paths
     */
    protected const XML_PATH_BREVO_API_KEY = 'brevo_api/settings/api_key';
    /**
     * Cache TTL for Brevo API responses in seconds.
     */
    protected const XML_PATH_BREVO_CACHE_TTL = 'brevo_api/settings/cache_ttl';

    /**
     * Indicates whether the Brevo API is enabled.
     */
    protected const XML_PATH_BREVO_ENABLED = 'brevo_api/settings/enabled';

    /**
     * Check if the Brevo API is enabled in the configuration.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_BREVO_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the Brevo API key from configuration.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_PATH_BREVO_API_KEY, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the cache TTL for Brevo API responses.
     *
     * @return int
     */
    public function getCacheTtl(): int
    {
        return (int) $this->scopeConfig->getValue(self::XML_PATH_BREVO_CACHE_TTL, ScopeInterface::SCOPE_STORE) ?: 3600;
    }
}
