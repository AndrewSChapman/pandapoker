<?php

namespace Testing\Functional;

use App\Domains\Shared\Helper\JsonHelper;
use App\Domains\Shared\Http\Type\HttpRequestType;
use App\Domains\Shared\Http\Type\HttpRequestUri;
use App\Domains\Shared\Persistence\DataStore\DataStoreInterface;
use App\Domains\Shared\Persistence\DataStore\RedisDataStore;
use App\Domains\Shared\Security\SecuritySingleton;
use App\Domains\Shared\Security\Service\TokenService\ValueObject\TokenInfo;
use App\Domains\User\Type\AnimalType;
use Faker\Provider\Person;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class FunctionalTest extends TestCase
{
    /** @var DataStoreInterface */
    private $dataStore;

    /** @var TokenInfo */
    private $forcedTokenInfo = null;

    protected function makeJsonHttpRequest(
        HttpRequestType $requestType,
        HttpRequestUri $uri,
        array $jsonData,
        array $headers = []
    ): ResponseInterface {
        // Send the request
        $client = $this->getHttpClient();

        $headers = $this->addBearerTokenToRequestHeaders($headers);

        return $client->request((string)$requestType, (string)$uri, [
            'json' => $jsonData,
            'headers' => $headers
        ]);
    }

    protected function makeHttpRequest(
        HttpRequestType $requestType,
        HttpRequestUri $uri,
        array $headers = []
    ): ResponseInterface {
        // Send the request
        $client = $this->getHttpClient();

        $headers = $this->addBearerTokenToRequestHeaders($headers);

        return $client->request((string)$requestType, (string)$uri, [
            'headers' => $headers
        ]);
    }

    protected function getDataStore(): DataStoreInterface
    {
        if (!$this->dataStore) {
            $this->dataStore = new RedisDataStore();
        }

        return $this->dataStore;
    }

    protected function clearDataStore(): void
    {
        $this->getDataStore()->flush();
    }

    protected function login(): TokenInfo
    {
        if (SecuritySingleton::hasTokenInfo()) {
            return SecuritySingleton::getTokenInfo();
        }

        $response = $this->makeJsonHttpRequest(new HttpRequestType(HttpRequestType::POST), new HttpRequestUri('/user'), [
            'username' => Person::firstNameFemale() . ' ' . Person::randomAscii(),
            'totem_animal' => (string)(new AnimalType(AnimalType::CAT))
        ]);

        if ($response->getStatusCode() != Response::HTTP_OK) {
            throw new \Exception('FunctionalTest::login - failed to login!');
        }

        $responseData = JsonHelper::jsonStringToArray((string)$response->getBody());

        $tokenInfo = TokenInfo::fromArray($responseData);

        SecuritySingleton::setTokenInfo($tokenInfo);

        return $tokenInfo;
    }

    /**
     * @return Client
     * @throws Exception
     */
    private function getHttpClient(): Client
    {
        $appUrl = env('APP_URL', '');
        if (empty($appUrl)) {
            throw new Exception('APP_URL not found in environment file!');
        }

        return new Client(['base_uri' => $appUrl]);
    }

    private function addBearerTokenToRequestHeaders(array $headers): array
    {
        if ($this->forcedTokenInfo) {
            $headers['Authorization'] = 'Bearer ' . (string)$this->forcedTokenInfo->getToken();
        } else if (SecuritySingleton::hasTokenInfo()) {
            $tokenInfo = SecuritySingleton::getTokenInfo();
            $headers['Authorization'] = 'Bearer ' . (string)$tokenInfo->getToken();
        }

        return $headers;
    }

    public function setForcedTokenInfo(?TokenInfo $forcedTokenInfo): void
    {
        $this->forcedTokenInfo = $forcedTokenInfo;
    }
}
