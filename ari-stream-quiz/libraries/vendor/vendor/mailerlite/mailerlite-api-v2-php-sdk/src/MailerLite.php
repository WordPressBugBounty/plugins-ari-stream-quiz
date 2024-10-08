<?php

namespace MailerLiteApi;

use Http\Client\HttpClient;

use MailerLiteApi\Common\ApiConstants;
use MailerLiteApi\Common\RestClient;
use MailerLiteApi\Exceptions\MailerLiteSdkException;

class MailerLite {

    /**
     * @var null | string
     */
    protected $apiKey;

    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @param string|null $apiKey
     * @param HttpClient $client
     */
    public function __construct(
        $apiKey = null,
        HttpClient $httpClient = null
    ) {
        if (is_null($apiKey)) {
            throw new MailerLiteSdkException("API key is not provided");
        }

        $this->apiKey = $apiKey;

        $this->restClient = new RestClient(
            $this->getBaseUrl(),
            $apiKey,
            $httpClient
        );
    }

    /**
     * @return \MailerLiteApi\Api\Groups
     */
    public function groups()
    {
        return new \MailerLiteApi\Api\Groups($this->restClient);
    }

    /**
     * @return \MailerLiteApi\Api\Fields
     */
    public function fields()
    {
        return new \MailerLiteApi\Api\Fields($this->restClient);
    }

    /**
     * @return \MailerLiteApi\Api\Subscribers
     */
    public function subscribers()
    {
        return new \MailerLiteApi\Api\Subscribers($this->restClient);
    }

    /**
     * @return \MailerLiteApi\Api\Campaigns
     */
    public function campaigns()
    {
        return new \MailerLiteApi\Api\Campaigns($this->restClient);
    }

    /**
     * @return \MailerLiteApi\Api\Stats
     */
    public function stats()
    {
        return new \MailerLiteApi\Api\Stats($this->restClient);
    }

    public function getBaseUrl()
    {
        $isNewApi = $this->isNewApi();

        if ($isNewApi) {
            return ApiConstants::NEW_BASE_URL;
        }

        return ApiConstants::BASE_URL/* . $version . '/'*/;
    }

    public function isNewApi() {
        return strlen( $this->apiKey ) > 32;
    }
}