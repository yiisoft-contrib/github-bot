<?php

namespace yiiunit\extensions\githubbot\mocks;

use Github\Api\Repo;

/**
 * Class RepoMock
 * @package yiiunit\extensions\githubbot\mocks
 * @author Boudewijn Vahrmeijer <info@dynasource.eu>
 */
class RepoMock extends Repo
{
    public function hooks()
    {
        return new HooksMock($this->client);
    }
}
