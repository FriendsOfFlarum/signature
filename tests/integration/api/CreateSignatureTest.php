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

class CreateSignatureTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-signature');

        $this->prepareDatabase([
            User::class => [
                $this->normalUser(),
                ['id' => 3, 'username' => 'normal2', 'email' => 'normal2@machine.local', 'is_email_confirmed' => true],
                ['id' => 4, 'username' => 'moderator', 'email' => 'moderator@machine.local', 'is_email_confirmed' => true],
                ['id' => 5, 'username' => 'normal3', 'email' => 'normal3@machine.local', 'is_email_confirmed' => true],
            ],
            Group::class => [
                ['id' => 5, 'name_singular' => 'TestSig', 'name_plural' => 'TestSigs', 'color' => '#FF0000', 'icon' => 'fas fa-user'],
            ],
            'group_permission' => [
                ['permission' => 'haveSignature', 'group_id' => 5],
                ['permission' => 'haveSignature', 'group_id' => 4],
                ['permission' => 'moderateSignature', 'group_id' => 4],
            ],
            'group_user' => [
                ['user_id' => 5, 'group_id' => 5],
                ['user_id' => 4, 'group_id' => 4],
            ],
        ]);

        // The bundled default-permissions migration grants `haveSignature` to
        // all members. Revoke that default here so the seeded group permissions
        // fully determine which users may have a signature of their own.
        $this->database()->table('group_permission')
            ->where('permission', 'haveSignature')
            ->where('group_id', Group::MEMBER_ID)
            ->delete();
    }

    #[Test]
    public function user_cannot_create_signature_without_permission()
    {
        $response = $this->send(
            $this->request(
                'PATCH',
                '/api/users/2',
                [
                    'authenticatedAs' => 2,
                    'json'            => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(403, $response->getStatusCode(), 'User without permission can create signature');

        $user = User::find(2);

        $this->assertNull($user->signature);
    }

    #[Test]
    public function user_can_create_signature_with_permission()
    {
        $response = $this->send(
            $this->request(
                'PATCH',
                '/api/users/5',
                [
                    'authenticatedAs' => 5,
                    'json'            => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('signature', $json['data']['attributes'], 'Creating a signature failed');
        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);
        $this->assertEquals('This is my signature', $json['data']['attributes']['signatureHtml']);

        $user = User::find(5);

        $this->assertEquals('<t>This is my signature</t>', $user->signature);
    }

    #[Test]
    public function user_cannot_create_signature_for_other_user()
    {
        $response = $this->send(
            $this->request(
                'PATCH',
                '/api/users/2',
                [
                    'authenticatedAs' => 5,
                    'json'            => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(403, $response->getStatusCode(), 'Expecting a permission denied 403');

        $user = User::find(2);

        $this->assertNull($user->signature);
    }

    #[Test]
    public function user_with_permission_can_create_signature_for_other_user_who_can_have_signature()
    {
        $response = $this->send(
            $this->request(
                'PATCH',
                '/api/users/5',
                [
                    'authenticatedAs' => 4,
                    'json'            => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertArrayHasKey('data', $json, 'Expecting a data key to be returned');
        $this->assertArrayHasKey('signature', $json['data']['attributes'], 'Expecting a signature to be returned');
        $this->assertEquals('This is my signature', $json['data']['attributes']['signature']);
        $this->assertEquals('This is my signature', $json['data']['attributes']['signatureHtml']);

        $user = User::find(5);

        $this->assertEquals('<t>This is my signature</t>', $user->signature);
    }

    #[Test]
    public function user_with_permission_can_create_signature_for_other_user_who_cannot_have_signature()
    {
        $response = $this->send(
            $this->request(
                'PATCH',
                '/api/users/2',
                [
                    'authenticatedAs' => 4,
                    'json'            => [
                        'data' => [
                            'attributes' => [
                                'signature' => 'This is my signature',
                            ],
                        ],
                    ],
                ]
            )
        );

        // A moderator (moderateSignature) may set a signature for another user
        // regardless of whether that user can have one themselves.
        $this->assertEquals(200, $response->getStatusCode());

        $user = User::find(2);

        $this->assertEquals('<t>This is my signature</t>', $user->signature);
    }

    #[Test]
    public function moderator_can_edit_signature_of_user_who_cannot_have_signature()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/2', ['authenticatedAs' => 4])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        // A moderator can edit any user's signature, so the edit UI is offered
        // even for a user who cannot have a signature of their own.
        $this->assertTrue($json['data']['attributes']['canEditSignature']);
    }

    #[Test]
    public function moderator_can_edit_signature_of_user_who_can_have_signature()
    {
        $response = $this->send(
            $this->request('GET', '/api/users/5', ['authenticatedAs' => 4])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);

        $this->assertTrue($json['data']['attributes']['canEditSignature']);
    }

    #[Test]
    public function image_syntax_is_not_counted_when_no_formatter_is_enabled()
    {
        // No formatting extension (Markdown/BBCode) is enabled in this test, so
        // image markup stays plain text and never produces an <IMG> tag. The
        // image limit must therefore be a no-op rather than wrongly rejecting
        // text that merely looks like image markup.
        $response = $this->send(
            $this->request('PATCH', '/api/users/5', [
                'authenticatedAs' => 5,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'signature' => '![one](https://example.com/1.png) ![two](https://example.com/2.png) ![three](https://example.com/3.png)',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody());
    }
}
