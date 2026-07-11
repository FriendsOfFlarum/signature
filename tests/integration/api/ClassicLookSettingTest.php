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

use Flarum\Testing\integration\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ClassicLookSettingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->extension('fof-signature');
    }

    #[Test]
    public function classic_look_is_disabled_by_default(): void
    {
        $response = $this->send($this->request('GET', '/api'));

        $this->assertSame(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertFalse($payload['data']['attributes']['classicLook']);
    }

    #[Test]
    public function classic_look_setting_is_serialized_to_the_forum(): void
    {
        $this->setting('signature.classic_look', true);

        $response = $this->send($this->request('GET', '/api'));

        $this->assertSame(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertTrue($payload['data']['attributes']['classicLook']);
    }
}
