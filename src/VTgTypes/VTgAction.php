<?php

class VTgAction
{
    public int $action = 0;
    public $chatId;
    public string $text;
    public array $extraParameters = [];
    public callable $handler = null;

    const ACTION__DO_NOTHING = 0;
    const ACTION__SEND_MESSAGE = 1;
    const ACTION__CALL_HANDLER = 2;

    public function __construct(int $action, ...$parameters = null) {
        $this->action = $action;
        if($this->action == self::ACTION__DO_NOTHING)
            return;
        if ($this->action == self::ACTION__SEND_MESSAGE) {
            $this->chatId = $parameters[0];
            $this->text = $parameters[1];
            $this->extraParameters = $parameters[2] ?? [];
            return;
        }
        if($this->action == self::ACTION__CALL_HANDLER) {
            $this->handler = $parameters[0];
            return;
        }
    }

    public function callHandler(...$args = null) 
    {
        if($this->action == self::ACTION__CALL_HANDLER)
            ($this->handler)(...$args);
    }
}