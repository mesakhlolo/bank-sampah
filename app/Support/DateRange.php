<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Helpers for building index-friendly date-range boundaries.
 *
 * Why this exists
 * ---------------
 * `transaction_date` is declared as a DATE column, but the models cast it to
 * `datetime` (`'transaction_date' => 'datetime'`). Eloquent therefore *stores*
 * `'2026-11-30 00:00:00'`, not `'2026-11-30'`.
 *
 * `whereDate()` compares `date(column) <= '2026-11-30'`, which is correct but wraps
 * the column in a function, so no index can be used. Replacing it with a plain
 * `where('transaction_date', '<=', '2026-11-30')` restores the index but silently
 * drops the final day of the range, because SQLite compares the two as strings and
 * `'2026-11-30 00:00:00' > '2026-11-30'`.
 *
 * The fix is to compare against the real boundaries of the day: the start is
 * `Y-m-d 00:00:00` and the end is `Y-m-d 23:59:59`. Both are plain string
 * comparisons, so the index still applies, and both are inclusive.
 */
class DateRange
{
    /**
     * First instant of the given day, as `Y-m-d 00:00:00`.
     */
    public static function startOfDay(CarbonInterface|string $date): string
    {
        return self::parse($date)->startOfDay()->format('Y-m-d H:i:s');
    }

    /**
     * Last instant of the given day, as `Y-m-d 23:59:59`.
     */
    public static function endOfDay(CarbonInterface|string $date): string
    {
        return self::parse($date)->endOfDay()->format('Y-m-d H:i:s');
    }

    private static function parse(CarbonInterface|string $date): CarbonInterface
    {
        return $date instanceof CarbonInterface ? $date : \Carbon\Carbon::parse($date);
    }
}
