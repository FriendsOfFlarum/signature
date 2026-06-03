# Signature

[![Latest Stable Version](https://img.shields.io/packagist/v/fof/signature.svg)](https://packagist.org/packages/fof/signature)
[![Total Downloads](https://img.shields.io/packagist/dt/fof/signature.svg)](https://packagist.org/packages/fof/signature)

A [Flarum](https://flarum.org) extension that adds signature support to your forum. Users can add a signature to their profile which is displayed beneath their posts.

> Originally created by [Katos](https://github.com/katosdev/signature) and now maintained by [FriendsOfFlarum](https://github.com/FriendsOfFlarum).

## Features

- Per-group permission to allow signatures and to moderate other users' signatures.
- Configurable maximum character limit and maximum image count.
- Optional inline editing of signatures directly from a post.
- Respects Markdown and BBCode formatting when those extensions are enabled.

## Installation

```bash
composer require fof/signature
```

## Updating

```bash
composer update fof/signature
php flarum cache:clear
```

### Migrating from `katosdev/signature`

This extension is a continuation of `katosdev/signature`. To switch over:

```bash
composer remove katosdev/signature
composer require fof/signature
php flarum migrate
php flarum cache:clear
```

Existing signatures are preserved during the migration.

## Links

- [Packagist](https://packagist.org/packages/fof/signature)
- [GitHub](https://github.com/FriendsOfFlarum/signature)
- [Discuss](https://discuss.flarum.org/d/28108)

## License

This extension is licensed under the [MIT License](LICENSE.md).
