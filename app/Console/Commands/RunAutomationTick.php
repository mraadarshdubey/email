<?php

namespace App\Console\Commands;

use App\Services\AutomationTickService;
use Illuminate\Console\Command;

class RunAutomationTick extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'automations:tick';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due Sequence steps, Follow-ups, and RSS feed checks (run every minute via cron)';

    public function handle(AutomationTickService $tick): int
    {
        $tick->run();
        $this->info('Automation tick complete.');

        return self::SUCCESS;
    }
}
