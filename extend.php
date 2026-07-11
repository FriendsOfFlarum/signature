<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature;

use Flarum\Api\Resource\UserResource;
use Flarum\Extend;
use Flarum\Extension\Event\Disabled;
use Flarum\Extension\Event\Enabled;
use Flarum\User\User;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/u:username/signature', 'user.signature'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\ApiResource(UserResource::class))
        ->fields(Api\AddUserAttributes::class),

    // Rebuild the cached signature formatter when a formatting extension is
    // toggled, so signatures pick up the new BBCode/Markdown configuration.
    (new Extend\Event())
        ->listen(Enabled::class, Listener\FlushFormatterCache::class)
        ->listen(Disabled::class, Listener\FlushFormatterCache::class),

    (new Extend\Settings())
        ->default('signature.maximum_char_limit', 500)
        ->default('signature.maximum_image_count', 2)
        ->default('signature.allow_remote_images', true)
        ->default('signature.allowed_image_hosts', '')
        ->default('signature.allow_inline_editing', false)
        ->default('signature.classic_look', false)
        ->serializeToForum('allowInlineEditing', 'signature.allow_inline_editing', 'boolval')
        ->serializeToForum('classicLook', 'signature.classic_look', 'boolval'),

    (new Extend\Model(User::class))
        ->cast('signature', 'string'),

    (new Extend\Policy())
        ->modelPolicy(User::class, Access\UserPolicy::class),

    (new Extend\ServiceProvider())
        ->register(Provider\SignatureFormatterProvider::class),

    // Optional GDPR integration. The signature column is exported and cleared
    // by the core user data type; registering this type declares `signature`
    // as PII and surfaces it in the GDPR data-handling overview.
    (new Extend\Conditional())
        ->whenExtensionEnabled('flarum-gdpr', fn () => [
            (new \Flarum\Gdpr\Extend\UserData())
                ->addType(Data\SignatureData::class),
        ]),
];
