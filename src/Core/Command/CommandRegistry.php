<?php

namespace Timpack\TvshowTracker\Core\Command;

class CommandRegistry
{

    /**
     * @var CommandRegistry
     */
    private static $instance;

    /**
     * @var array
     */
    protected $_registry = [];

    /**
     * @param string|array $command
     */
    public function pushCommand($command)
    {
        if (is_array($command)) {
            foreach ($command as $class) {
                $this->pushCommand($class);
            }
        } elseif (!in_array($command, $this->_registry)) {
            $this->_registry[] = $command;
        }
    }

    /**
     * @return CommandRegistry
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new CommandRegistry();
        }
        return self::$instance;
    }

    /**
     * @return array
     */
    public function getCommands()
    {
        return $this->_registry;
    }

}