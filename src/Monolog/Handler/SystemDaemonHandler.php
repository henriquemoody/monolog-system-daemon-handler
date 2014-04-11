<?php

namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use OutOfBoundsException;
use System_Daemon;

/**
 * System_Daemon (http://pear.php.net/System_Daemon) log handler.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 */
class SystemDaemonHandler extends AbstractProcessingHandler
{
    protected $levels = array(
        Logger::EMERGENCY => 'emerg',
        Logger::ALERT => 'alert',
        Logger::CRITICAL => 'crit',
        Logger::ERROR => 'err',
        Logger::WARNING => 'warning',
        Logger::NOTICE => 'notice',
        Logger::INFO => 'info',
        Logger::DEBUG => 'debug',
    );

    protected $systemDaemonClassName = 'System_Daemon';

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter('(%channel%) %message% %context% %extra%');
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $level = $record['level'];
        if (! isset($this->levels[$level])) {
            throw new OutOfBoundsException(sprintf('Unrecognized level "%s"', $level));
        }

        call_user_func(array($this->systemDaemonClassName, $this->levels[$level]), $record['formatted']);
    }
}
