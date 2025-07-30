<?php

namespace VnCoder\Core\Console;
use Illuminate\Console\Command;
use VnCoder\Helper\DatabaseHelper;

class RunGitCommand extends Command
{
    protected $signature = 'git:commit {message?*}';
    protected $description = "Git Command : php artisan git {commit|update} {message?*}";

    public function handle()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            system('cls');
        } else {
            system('clear');
        }
        $dbHelper = new DatabaseHelper();
        $dbHelper->saveCurrentDatabase();
        $this->gitCommit();
    }

    protected function gitCommit(){
        $message = $this->argument('message');
        $message = implode(" ", $message);
        if(!$message){
            $message = "Update code";
        }
        $this->info("Git Command: " . $message);
        system('git add . -A');
        system('git commit -m "'.$message.'"');
        system('git push origin ' . env('GIT_BRANCH', 'master'));
    }

}