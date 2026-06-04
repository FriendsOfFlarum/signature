<?php

/*
 * This file is part of fof/signature.
 *
 * Copyright (c) FriendsOfFlarum.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FoF\Signature\Validator;

use Flarum\Foundation\AbstractValidator;
use Flarum\Foundation\Config;
use Flarum\Locale\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use FoF\Signature\Formatter\SignatureFormatter;
use Illuminate\Validation\Factory;
use s9e\TextFormatter\Utils;

class SignatureValidator extends AbstractValidator
{
    public function __construct(Factory $validator, TranslatorInterface $translator, protected SettingsRepositoryInterface $settings, protected SignatureFormatter $formatter, protected Config $config)
    {
        parent::__construct($validator, $translator);

        $this->validator->extend('signature_images', function ($attribute, $value, $parameters, $validator) {
            return $this->validateImageCount($value);
        });

        $this->validator->extend('signature_remote_images', function ($attribute, $value, $parameters, $validator) {
            return $this->validateRemoteImages($value);
        });
    }

    protected function getRules(): array
    {
        return [
            'signature' => [
                'string',
                'max:'.$this->settings->get('signature.maximum_char_limit'),
                'signature_images',
                'signature_remote_images',
            ],
        ];
    }

    protected function getMessages(): array
    {
        return [
            'signature.signature_images'        => $this->translator->trans('fof-signature.forum.errors.max_image_count_exceed'),
            'signature.signature_remote_images' => $this->translator->trans('fof-signature.forum.errors.remote_images_not_allowed'),
        ];
    }

    private function validateImageCount(string $value): bool
    {
        // `parse()` returns TextFormatter's intermediate representation, which is
        // always valid XML. Images (from Markdown or BBCode) are represented as
        // <IMG> tags, so we can count them directly with TextFormatter's own
        // utility rather than coercing the XML through an HTML parser.
        $imageCount = count(Utils::getAttributeValues($this->formatter->parse($value), 'IMG', 'src'));

        return $imageCount <= (int) $this->settings->get('signature.maximum_image_count');
    }

    private function validateRemoteImages(string $value): bool
    {
        if ((bool) $this->settings->get('signature.allow_remote_images', true)) {
            return true;
        }

        $allowedHosts = $this->allowedImageHosts();

        foreach (Utils::getAttributeValues($this->formatter->parse($value), 'IMG', 'src') as $src) {
            $host = parse_url($src, PHP_URL_HOST);

            // Relative URLs (no host) point at the forum itself.
            if (empty($host)) {
                continue;
            }

            // Any host that isn't the forum's own or an explicitly allowed one
            // (e.g. a CDN) is off-site and would leak viewers' IPs to a third
            // party, so the signature is rejected.
            if (!in_array(strtolower($host), $allowedHosts, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Hosts whose images are permitted when remote images are disabled: the
     * forum's own host plus any admin-configured hosts (typically a CDN).
     *
     * @return string[]
     */
    private function allowedImageHosts(): array
    {
        $hosts = [strtolower($this->config->url()->getHost())];

        $configured = (string) $this->settings->get('signature.allowed_image_hosts', '');

        // The setting is a free-form list separated by commas and/or newlines.
        // Entries may be bare hostnames or full URLs.
        foreach (preg_split('/[\s,]+/', $configured, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $entry) {
            $host = parse_url($entry, PHP_URL_HOST) ?: $entry;
            $hosts[] = strtolower($host);
        }

        return array_values(array_unique(array_filter($hosts)));
    }
}
