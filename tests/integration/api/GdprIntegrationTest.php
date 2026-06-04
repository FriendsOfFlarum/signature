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

use Carbon\Carbon;
use Flarum\Gdpr\Models\ErasureRequest;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use PHPUnit\Framework\Attributes\Test;

class GdprIntegrationTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('flarum-gdpr');
        $this->extension('fof-signature');

        $this->prepareDatabase([
            User::class => [
                $this->normalUser(),
                [
                    'id'                 => 3,
                    'username'           => 'normal2',
                    'password'           => '$2y$10$LO59tiT7uggl6Oe23o/O6.utnF6ipngYjvMvaxo1TciKqBttDNKim', // BCrypt hash for "too-obscure"
                    'email'              => 'normal2@machine.local',
                    'is_email_confirmed' => 1,
                    'last_seen_at'       => Carbon::now()->subSecond(),
                    'signature'          => '<t>This is a test signature for normal2.</t>',
                ],
            ],
            'gdpr_erasure' => [
                ['id' => 1, 'user_id' => 3, 'verification_token' => '123abc', 'status' => 'user_confirmed', 'reason' => 'I want to be forgotten', 'created_at' => Carbon::now(), 'user_confirmed_at' => Carbon::now()],
            ],
        ]);
    }

    #[Test]
    public function signature_is_anonymized()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/user-erasure-requests/1', [
                'authenticatedAs' => 1,
                'json'            => [
                    'data' => [
                        'type'       => 'user-erasure-requests',
                        'id'         => '1',
                        'attributes' => [
                            'processorComment' => 'I have processed this request',
                            'processedMode'    => ErasureRequest::MODE_ANONYMIZATION,
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody());

        $user = User::find(3);
        $this->assertNotNull($user);
        $this->assertEquals('Anonymous1', $user->username);
        $this->assertNull($user->signature);
    }
}
