# survos/lingua-contracts

Shared contracts for communication between Survos Lingua clients and servers.

This package defines the **stable wire-level API** used when applications communicate with a Lingua translation server.

## What lives here

- Request / response DTOs
- API route and header constants
- Small enums or value objects that are part of the public protocol

This package deliberately contains:
- no framework code
- no storage models
- no business logic

## Intended usage

Both sides of the integration depend on this package:

- **Clients** (via `lingua-bundle`) use it to build requests
- **Servers** use it to parse and respond consistently

```php
# survos/lingua-core

Core, framework-agnostic utilities shared by the Survos translation ecosystem.

This package contains **canonical identity and hashing logic** used by:
- Lingua server
- Lingua client bundle
- Babel bundle
- Translation workflows

It exists to guarantee that **source keys and translation keys are computed identically everywhere**, avoiding subtle duplication and cache misses.

## What lives here

- Deterministic hashing / key generation
- Locale and engine normalization
- Small, pure-PHP helpers required across packages

This package deliberately has:
- no Symfony dependencies
- no Doctrine dependencies
- no I/O or HTTP code

## Usage

```php
use Survos\Lingua\Core\Identity\HashUtil;

$sourceKey = HashUtil::calcSourceKey('Hello world', 'en');
$translationKey = HashUtil::calcTranslationKey($sourceKey, 'es', 'libre');
```

