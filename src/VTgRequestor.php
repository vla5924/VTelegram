<?php

require_once __DIR__ . '/VTgRequestController.php';
require_once __DIR__ . '/VTgMetaObjects/VTgResult.php';
require_once __DIR__ . '/VTgMetaObjects/VTgFile.php';

/**
 * @brief Class provides full interface for interaction with Telegram Bot API
 * @details Phylosophy of methods is all required parameters must be passed as arguments
 * to functions, all optional parameters may be passed in additional array. In duet with
 * detailed documentaion recognized by IDEs this makes code wrote with VTgRequestor more
 * concise without loss of clarity.
 * @todo Wrappers for all methods of Telegram Bot API, including media and inline
 */
class VTgRequestor extends VTgRequestController
{
    /**
     * @var array $defaultParameters
     * @brief Default parameters for text messages
     * @details Some default parameters added to requests that are used only if not specified in methods
     */
    protected $defaultParameters = [];

    const PARSE_MODE__PARAM    = 'parse_mode'; ///< Parse mode parameter name
    const PARSE_MODE__DISABLED = '';           ///< Marker used for disabling parsing in messages
    const PARSE_MODE__MARKDOWN = 'Markdown';   ///< Marker used for enabling parsing messages as Markdown
    const PARSE_MODE__HTML     = 'HTML';       ///< Marker used for enabling parsing messages as HTML

    const DISABLE_WEB_PAGE_PREVIEW__PARAM = 'disable_web_page_preview'; ///< Disable web page preview parameter name

    /**
     * @brief Updates default parse mode
     * @details Changes default parse mode to passed so it will be used when sending and editing messages
     * @param string $parseMode New default parse mode name
     */
    public function setParseMode(string $parseMode): void
    {
        switch ($parseMode) {
            case self::PARSE_MODE__MARKDOWN:
                $this->defaultParameters[self::PARSE_MODE__PARAM] = self::PARSE_MODE__MARKDOWN;
                break;
            case self::PARSE_MODE__HTML:
                $this->defaultParameters[self::PARSE_MODE__PARAM] = self::PARSE_MODE__HTML;
                break;
            default:
                if (isset($this->defaultParameters[self::PARSE_MODE__PARAM]))
                    unset($this->defaultParameters[self::PARSE_MODE__PARAM]);
        }
    }

    /**
     * @brief Updates default disabling web page preview state
     * @param bool $value Parameter value
     */
    public function setDisableWebPagePreview(bool $value = true): void
    {
        $this->defaultParameters[self::DISABLE_WEB_PAGE_PREVIEW__PARAM] = $value;
    }

    /**
     * @brief Adds or changes default parameter
     * @param string $name Parameter name
     * @param mixed $value Parameter value
     */
    public function setDefaultParameter(string $name, $value = true): void
    {
        $this->defaultParameters[$name] = $value;
    }

    /**
     * @brief Removes default parameter if set
     * @param string $name Parameter name
     */
    public function unsetDefaultParameter(string $name): void
    {
        if (isset($this->defaultParameters[$name]))
            unset($this->defaultParameters[$name]);
    }

    /**
     * @brief Adds default values for given parameter names if presented
     * @param array $parameters Destination array
     * @param string $paramNames Any parameter names to check
     */
    protected function applyDefaultParameters(array &$parameters, string ...$paramNames): void
    {
        foreach ($paramNames as $name) {
            if (isset($this->defaultParameters[$name]))
                $parameters[$name] = $this->defaultParameters[$name];
        }
    }

    protected function mergeExtraParameters(array &$parameters, array $extraParameters = []): void
    {
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
    }

    /**
     * @brief A simple method for testing your bot's auth token
     * @return VTgResult Basic information about the bot as VTgUser object
     */
    public function getMe(): VTgResult
    {
        return VTgResult::fromData($this->callMethod('getMe'), 'VTgUser');
    }

    /**
     * @brief Use this method to send text messages
     * @details Wrapper for callMethod()
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param string $text Message body
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult Sent message as VTgMessage on success
     */
    public function sendMessage(string $chatId, string $text, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'text' => $text,
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM, self::DISABLE_WEB_PAGE_PREVIEW__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('sendMessage', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to forward messages of any kind
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param string $fromChatId Unique identifier for the chat where the original message was sent (or channel @username)
     * @param int $messageId Message identifier in the chat specified in $fromChatId
     * @param bool $disableNotification If true, message will be forwarded silently (without a notification for users)
     * @return VTgResult Sent message as VTgMessage on success
     */
    public function forwardMessage(string $chatId, string $fromChatId, int $messageId, bool $disableNotification = false): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
            'disable_notification' => $disableNotification
        ];
        return VTgResult::fromData($this->callMethod('forwardMessage', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to send photos
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param VTgFile $photo Photo to send (pass file_id or HTTP URL as string, or upload a new photo)
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult Sent message as VTgMessage on success
     * @todo Uploading and InputFile usage
     */
    public function sendPhoto(string $chatId, VTgFile $photo, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'photo' => $photo->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('sendPhoto', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to send audio files
     * @details Use this method to send audio files, if you want Telegram clients to display them in the music player. 
     * Your audio must be in the .MP3 or .M4A format.
     * @note For sending voice messages, use the sendVoice().
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param VTgFile $audio Audio to send (pass file_id or HTTP URL as string, or upload a new audio file)
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult Sent message as VTgMessage on success
     * @todo Uploading and InputFile usage
     */
    public function sendAudio(string $chatId, VTgFile $audio, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'audio' => $audio->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('sendAudio', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to edit text and game messages
     * @details Wrapper for callMethod() for editing a text message sent by the bot
     * @note This method corresponds to 'optionality' as declared in Telegram API documentation.
     * See wrappers for this method like editMessageText() etc. for more convenience.
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult If edited message is sent by the bot, VTgMessage is returned, otherwise true
     */
    public function editMessageTextStd(string $text, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'text' => $text,
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM, self::DISABLE_WEB_PAGE_PREVIEW__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('editMessageText', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to edit text and game messages
     * @details This is a wrapper for editMessageTextStd()
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param int $messageId Identifier of the message to edit
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult If edited message is sent by the bot, VTgMessage is returned, otherwise true
     */
    public function editMessageText(string $chatId, int $messageId, string $text, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        return $this->editMessageTextStd($text, $parameters);
    }

    /**
     * @brief Use this method to edit inline messages
     * @details This is a wrapper for editMessageTextStd()
     * @param string $inlineMessageId Identifier of the inline message
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult If edited message is sent by the bot, VTgMessage is returned, otherwise true
     */
    public function editInlineMessageText(string $inlineMessageId, string $text, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'inline_message_id' => $inlineMessageId,
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        return $this->editMessageTextStd($text, $parameters);
    }

    /**
     * @brief Use this method to send answers to callback queries sent from inline keyboards
     * @details The answer will be displayed to the user as a notification at the top of the chat screen or as an alert
     * @param string $callbackQueryId Identifier of query to answer
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult True on success
     */
    public function answerCallbackQuery(string $callbackQueryId, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'callback_query_id' => $callbackQueryId,
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        $result = $this->callMethod('answerCallbackQuery', $parameters);
        if ($result['ok'])
            return new VTgResult(true);
        return VTgResult::fromData($result);
    }
}
