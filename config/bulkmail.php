<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bulk Mail Sending
    |--------------------------------------------------------------------------
    |
    | Tuning knobs for large campaigns (e.g. 70,000 recipients). These values
    | are read by the campaign dispatcher and the SendEmailJob rate limiter.
    |
    */

    // Hard ceiling on how many recipients a single campaign may target.
    'max_campaign_size' => (int) env('BULK_MAX_CAMPAIGN_SIZE', 100000),

    // How many recipients the dispatcher pulls from the DB per chunk while
    // fanning out SendEmailJob instances. Keeps memory flat for 70k+ lists.
    'dispatch_chunk' => (int) env('BULK_DISPATCH_CHUNK', 500),

    // Soft throttle applied to the "emails" queue so we stay under the
    // provider's send rate. Amazon SES production default is 14/sec (=840/min);
    // we keep headroom. On a Redis-backed cache this is exact; on the file
    // cache it is a best-effort cap (SES also throttles + jobs retry on 454).
    'rate_per_minute' => (int) env('BULK_RATE_PER_MINUTE', 700),
];
