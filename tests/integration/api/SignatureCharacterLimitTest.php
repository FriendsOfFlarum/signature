<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Tests\integration\api;

use Flarum\Group\Group;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use PHPUnit\Framework\Attributes\Test;

class SignatureCharacterLimitTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-signature');

        $this->prepareDatabase([
            User::class => [
                ['id' => 5, 'username' => 'normal3', 'email' => 'normal3@machine.local', 'is_email_confirmed' => true],
            ],
            Group::class => [
                ['id' => 5, 'name_singular' => 'TestSig', 'name_plural' => 'TestSigs', 'color' => '#FF0000', 'icon' => 'fas fa-user'],
            ],
            'group_permission' => [
                ['permission' => 'haveSignature', 'group_id' => 5],
            ],
            'group_user' => [
                ['user_id' => 5, 'group_id' => 5],
            ],
        ]);
    }

    private function patchSignature(string $signature)
    {
        return $this->send(
            $this->request('PATCH', '/api/users/5', [
                'authenticatedAs' => 5,
                'json'            => ['data' => ['attributes' => ['signature' => $signature]]],
            ])
        );
    }

    #[Test]
    public function signature_within_the_character_limit_is_accepted()
    {
        $this->setting('signature.maximum_char_limit', 10);

        $response = $this->patchSignature(str_repeat('a', 10));

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody());
    }

    #[Test]
    public function signature_exceeding_the_character_limit_is_rejected()
    {
        $this->setting('signature.maximum_char_limit', 10);

        $response = $this->patchSignature(str_repeat('a', 11));

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertNull(User::find(5)->signature);
    }
}
