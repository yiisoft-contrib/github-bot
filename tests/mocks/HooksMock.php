<?php

namespace yiiunit\extensions\githubbot\mocks;

use Github\Api\Repo;
use Github\Api\Repository\Hooks;

/**
 * @author Boudewijn Vahrmeijer <info@dynasource.eu>
 */
class HooksMock extends Hooks
{
    /**
     * Mock for all()
     * @see Repo::all()
     */
    public function all($username, $repository)
    {
        return [
            [
                'type' => 'Repository',
                'id' => '12345',
                'name' => 'web',
                'active' => 1,
                'events' => [
                    0 => 'push'
                ],
                'config' => [
                    'content_type' => 'json',
                    'insecure_ssl' => 0,
                    'secret' => '********',
                    'url' => 'http://www.domain.com/payload',
                ],
                'updated_at' => 'Repository',
                'created_at' => 'Repository',
                'url' => 'Repository',
                'test_url' => 'Repository',
                'ping_url' => 'Repository',
                'last_response' => [
                    'code' => 422,
                    'status' => 'misconfigured',
                    'message' => 'Invalid HTTP Response: 404'
                ]
            ]
        ];
    }

}
