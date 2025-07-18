<?php
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\Service;

use Beljic\Brevo\Api\BrevoClientInterface;
use Beljic\Brevo\Api\BrevoServiceInterface;

/**
 * Service class for Brevo API interactions.
 */
class BrevoService implements BrevoServiceInterface
{
    /**
     * @param BrevoClientInterface $apiClient
     */
    public function __construct(
        private readonly BrevoClientInterface $apiClient
    ) {
    }

    /**
     * Check if an email is blacklisted in Brevo.
     *
     * @param string $email
     * @return bool
     */
    public function isBlacklisted(string $email): bool
    {
        return $this->apiClient->isBlacklisted($email);
    }
}
