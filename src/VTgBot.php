<?php

require_once VTELEGRAM_REQUIRE_DIR . '/VTelegram.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgTypes/VTgUpdate.php';
require_once VTELEGRAM_REQUIRE_DIR . '/VTgTypes/VTgAction.php';

class VTgBot
{
    static protected VTelegram $tg = new VTelegram();
    static protected array $commands = [];
    static protected callable $standardMessageHadler = null;

    /**
     * @brief Updates stored Bot API token for VTelegram instance
     * @param string $token New Bot API token
     */
    static public final function setToken(string $token): void
    {
        static::$tg->updateToken($token);
    }

    /**
     * @brief Enables SOCKS5 proxy for VTelegram instance
     * @details Requests to Bot API will be made via proxy server
     * @param string $address Proxy HTTP address
     * @param string $port Connection port
     * @param string $username Username for proxy authentication
     * @param string $password Password for proxy authentication
     */
    public function enableProxy(string $address, string $port, string $username, string $password): void
    {
        $this->tg->enableProxy($address, $port, $username, $password);
    }

    /**
     * @brief Disables SOCKS5 proxy for VTelegram instance if enabled
     */
    public function disableProxy(): void
    {
        $this->tg->disableProxy();
    }

    static public final function registerCommand(string $command, callable $handler): void
    {
        static::$commands[$command] = $handler;
    }

    static public final function registerStandardMessageHandler(callable $handler): void
    {
        self::$standardMessageHadler = $handler;
    }

    static public final function processUpdateData(array $data)
    {
        $update = new VTgUpdate($data);
        static::handleUpdate($update);
    }

    static protected final function handleUpdate(VTgUpdate $update)
    {
        if($update->type == VTgUpdate::TYPE__MESSAGE)
            self::handleMessage($update->message);
    }

    static protected final function handleMessage(VTgMessage $message)
    {
        $action = new VTgAction(VTgAction::ACTION__DO_NOTHING);
        $containsCommand = ($message->text and $message->text[0] == '/');
        if ($containsCommand && !empty(self::$commands)) {
            $data = explode(' ', $message->text, 2);
            $command = substr($data[0], 1);
            $action = self::handleCommand($message, $command, $data[1]);
        } else {
            $action = (self::$standardMessageHadler)($message);
        }
        if ($action->action == VTgAction::ACTION__SEND_MESSAGE) {
            $data = $this->tg->sendMessage($action->chatId, $action->text, $action->extraParameters);
            if ($data['ok'])
                return new VTgMessage($data['message']);
        }
    }

    static protected final function handleCommand(VTgMessage $message, string $command, string $data = ""): VTgAction
    {
        if (!isset(self::$commands[$command]))
            return new VTgAction(VTgAction::ACTION__DO_NOTHING);
        return (self::$commands[$command])($message, $data);
    }
}
