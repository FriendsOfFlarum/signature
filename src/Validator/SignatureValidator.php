<?php

namespace FoF\Signature\Validator;

use Flarum\Foundation\AbstractValidator;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Validation\Factory;
use DOMDocument;
use FoF\Signature\Formatter\SignatureFormatter;
use Symfony\Contracts\Translation\TranslatorInterface;

class SignatureValidator extends AbstractValidator
{
    protected SettingsRepositoryInterface $settings;
    protected SignatureFormatter $formatter;

    public function __construct(Factory $validator, TranslatorInterface $translator, SettingsRepositoryInterface $settings, SignatureFormatter $formatter)
    {
        parent::__construct($validator, $translator);

        $this->settings = $settings;
        $this->formatter = $formatter;

        $this->validator->extend('signature_images', function ($attribute, $value, $parameters, $validator) {
            return $this->validateSignatureImages($value);
        });
    }

    protected function getRules()
    {
        return [
            'signature' => [
                'string',
                'max:' . $this->settings->get('signature.maximum_char_limit'),
                'signature_images',
            ],
        ];
    }

    private function validateSignatureImages(string $value): bool
    {
        $parsedContent = $this->formatter->parse($value);

        if ($parsedContent === '') {
            return true;
        }

        $document = new DOMDocument();

        // The parsed content is TextFormatter's XML representation, which may not
        // be a fully valid HTML document. Suppress libxml warnings while loading.
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML($parsedContent, LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $imageCount = $document->getElementsByTagName('img')->length;

        return $imageCount <= (int) $this->settings->get('signature.maximum_image_count');
    }
}
