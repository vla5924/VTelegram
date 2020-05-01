<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTgRequestController.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgResult.php';

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
     * @brief Default parameters for requests
     * @details Some default parameters added to requests that are used only if not specified in methods
     */
    protected $defaultParameters = [];

    const PARSE_MODE__TEXT = 0;     ///< Marker used for disabling parsing in messages
    const PARSE_MODE__MARKDOWN = 1; ///< Marker used for enabling parsing messages as Markdown
    const PARSE_MODE__HTML = 2;     ///< Marker used for enabling parsing messages as HTML

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
            'disable_web_page_preview' => true
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('sendMessage', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to forward messages of any kind
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param string $fromChatId Unique identifier for the chat where the original message was sent (or channel @username)
     * @param int @messageId Message identifier in the chat specified in $fromChatId
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
     * @param mixed|string $photo Photo to send (pass file_id or HTTP URL as string, or upload a new photo)
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult Sent message as VTgMessage on success
     * @todo Uploading and InputFile usage
     */
    public function sendPhoto(string $chatId, $photo, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'photo' => $photo
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('sendPhoto', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to send audio files
     * @details Use this method to send audio files, if you want Telegram clients to display them in the music player. 
     * Your audio must be in the .MP3 or .M4A format.
     * @note For sending voice messages, use the sendVoice().
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param mixed|string $audio Audio to send (pass file_id or HTTP URL as string, or upload a new audio file)
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult Sent message as VTgMessage on success
     * @todo Uploading and InputFile usage
     */
    public function sendAudio(string $chatId, $audio, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'chat_id' => $chatId,
            'audio' => $audio
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('sendAudio', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to edit text and game messages
     * @details Wrapper for callMethod() for editing a regular message sent by the bot
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param int $messageId Identifier of the message to edit
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult If edited message is sent by the bot, VTgMessage is returned, otherwise true
     */
    public function editMessage(string $chatId, int $messageId, string $text, array $extraParameters = []): VTgResult
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
        return VTgResult::fromData($this->callMethod('editMessageText', $parameters), 'VTgMessage');
    }

    /**
     * @brief Use this method to edit inline messages
     * @details Wrapper for callMethod() for editing a regular message sent by the bot
     * @param string $inlineMessageId Identifier of the inline message
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return VTgResult If edited message is sent by the bot, VTgMessage is returned, otherwise true
     */
    public function editInlineMessage(string $inlineMessageId, string $text, array $extraParameters = []): VTgResult
    {
        $parameters = [
            'inline_message_id' => $inlineMessageId,
            'text' => $text,
            'disable_web_page_preview' => true
        ];
        if (isset($this->defaultParameters['parse_mode']))
            $parameters['parse_mode'] = $this->defaultParameters['parse_mode'] ?? null;
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        return VTgResult::fromData($this->callMethod('editMessageText', $parameters), 'VTgMessage');
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
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
        $result = $this->callMethod('answerCallbackQuery', $parameters);
        if($result['ok'])
            return new VTgResult(true);
        return VTgResult::fromData($result);
    }
}