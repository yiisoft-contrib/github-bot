<?php

namespace yiiunit\extensions\githubbot\mocks;
use app\commands\GithubController;

/**
 * Class GithubControllerMock
 * @package yiiunit\extensions\githubbot\mocks
 * @author Boudewijn Vahrmeijer <info@dynasource.eu>
 */
class GithubControllerMock extends GithubController
{
    /**
     * @var string output buffer.
     */
    private $stdOutBuffer = '';

    public function stdout($string)
    {
        $this->stdOutBuffer .= $string;
    }

    public function flushStdOutBuffer()
    {
        $result = $this->stdOutBuffer;
        $this->stdOutBuffer = '';
        return $result;
    }

}