<?php

namespace yiiunit\extensions\githubbot\mocks;

use Github\Exception\RuntimeException;
use Github\HttpClient\CachedHttpClient;
use Yii;

/**
 * Class CachedHttpClientMock
 * @package yiiunit\extensions\githubbot\mocks
 * @author Boudewijn Vahrmeijer <info@dynasource.eu>
 */
class CachedHttpClientMock extends CachedHttpClient
{
    const DUMMY_TOKEN = 'correct-token';
    const EXCEPTION_BAD_CREDENTIALS_MSG = 'Bad credentials';
    const EXCEPTION_BAD_CREDENTIALS_CODE = 401;

    /**
     * @inheritdoc
     */
    public function request($path, $body = null, $httpMethod = 'GET', array $headers = array(), array $options = array())
    {
        if (Yii::$app->params['github_token'] !== self::DUMMY_TOKEN) {
            throw new RuntimeException(self::EXCEPTION_BAD_CREDENTIALS_MSG, self::EXCEPTION_BAD_CREDENTIALS_CODE);
        } else {
            $response = new \Guzzle\Http\Message\Response(200, [], []);
            return $response;
        }
    }
}
