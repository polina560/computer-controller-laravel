<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MoonShine\Laravel\Models\MoonshineUser;

class CreateMoonShineUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moonshine:create-user {--u|username= : Username} {--N|name= : Name} {--p|password= : Password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = $this->option('username') ?? config('moonshine.default_username');
        $name = $this->option('name') ?? config('moonshine.default_name');
        $password = $this->option('password') ?? config('moonshine.default_password');

        if (MoonshineUser::where('email', $username)->exists()) {
            $this->info('User already exists!');

            return 0;
        }

        $this->call('moonshine:user', [
            '--username' => $username,
            '--name' => $name,
            '--password' => $password,
            '--no-interaction' => true,
        ]);

        return 0;
    }
}
