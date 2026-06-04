<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Listener;

use Flarum\Extension\Event\Disabled;
use Flarum\Extension\Event\Enabled;
use FoF\Signature\Formatter\SignatureFormatter;

class FlushFormatterCache
{
    /**
     * Extensions whose enabled state changes the compiled signature formatter.
     *
     * @see SignatureFormatter::getConfigurator()
     */
    protected const FORMATTING_EXTENSIONS = ['flarum-markdown', 'flarum-bbcode'];

    public function __construct(protected SignatureFormatter $formatter)
    {
    }

    public function handle(Enabled|Disabled $event): void
    {
        // The signature formatter is compiled once and cached forever. When a
        // formatting extension is toggled the cached formatter would otherwise
        // keep its old configuration, so flush it to force a rebuild.
        if (in_array($event->extension->getId(), self::FORMATTING_EXTENSIONS, true)) {
            $this->formatter->flush();
        }
    }
}
