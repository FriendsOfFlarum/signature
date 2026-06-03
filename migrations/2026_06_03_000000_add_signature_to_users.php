<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        // Fresh install: add a nullable signature column.
        if (!$schema->hasColumn('users', 'signature')) {
            $schema->table('users', function (Blueprint $table) {
                $table->text('signature')->nullable();
            });

            return;
        }

        // The column already exists (e.g. when migrating from katosdev/signature,
        // where it was created as a non-nullable TEXT column). Ensure it is nullable
        // without dropping it, so existing signatures are preserved.
        $schema->table('users', function (Blueprint $table) {
            $table->text('signature')->nullable()->change();
        });
    },
    'down' => function (Builder $schema) {
        if ($schema->hasColumn('users', 'signature')) {
            $schema->table('users', function (Blueprint $table) {
                $table->dropColumn('signature');
            });
        }
    },
];
