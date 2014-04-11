<?php

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\TestCase;
use ReflectionProperty;

class SystemDaemonHandlerTest extends TestCase
{
    public function logLevelsProvider()
    {
        return array(
            array(Logger::EMERGENCY, 'emerg'),
            array(Logger::ALERT, 'alert'),
            array(Logger::CRITICAL, 'crit'),
            array(Logger::ERROR, 'err'),
            array(Logger::WARNING, 'warning'),
            array(Logger::NOTICE, 'notice'),
            array(Logger::INFO, 'info'),
            array(Logger::DEBUG, 'debug'),
        );
    }

    /**
     * @dataProvider logLevelsProvider
     */
    public function testShouldLogUsingSystemDaemon($level, $methodName)
    {
        $record = $this->getRecord($level, 'test', array('data' => new \stdClass, 'foo' => 34));

        $systemDaemonMock = $this->getMockClass('System_Daemon', array($methodName));
        $systemDaemonMock::staticExpects($this->once())
            ->method($methodName)
            ->with('(test) test {"data":"[object] (stdClass: {})","foo":34} []');

        $handler = new SystemDaemonHandler();
        $reflection = new ReflectionProperty($handler, 'systemDaemonClassName');
        $reflection->setAccessible(true);
        $reflection->setValue($handler, $systemDaemonMock);

        $handler->handle($record);
    }

    /**
     * @expectedException OutOfBoundsException
     * @expectedExceptionMessage Unrecognized level "999"
     */
    public function testShouldThrowsAnExceptionWhenTryingToLogAnUnrecognizedLevel()
    {
        $record = $this->getRecord();
        $record['level'] = 999;

        $handler = new SystemDaemonHandler();
        $handler->handle($record);
    }
}
