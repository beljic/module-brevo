<?php /** @noinspection PhpCSValidationInspection */
/** @noinspection PhpCSValidationInspection */
declare(strict_types=1);

namespace Beljic\Brevo\Test\Unit\Model;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use Beljic\Brevo\Model\BrevoClient;
use Beljic\Brevo\Helper\Data as ConfigHelper;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BrevoClientTest
 *
 * Unit tests for the BrevoClient class.
 */
class BrevoClientTest extends TestCase
{
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelperMock;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cacheMock;

    /*
     * @var SerializerInterface
     */
    private SerializerInterface $serializerMock;
    /*
     * @var LoggerInterface|PHPUnit\Framework\MockObject\MockObject
     */
    private LoggerInterface $loggerMock;

    /**
     * Set up the test environment with mocks.
     *
     * @throws Exception|\PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->configHelperMock = $this->createMock(ConfigHelper::class);
        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    /**
     * Test that isBlacklisted returns false when the email is not blacklisted.
     *
     * @throws Exception|\PHPUnit\Framework\MockObject\Exception
     */
    public function testIsBlacklistedReturnsCachedValue(): void
    {
        $email = 'test@example.com';
        $cacheKey = 'brevo_blacklist_' . hash('sha256', $email);
        $cachedValue = 'true';

        $this->cacheMock->method('load')->with($cacheKey)->willReturn($cachedValue);
        $this->serializerMock->method('unserialize')->with($cachedValue)->willReturn(true);

        $client = new BrevoClient($this->configHelperMock, $this->cacheMock, $this->serializerMock, $this->loggerMock);

        $this->assertTrue($client->isBlacklisted($email));
    }

    /**
     * Test that isBlacklisted calls the API when no cached value is found.
     *
     * This test ensures that the BrevoClient correctly calls the API and caches the result.
     *
     * @throws Exception|\PHPUnit\Framework\MockObject\Exception
     */
    public function testIsBlacklistedCallsApiAndCachesResult(): void
    {
        $email = 'user@example.com';
        $cacheKey = 'brevo_blacklist_' . hash('sha256', $email);
        $cacheTtl = 3600;

        $this->cacheMock->method('load')->with($cacheKey)->willReturn(false);

        $this->configHelperMock->method('getApiKey')->willReturn('testApiKey');
        $this->configHelperMock->method('getCacheTtl')->willReturn($cacheTtl);

        $this->serializerMock->method('serialize')->willReturn('serialized_true');

        $this->loggerMock->expects($this->never())->method('error');

        $clientMock = $this->getMockBuilder(BrevoClient::class)
            ->setConstructorArgs([$this->configHelperMock, $this->cacheMock, $this->serializerMock, $this->loggerMock])
            ->onlyMethods(['callBrevoApi'])
            ->getMock();

        $clientMock->expects($this->once())
            ->method('callBrevoApi')
            ->with($email)
            ->willReturn(true);

        $this->cacheMock->expects($this->once())
            ->method('save')
            ->with('serialized_true', $cacheKey, [], $cacheTtl);

        $result = $clientMock->isBlacklisted($email);

        $this->assertTrue($result);
    }
}
