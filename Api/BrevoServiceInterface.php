<?php
declare(strict_types=1);

namespace Beljic\Brevo\Api;

/**
 * Interface BrevoServiceInterface
 *
 * This interface defines the methods for interacting with the Brevo API.
 * It includes a method to check if an email is blacklisted.
 */
interface BrevoServiceInterface
{
    /**
     * Check if an email is blacklisted in Brevo.
     *
     * @param string $email
     * @return bool
     */
    public function isBlacklisted(string $email): bool;
}
