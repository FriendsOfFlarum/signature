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

class SignatureImageLimitTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    public function setUp(): void
    {
        parent::setUp();

        // Markdown is what turns `![](…)` into an actual <IMG> tag; without a
        // formatting extension the image syntax stays plain text.
        $this->extension('flarum-markdown');
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
    public function signature_within_image_limit_is_accepted()
    {
        // Default signature.maximum_image_count is 2.
        $response = $this->patchSignature('![one](https://example.com/1.png) ![two](https://example.com/2.png)');

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody());
    }

    #[Test]
    public function signature_exceeding_image_limit_is_rejected()
    {
        $response = $this->patchSignature('![one](https://example.com/1.png) ![two](https://example.com/2.png) ![three](https://example.com/3.png)');

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertNull(User::find(5)->signature);
    }

    #[Test]
    public function remote_image_is_rejected_when_remote_images_are_disabled()
    {
        $this->setting('signature.allow_remote_images', false);

        $response = $this->patchSignature('![remote](https://example.com/1.png)');

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertNull(User::find(5)->signature);
    }

    #[Test]
    public function local_image_is_allowed_when_remote_images_are_disabled()
    {
        $this->setting('signature.allow_remote_images', false);

        // The test forum is served from http://localhost, so this image is local.
        $response = $this->patchSignature('![local](http://localhost/assets/sig.png)');

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody());
    }

    #[Test]
    public function whitelisted_host_is_allowed_when_remote_images_are_disabled()
    {
        $this->setting('signature.allow_remote_images', false);
        $this->setting('signature.allowed_image_hosts', "cdn.example.com\nimages.example.org");

        $response = $this->patchSignature('![cdn](https://cdn.example.com/sig.png)');

        $this->assertEquals(200, $response->getStatusCode(), $response->getBody());
    }

    #[Test]
    public function non_whitelisted_host_is_rejected_when_remote_images_are_disabled()
    {
        $this->setting('signature.allow_remote_images', false);
        $this->setting('signature.allowed_image_hosts', 'cdn.example.com');

        $response = $this->patchSignature('![other](https://not-allowed.example.net/sig.png)');

        $this->assertEquals(422, $response->getStatusCode());

        $this->assertNull(User::find(5)->signature);
    }
}
