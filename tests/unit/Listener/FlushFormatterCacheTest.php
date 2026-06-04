<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Tests\unit\Listener;

use Flarum\Extension\Event\Disabled;
use Flarum\Extension\Event\Enabled;
use Flarum\Extension\Extension;
use Flarum\Testing\unit\TestCase;
use FoF\Signature\Formatter\SignatureFormatter;
use FoF\Signature\Listener\FlushFormatterCache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class FlushFormatterCacheTest extends TestCase
{
    #[Test]
    public function flushes_when_markdown_is_toggled()
    {
        $formatter = Mockery::mock(SignatureFormatter::class);
        $formatter->shouldReceive('flush')->once();

        (new FlushFormatterCache($formatter))->handle(new Enabled($this->extension('flarum-markdown')));
    }

    #[Test]
    public function flushes_when_bbcode_is_toggled()
    {
        $formatter = Mockery::mock(SignatureFormatter::class);
        $formatter->shouldReceive('flush')->once();

        (new FlushFormatterCache($formatter))->handle(new Disabled($this->extension('flarum-bbcode')));
    }

    #[Test]
    public function does_not_flush_for_unrelated_extensions()
    {
        $formatter = Mockery::mock(SignatureFormatter::class);
        $formatter->shouldNotReceive('flush');

        (new FlushFormatterCache($formatter))->handle(new Enabled($this->extension('some-other-extension')));
    }

    private function extension(string $id): Extension
    {
        $extension = Mockery::mock(Extension::class);
        $extension->shouldReceive('getId')->andReturn($id);

        return $extension;
    }
}
