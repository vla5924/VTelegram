<?php

/**
 * @brief Class provides basic interaction with Telegram Bot API
 * @details Use new extended class VTgRequestor to have general parameters and methods native
 */
class VTelegram
{
    /**
     * @var string $token
     * @brief Bot API token
     * @details Bot API token is stored for making requests
     */
    protected $token;

    /**
     * @var array $proxySettings
     * @brief Array with proxy settings
     * @details Parameters to connect to Telegram API via proxy if needed
     */
    protected $proxySettings = [];

    const API_HOST = 'https://api.telegram.org'; ///< URL of API used in requests

    /**
     * @brief Constructor-initializer
     * @param string $token Bot API token
     */
    public function __construct(string $token = "")
    {
        $this->token = $token;
    }

    /**
     * @brief Updates stored Bot API token
     * @param string $token New Bot API token
     */
    public function updateToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @brief Enables SOCKS5 proxy
     * @details Requests to Bot API will be made via proxy server
     * @param string $address Proxy HTTP address
     * @param string $port Connection port
     * @param string $username Username for proxy authentication
     * @param string $password Password for proxy authentication
     */
    public function enableProxy(string $address, string $port, string $username, string $password): void
    {
        $this->proxySettings = [
            'address' => $address,
            'port' => $port,
            'username' => $username,
            'password' => $password
        ];
    }

    /**
     * @brief Disables SOCKS5 proxy if enabled
     */
    public function disableProxy(): void
    {
        $this->proxySettings = [];
    }

    /**
     * @brief Calls Bot API method
     * @details Makes a request to Bot API with specified method
     * @param string $method Name of method to be called
     * @param string $parameters Array of method parameters if needed
     * @return array JSON-decoded array with result of request from Telegram
     * @todo Less bulky curl usage in this method (maybe some wrappers will be added in the future)...
     */
    public function callMethod(string $method, array $parameters = []): array
    {
        $apiUrl = self::API_HOST . '/bot' . $this->token;
        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $apiUrl . '/' . $method);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        if ($this->proxySettings) {
            curl_setopt($handler, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($handler, CURLOPT_PROXY, $this->proxySettings['address'] . ':' . $this->proxySettings['port']);
            curl_setopt($handler, CURLOPT_PROXYUSERPWD, $this->proxySettings['username'] . ':' . $this->proxySettings['password']);
            curl_setopt($handler, CURLOPT_HEADER, false);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
        }
        $curlResult = curl_exec($handler);
        return json_decode($curlResult, true);
    }
}
