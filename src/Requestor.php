<?php

namespace VTg;

use VTg\RequestController;
use VTg\MetaObjects\Result;
use VTg\MetaObjects\InputFile;

/**
 * @brief Class provides full interface for interaction with Telegram Bot API
 * @details Phylosophy of methods is all required parameters must be passed as arguments
 * to functions, all optional parameters may be passed in additional array. In duet with
 * detailed documentaion recognized by IDEs this makes code wrote with VTgRequestor more
 * concise without loss of clarity.
 * @todo Wrappers for all methods of Telegram Bot API
 */
class Requestor extends RequestController
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

    const CHAT_ACTION__TYPING = 'typing';
    const CHAT_ACTION__UPLOAD_PHOTO = 'upload_photo';
    const CHAT_ACTION__RECORD_VIDEO = 'record_video';
    const CHAT_ACTION__UPLOAD_VIDEO = 'upload_video';
    const CHAT_ACTION__RECORD_AUDIO = 'record_audio';
    const CHAT_ACTION__UPLOAD_AUDIO = 'upload_audio';
    const CHAT_ACTION__UPLOAD_DOCUMENT = 'upload_document';
    const CHAT_ACTION__FIND_LOCATION = 'find_location';
    const CHAT_ACTION__RECORD_VIDEO_NOTE = 'record_video_note';
    const CHAT_ACTION__UPLOAD_VIDEO_NOTE = 'upload_video_note';

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

    /**
     * @brief Merges arrays if $extraMarameters is not empty
     * @param [out] array $parameters Destination array
     * @param array $extraParameters Array to merge from
     */
    protected function mergeExtraParameters(array &$parameters, array $extraParameters = []): void
    {
        if (!empty($extraParameters))
            $parameters = array_merge($parameters, $extraParameters);
    }

    /**
     * @brief Use this method to receive incoming updates using long polling
     * @param array $parameters Parameters if needed
     * @return Result On success, UpdatesArray returned
     */
    public function getUpdates(array $parameters = []): Result
    {
        return Result::fromData($this->callMethod('getUpdates', $parameters), '\VTg\Objects\UpdatesArray');
    }

    /**
     * @brief Use this method to specify a url and receive incoming updates via an outgoing webhook
     * @details If you'd like to make sure that the Webhook request comes from Telegram, it is
     * recommended using a secret path in the URL, e.g. https://www.example.com/[TOKEN]. Since
     * nobody else knows your bot's token, you can be pretty sure it's Telegram.
     * @param string $url HTTPS url to send updates to (use an empty string to remove webhook integration)
     * @param array $extraParameters Other parameters if needed
     * @return Result On success, true is returned
     */
    public function setWebhook(string $url, array $extraParameters = []): Result
    {
        $parameters = [
            'url' => $url
        ];
        if (isset($extraParameters['allowed_updates']) and gettype($extraParameters['allowed_updates']) === "array")
            $extraParameters['allowed_updates'] = json_encode($extraParameters['allowed_updates']);
        $this->mergeExtraParameters($parameters, $extraParameters);
        $result = $this->callMethod('setWebhook', $parameters);
        if ($result['ok'])
            return new Result(true);
        return Result::fromData($result);
    }

    /**
     * @brief Use this method to remove webhook integration if you decide to switch back to getUpdates()
     * @return Result On success, true is returned
     */
    public function deleteWebhook(): Result
    {
        $result = $this->callMethod('deleteWebhook');
        if ($result['ok'])
            return new Result(true);
        return Result::fromData($result);
    }

    /**
     * @brief A simple method for testing your bot's auth token
     * @return Result Basic information about the bot as User object
     */
    public function getMe(): Result
    {
        return Result::fromData($this->callMethod('getMe'), '\VTg\Objects\User');
    }

    /**
     * @brief Use this method to send text messages
     * @details Wrapper for callMethod()
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param string $text Message body
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendMessage(string $chatId, string $text, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'text' => $text,
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM, self::DISABLE_WEB_PAGE_PREVIEW__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendMessage', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to forward messages of any kind
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param string $fromChatId Unique identifier for the chat where the original message was sent (or channel @username)
     * @param int $messageId Message identifier in the chat specified in $fromChatId
     * @param bool $disableNotification If true, message will be forwarded silently (without a notification for users)
     * @return Result Sent message as Message on success
     */
    public function forwardMessage(string $chatId, string $fromChatId, int $messageId, bool $disableNotification = false): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'from_chat_id' => $fromChatId,
            'message_id' => $messageId,
            'disable_notification' => $disableNotification
        ];
        return Result::fromData($this->callMethod('forwardMessage', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send photos
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $photo Photo to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendPhoto(string $chatId, InputFile $photo, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'photo' => $photo->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendPhoto', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send audio files
     * @details Use this method to send audio files, if you want Telegram clients to display them in the music player. 
     * Your audio must be in the .MP3 or .M4A format.
     * @note For sending voice messages, use the sendVoice().
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $audio Audio to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendAudio(string $chatId, InputFile $audio, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'audio' => $audio->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendAudio', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send documents
     * @details Use this method to send general files
     * @note Bots can currently send files of any type of up to 50 MB in size.
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $document Document to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendDocument(string $chatId, InputFile $document, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'document' => $document->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendDocument', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send MP4-videos
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $video Video to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendVideo(string $chatId, InputFile $video, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'video' => $video->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendVideo', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send animation files (GIF or H.264/MPEG-4 AVC video without sound)
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $animation Animation file to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendAnimation(string $chatId, InputFile $animation, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'animation' => $animation->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendAnimation', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send voice messages
     * @details Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message.
     * @note Your audio must be in an .OGG file encoded with OPUS
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $voice Audio file to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendVoice(string $chatId, InputFile $voice, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'voice' => $voice->get()
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendVoice', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send video messages
     * @details Telegram clients support rounded square mp4 videos of up to 1 minute long.
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param InputFile $videoNote Video file to send
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendVideoNote(string $chatId, InputFile $videoNote, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'video_note' => $videoNote->get()
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendVideoNote', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to send point on the map
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param float $latitude Latitude of the location
     * @param float $longitude Longitude of the location
     * @param array $extraParameters Other parameters if needed
     * @return Result Sent message as Message on success
     */
    public function sendLocation(string $chatId, float $latitude, float $longitude, array $extraParameters = []): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('sendLocation', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method when you need to send chat action
     * @details Use this method when you need to tell the user that something is happening on the bot's side
     * @note The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status).
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param string $action Type of action to broadcast
     * @return Result Sent message as Message on success
     */
    public function sendChatAction(string $chatId, string $action): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'action' => $action
        ];
        return Result::fromData($this->callMethod('sendChatAction', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to get basic info about a file and prepare it for downloading
     * @details For the moment, bots can download files of up to 20MB in size.
     * @warning It is guaranteed that the link will be valid for at least 1 hour. 
     * When the link expires, a new one can be requested by calling getFile() again.
     * @note This function may not preserve the original file name and MIME type. 
     * You should save the file's MIME type and name (if available) when the File object is received.
     * @param string $fileId file_id of file stored on Telegram servers
     * @return Result Sent message as Message on success
     */
    public function getFile(string $fileId): Result
    {
        $parameters = [
            'file_id' => $fileId
        ];
        return Result::fromData($this->callMethod('getFile', $parameters), '\VTg\Objects\File');
    }

    /**
     * @brief Use this method to send answers to callback queries sent from inline keyboards
     * @details The answer will be displayed to the user as a notification at the top of the chat screen or as an alert
     * @param string $callbackQueryId Identifier of query to answer
     * @param array $extraParameters Other parameters if needed
     * @return Result True on success
     */
    public function answerCallbackQuery(string $callbackQueryId, array $extraParameters = []): Result
    {
        $parameters = [
            'callback_query_id' => $callbackQueryId,
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        $result = $this->callMethod('answerCallbackQuery', $parameters);
        if ($result['ok'])
            return new Result(true);
        return Result::fromData($result);
    }

    /**
     * @brief Use this method to edit text and game messages
     * @details Wrapper for callMethod() for editing a text message sent by the bot
     * @note This method corresponds to 'optionality' as declared in Telegram API documentation.
     * See wrappers for this method like editMessageText() etc. for more convenience.
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return Result If edited message is sent by the bot, Message is returned, otherwise true
     */
    public function editMessageTextStd(string $text, array $extraParameters = []): Result
    {
        $parameters = [
            'text' => $text,
        ];
        $this->applyDefaultParameters($parameters, self::PARSE_MODE__PARAM, self::DISABLE_WEB_PAGE_PREVIEW__PARAM);
        $this->mergeExtraParameters($parameters, $extraParameters);
        return Result::fromData($this->callMethod('editMessageText', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to edit text and game messages
     * @details This is a wrapper for editMessageTextStd()
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param int $messageId Identifier of the message to edit
     * @param string $text New text of the message
     * @param array $extraParameters Other parameters if needed
     * @return Result If edited message is sent by the bot, Message is returned, otherwise true
     */
    public function editMessageText(string $chatId, int $messageId, string $text, array $extraParameters = []): Result
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
     * @return Result If edited message is sent by the bot, Message is returned, otherwise true
     */
    public function editIMessageText(string $inlineMessageId, string $text, array $extraParameters = []): Result
    {
        $parameters = [
            'inline_message_id' => $inlineMessageId,
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        return $this->editMessageTextStd($text, $parameters);
    }

    /**
     * @brief Use this method to edit only the reply markup of messages
     * @details Wrapper for callMethod() for editing a text message sent by the bot
     * @note This method corresponds to 'optionality' as declared in Telegram API documentation.
     * See wrappers for this method like editMessageReplyMarkup() etc. for more convenience.
     * @param array $parameters Parameters if needed
     * @return Result If edited message is sent by the bot, Message is returned, otherwise true
     */
    public function editMessageReplyMarkupStd(array $parameters = []): Result
    {
        return Result::fromData($this->callMethod('editMessageReplyMarkup', $parameters), '\VTg\Objects\Message');
    }

    /**
     * @brief Use this method to edit only the reply markup of messages
     * @details This is a wrapper for editMessageReplyMarkupStd()
     * @param int|string $chatId Unique identifier for the target chat or @username of the target channel
     * @param int $messageId Identifier of the message to edit
     * @param string|bool $replyMarkup New reply markup or false to remove it
     * @return Result If edited message is sent by the bot, Message is returned, otherwise true
     */
    public function editMessageReplyMarkup(string $chatId, int $messageId, $replyMarkup = false): Result
    {
        $parameters = [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ];
        if ($replyMarkup)
            $parameters['reply_markup'] = $replyMarkup;
        return $this->editMessageReplyMarkupStd($parameters);
    }

    /**
     * @brief Use this method to edit only the reply markup of messages
     * @details This is a wrapper for editMessageReplyMarkupStd()
     * @param string $inlineMessageId Identifier of the inline message
     * @param string|bool $replyMarkup New reply markup or false to remove it
     * @return Result If edited message is sent by the bot, Message is returned, otherwise true
     */
    public function editIMessageReplyMarkup(string $inlineMessageId, $replyMarkup = false): Result
    {
        $parameters = [
            'inline_message_id' => $inlineMessageId
        ];
        if ($replyMarkup)
            $parameters['reply_markup'] = $replyMarkup;
        return $this->editMessageReplyMarkupStd($parameters);
    }

    /**
     * @brief Use this method to send answers to an inline query
     * @details No more than 50 results per query are allowed.
     * @param string $inlineQueryId Unique identifier for the answered query
     * @param array $results Array of results - BaseIQR or children objects - for the inline query
     * @param array $extraParameters Other parameters if needed
     * @return Result On success, true is returned
     */
    public function answerInlineQuery(string $inlineQueryId, array $results, array $extraParameters = []): Result
    {
        foreach ($results as &$result)
            $result = $result->toArray();
        $parameters = [
            'inline_query_id' => $inlineQueryId,
            'results' => json_encode($results)
        ];
        $this->mergeExtraParameters($parameters, $extraParameters);
        $result = $this->callMethod('answerInlineQuery', $parameters);
        if ($result['ok'])
            return new Result(true);
        return Result::fromData($result);
    }
}
