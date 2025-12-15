# Lingua Core

**Lingua Core** is a small, framework-agnostic PHP library that defines the *canonical identity model* for translatable strings.

It is intentionally boring.

There are **no translators**, **no HTTP clients**, **no queues**, and **no storage** here.  
Lingua Core exists to guarantee that every part of a multilingual system agrees on:

- how source strings are identified
- how translations are keyed
- how locales are normalized
- how hashes are derived (and remain stable)

This bundle is the lowest layer used by higher-level systems such as **Babel**, **Lingua**, and translation servers.

---

## What Lingua Core Is

Lingua Core provides:

- **Deterministic hash generation** for:
  - source strings
  - translation keys
- **Locale normalization**
- **Stable identity guarantees** across:
  - databases
  - queues
  - JSONL caches
  - HTTP boundaries
  - multiple applications

If two systems use Lingua Core, they will compute **the same keys** for the same inputs.

---

## What Lingua Core Is *Not*

Lingua Core deliberately does **not** include:

- Translation engines (DeepL, LibreTranslate, etc.)
- Symfony services or configuration
- Doctrine entities
- Database access
- HTTP clients
- Caching
- CLI commands

Those belong in higher layers.

---

## Core Concepts

### Source Key (`str_hash`)

A **source key** uniquely identifies a string *in its source language*.

It is derived from:

- the original text
- the source locale

<pre>php
use Survos\Lingua\Core\Identity\HashUtil;

$strHash = HashUtil::calcSourceKey(
    'Museum of Things',
    'en'
);
</pre>

This key is:

- deterministic
- content-based
- stable across systems
- safe to persist and share

---

### Translation Key (`tr_hash`)

A **translation key** identifies a translation *of a specific source string* into a target locale.

It is derived from:

- the source key
- the target locale
- an optional namespace (engine or logical domain)

<pre>php
$trHash = HashUtil::calcTranslationKey(
    $strHash,
    'es',
    'babel'
);
</pre>

Namespaces allow multiple translation systems to coexist without schema changes.

---

### Locale Normalization

Locales are normalized consistently to avoid mismatches:

<pre>php
HashUtil::normalizeLocale('EN-us'); // 'en'
HashUtil::normalizeLocale('pt_BR'); // 'pt'
</pre>

This ensures that `en`, `en_US`, and `en-us` do not silently diverge.

---

## Why Hash-Based Identity?

Lingua Core uses **content-addressed identity** rather than numeric IDs because:

- strings may originate in different systems
- strings may be discovered lazily
- translation may happen asynchronously
- systems may not share a database

A hash-based key lets you:

- insert without coordination
- upsert safely
- merge datasets
- cache translations in files
- move work across queues or services

---

## Typical Usage (Higher Layers)

Lingua Core is usually consumed indirectly.

Examples:

- **Babel Bundle**  
  https://github.com/survos/babel-bundle  
  Uses source keys for `str.hash` and translation keys for `str_translation.hash`.

- **Lingua Bundle**  
  https://github.com/survos/lingua-bundle  
  Implements translation workflows, batching, dispatch, and integration with external engines.

- **Translation Servers / Workers**  
  Consume Lingua Core hashes to translate at scale without sharing database IDs.

- **JSONL Translation Caches**  
  Use hashes as stable primary keys for offline or file-based translation storage.

- **CLI / Batch Pipelines**  
  Operate on hashes instead of database IDs to remain portable.

---

## Design Guarantees

Lingua Core guarantees:

- **No breaking changes to hash algorithms** without a major version
- **Pure functions** (no I/O, no globals)
- **Framework independence**
- **Cross-language portability** (algorithms are trivial to reimplement elsewhere)

---

## Versioning Policy

- Patch releases: bug fixes only
- Minor releases: additive helpers
- Major releases: identity semantics may change (rare, explicit)

If a hash algorithm ever changes, it will be treated as a **data migration event**, not a refactor.

---

## Installation

<pre>bash
composer require survos/lingua-core
</pre>

Lingua Core has **no required dependencies** beyond PHP itself.

---

## Philosophy

> Translation is not about strings.  
> It is about identity.

Lingua Core exists so everything else can be opinionated, flexible, and replaceable—without ever disagreeing on *what* is being translated.
