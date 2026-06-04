<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Data;

use Flarum\Gdpr\Data\Type;

class SignatureData extends Type
{
    public static function dataType(): string
    {
        return 'signature';
    }

    public function export(): ?array
    {
        // No action, data included in core user export.
        return null;
    }

    public function anonymize(): void
    {
        // No action, the signature column is cleared by the core user data type.
    }

    public function delete(): void
    {
        // Nothing to do, the user table row is deleted by the core user data type.
    }

    public static function exportDescription(): string
    {
        return static::staticTranslator()->trans('flarum-gdpr.lib.data.default_user_action');
    }

    public static function anonymizeDescription(): string
    {
        return static::staticTranslator()->trans('flarum-gdpr.lib.data.default_user_action');
    }

    public static function deleteDescription(): string
    {
        return static::staticTranslator()->trans('flarum-gdpr.lib.data.default_user_action');
    }

    public static function piiFields(): array
    {
        return ['signature'];
    }
}
