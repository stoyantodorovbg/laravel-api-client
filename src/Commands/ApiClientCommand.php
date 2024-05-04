<?php

namespace Stoyantodorov\ApiClient\Commands;

use Illuminate\Console\Command;

class ApiClientCommand extends Command
{
    public $signature = 'api-client';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
