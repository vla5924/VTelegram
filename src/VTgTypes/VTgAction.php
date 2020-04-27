<?php

class VTgAction
{
    public int $action = 0;
    public $chatId;
    public $messageId;
    public $inlineMessageId;
    public string $text;
    public array $extraParameters = [];
    public callable $handler = null;

    const ACTION__DO_NOTHING = 0;
    const ACTION__SEND_MESSAGE = 1;
    const ACTION__EDIT_MESSAGE = 2;
    const ACTION__EDIT_REPLY_MARKUP = 3;
    const ACTION__EDIT_INLINE_MESSAGE = 4;
    const ACTION__CALL_FUNCTION = 100;


    public function __construct(int $action, ...$parameters = null)
    {
        $this->action = $action;
        if ($this->action == self::ACTION__DO_NOTHING)
            return;
        if ($this->action == self::ACTION__SEND_MESSAGE) {
            $this->chatId = $parameters[0];
            $this->text = $parameters[1];
            $this->extraParameters = $parameters[2] ?? [];
            return;
        }
        if ($this->action == self::ACTION__EDIT_MESSAGE) {
            $this->chatId = $parameters[0];
            $this->messageId = $parameters[1];
            $this->text = $parameters[2];
            $this->extraParameters = $parameters[3] ?? [];
            return;
        }
        if ($this->action == self::ACTION__EDIT_REPLY_MARKUP) {
            $this->chatId = $parameters[0];
            $this->messageId = $parameters[1];
            $this->extraParameters = ['reply_markup' => $parameters[2]];
            return;
        }
        if ($this->action == self::ACTION__EDIT_INLINE_MESSAGE) {
            $this->inlineMessageId = $parameters[0];
            $this->text = $parameters[1];
            $this->extraParameters = $parameters[2] ?? [];
            return;
        }
        if ($this->action == self::ACTION__CALL_FUNCTION) {
            $this->handler = $parameters[0];
            return;
        }
    }

    public function callFunctionHandler(...$args = null)
    {
        if ($this->action == self::ACTION__CALL_FUNCTION) ($this->handler)(...$args);
    }

    static public function doNothing(): VTgAction
    {
        return new VTgAction(self::ACTION__DO_NOTHING);
    }

    static public function sendMessage($chatId, string $text, array $extraParameters = []): VTgAction
    {
        return new VTgAction(self::ACTION__SEND_MESSAGE, $chatId, $text, $extraParameters);
    }

    static public function editMessage($chatId, int $messageId, string $text, array $extraParameters = []): VTgAction
    {
        return new VTgAction(self::ACTION__EDIT_MESSAGE, $chatId, $messageId, $text, $extraParameters);
    }

    static public function editReplyMarkup($chatId, int $messageId, string $replyMarkup): VTgAction
    {
        return new VTgAction(self::ACTION__EDIT_REPLY_MARKUP, $chatId, $messageId, $replyMarkup);
    }

    static public function editInlineMessage(string $inlineMessageId, string $text, array $extraParameters = []): VTgAction
    {
        return new VTgAction(self::ACTION__EDIT_INLINE_MESSAGE, $inlineMessageId, $text, $extraParameters);
    }

    static public function callFunction(callable $handler): VTgAction
    {
        return new VTgAction(self::ACTION__CALL_FUNCTION, $handler);
    }
}
