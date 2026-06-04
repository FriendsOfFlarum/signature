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

use Flarum\Api\Context;
use Flarum\Api\Schema;
use Flarum\User\User;
use FoF\Signature\Event\SignatureSaved;
use FoF\Signature\Event\SignatureSaving;
use FoF\Signature\Formatter\SignatureFormatter;
use FoF\Signature\Validator\SignatureValidator;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;

class AddUserAttributes
{
    public function __construct(
        protected SignatureFormatter $formatter,
        protected SignatureValidator $validator,
        protected Dispatcher $events
    ) {
    }

    /**
     * @return \Tobyz\JsonApiServer\Schema\Field\Field[]
     */
    public function __invoke(): array
    {
        return [
            Schema\Str::make('signature')
                ->nullable()
                ->writable(fn (User $user, Context $context) => $context->getActor()->can('editSignature', $user))
                ->get(function (User $user) {
                    return $user->signature ? $this->formatter->unparse($user->signature) : null;
                })
                ->set(function (User $user, ?string $value, Context $context) {
                    $this->validator->assertValid(['signature' => $value]);

                    $signature = Str::of($value ?? '')->trim();
                    $user->signature = $signature->isEmpty() ? null : $this->formatter->parse((string) $signature);

                    if ($user->isDirty('signature')) {
                        $this->dispatchEvents($user, $context->getActor());
                    }
                }),

            Schema\Str::make('signatureHtml')
                ->visible(fn (User $user) => (bool) $user->signature)
                ->get(function (User $user) {
                    return $user->signature ? $this->formatter->render($user->signature) : null;
                }),

            Schema\Boolean::make('canEditSignature')
                ->get(fn (User $user, Context $context) => $context->getActor()->can('editSignature', $user)),

            Schema\Boolean::make('canHaveSignature')
                ->get(fn (User $user) => $user->hasPermission('haveSignature')),
        ];
    }

    protected function dispatchEvents(User $user, User $actor): void
    {
        $this->events->dispatch(new SignatureSaving($user, $actor->id === $user->id ? null : $actor));

        $user->afterSave(function (User $user) use ($actor) {
            $user->raise(new SignatureSaved($user, $actor->id === $user->id ? null : $actor));
        });
    }
}
