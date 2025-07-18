<?php
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\Model;

use Beljic\Brevo\Api\BrevoClientInterface;
use Beljic\Brevo\Helper\Data as ConfigHelper;
use Brevo\Client\Api\ContactsApi;
use Brevo\Client\ApiException;
use Brevo\Client\Configuration;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * BrevoClient class to interact with Brevo API for checking if an email is blacklisted.
 */
class BrevoClient implements BrevoClientInterface
{
    /**
     * BrevoClient constructor.
     *
     * @param ConfigHelper $configHelper
     * @param CacheInterface $cache
     * @param SerializerInterface $serializer
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ConfigHelper        $configHelper,
        private readonly CacheInterface      $cache,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface     $logger,
    ) {
    }

    /**
     * Check if an email is blacklisted in Brevo.
     *
     * @param  string $email
     * @return boolean
     */
    public function isBlacklisted(string $email): bool
    {
        $cacheKey = 'brevo_blacklist_' . hash('sha256', $email);
        $cached = $this->cache->load($cacheKey);

        if ($cached !== false) {
            return (bool) $this->serializer->unserialize($cached);
        }

        $status = $this->callBrevoApi($email);

        $this->cache->save(
            $this->serializer->serialize($status),
            $cacheKey,
            [],
            $this->configHelper->getCacheTtl()
        );

        return $status;
    }

    /**
     * Calls the Brevo API to check blacklist status.
     *
     * @param string $email
     * @return bool
     */
    protected function callBrevoApi(string $email): bool|null
    {
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->configHelper->getApiKey());
            $api = new ContactsApi(null, $config);
            $response = $api->getContactInfo($email);
            return $response?->getEmailBlackListed();
        } catch (ApiException $e) {
            $this->logger->error('Brevo API error: ' . $e->getMessage());
            return null;
        } catch (Throwable $e) {
            $this->logger->error('Brevo error: ' . $e->getMessage());
            return null;
        }
    }
}
