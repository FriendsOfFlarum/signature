<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Event;

use Flarum\User\User;

abstract class AbstractSignatureEvent
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var User|null
     */
    public $actor;

    public function __construct(User $user, ?User $actor = null)
    {
        $this->user = $user;
        $this->actor = $actor;
    }
}
