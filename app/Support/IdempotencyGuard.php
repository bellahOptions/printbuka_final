<?php

namespace App\Support;

/**
 * Prevents duplicate record creation from a double-click, a slow-network
 * retry, or a user resubmitting a form they weren't sure went through.
 *
 * Usage: the form includes a hidden `idempotency_key` input generated once
 * client-side (see resources/js/app.js — wireIdempotencyKey) when the page
 * loads. That same key is resent on every attempt to submit that page's
 * form, including retries — so the second attempt is recognized as a replay
 * of the first, not a new entry. Reloading the page (a genuine new attempt)
 * generates a fresh key, so real duplicate entries are never blocked.
 *
 * A model using this needs a nullable, unique `idempotency_key` string
 * column — see database/migrations/..._add_idempotency_key_to_finance_entries_table.php
 * for the reference migration.
 */
class IdempotencyGuard
{
    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    public static function alreadyProcessed(string $modelClass, ?string $key): ?\Illuminate\Database\Eloquent\Model
    {
        if (! filled($key)) {
            return null;
        }

        return $modelClass::query()->where('idempotency_key', $key)->first();
    }
}
