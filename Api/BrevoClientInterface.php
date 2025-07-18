<?php
declare(strict_types=1);

namespace Beljic\Brevo\Api;

/**
 * Interface for Brevo API client.
 */
interface BrevoClientInterface
{
    /**
     * Check if an email is blacklisted in Brevo.
     *
     * @param string $email
     * @return bool
     */
    public function isBlacklisted(string $email): bool;
}
