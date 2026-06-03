<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Api;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use FoF\Signature\Formatter\SignatureFormatter;

class AddUserAttributes
{
    public function __construct(protected SignatureFormatter $formatter)
    {
    }

    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        $attributes['signature'] = $user->signature ? $this->formatter->unparse($user->signature) : null;
        if ($user->signature) {
            $attributes['signatureHtml'] = $this->formatter->render($user->signature);
        }

        $actor = $serializer->getActor();

        $attributes['canEditSignature'] = $actor->can('editSignature', $user);
        $attributes['canHaveSignature'] = $user->hasPermission('haveSignature');

        return $attributes;
    }
}
