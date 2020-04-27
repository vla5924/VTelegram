<?php

/**
 * @brief Class provides an interface for interaction with Telegram Bot API
 */
class VTelegram
{
    /**
     * @brief Bot API token
     * @details Bot API token is stored for making requests
     */
    private $token;

    /**
     * @brief Array with proxy settings
     * @details Parameters to connect to Telegram API via proxy if needed
     */
    private $proxySettings = [];

    /**
     * @brief Default parameters for requests
     * @details Some default parameters added to requests that are used only if not specified in methods
     */
    private $defaultParameters = [];

    const API_HOST = 'https://api.telegram.org'; ///< URL of API used in requests

    const PARSE_MODE__TEXT = 0;     ///< Marker used for disabling parsing in messages
    const PARSE_MODE__MARKDOWN = 1; ///< Marker used for enabling parsing messages as Markdown
    const PARSE_MODE__HTML = 2;     ///< Marker used for enabling parsing messages as HTML

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
     * @brief Updates default parse mode
     * @details Changes default parse mode to passed so it will be used when sending and editing messages
     * @param int $parseModeCode New default parse mode code (can be 0, 1 or 2)
     */
    public function setParseMode(int $parseModeCode): void
    {
        switch ($parseModeCode) {
            case self::PARSE_MODE__MARKDOWN:
                $this->defaultParameters['parse_mode'] = 'Markdown';
                break;
            case self::PARSE_MODE__HTML:
                $this->defaultParameters['parse_mode'] = 'HTML';
                break;
            default:
                if (isset($this->defaultParameters['parse_mode']))
                    unset($this->defaultParameters['parse_mode']);
        }
    }

    /**
     * @brief Calls Bot API method
     * @details Makes a request to Bot API with specified method
     * @param string $method Name of method to be called
     * @param string $parameters Array of method parameters if needed
     * @return array JSON-decoded array with result of request from Telegram
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

    /**
     * @brief Sends a message via bot
     * @details Wrapper for callMethod() for sending a message to user from the bot
     * @param string $chatId Numeric destination chat identifier
     * @param string $text Message body
     * @param array $extraParameters Extra or special (not defaulted) paremeters if needed
     * @return array JSON-decoded array with result of request from Telegram
     */
    public function sendMessage(string $chatId, string $text, array $extraParameters = []): array
    {
        $parameters = [
            'chat_id' => $chatId,
            'text' => $text,
            'disable_web_page_preview' => true
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        return $this->callMethod('sendMessage', $parameters);
    }

    /**
     * @brief Edits bot message
     * @details Wrapper for callMethod() for editing a regular message sent by the bot
     * @param string $chatId Numeric destination chat identifier
     * @param string $messageId Identifier of message from bot to edit
     * @param string $text Message new body
     * @param array $extraParameters Extra or special (not defaulted) paremeters if needed
     * @return array JSON-decoded array with result of request from Telegram
     */
    public function editMessage(string $chatId, int $messageId, string $text, array $extraParameters = []): array
    {
        $parameters = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'disable_web_page_preview' => true
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        return $this->callMethod('editMessageText', $parameters);
    }

    /**
     * @brief Edits bot inline message
     * @details Wrapper for callMethod() for editing an "inline-mode" message sent via the bot
     * @param string $inlineMessageId Identifier of message sent via the bot to edit
     * @param string $text Message new body
     * @param string $replyMarkup Reply markup (with inline keyboard) of needed
     * @return array JSON-decoded array with result of request from Telegram
     */
    public function editInlineMessage(string $inlineMessageId, string $text, string $replyMarkup = ""): array
    {
        $parameters = [
            'inline_message_id' => $inlineMessageId,
            'text' => $text,
            'disable_web_page_preview' => true
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if ($replyMarkup)
            $parameters["reply_markup"] = $replyMarkup;
        return $this->callMethod('editMessageText', $parameters);
    }

    /**
     * @brief Triggers an answer to callback query
     * @details Wrapper for callMethod() for answering to callback queries from users
     * @param string $callbackQueryId Identifier of query to answer
     * @param string $text Notification message text if needed
     * @param bool $showAlert If true, a pop-up window instead of toast will be shown to user (default false)
     * @return array JSON-decoded array with result of request from Telegram
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = "", bool $showAlert = false): array
    {
        $parameters = [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert
        ];
        return $this->callMethod('answerCallbackQuery', $parameters);
    }
}
