<?php

namespace VnCoder\Core\Console;

use Illuminate\Console\Command;

class VnCoderComposer extends Command
{
    protected $signature = 'composer:update';
    protected $description = "Call from Composer Update";

    public function handle()
    {
        $this->updateSymlink();
    }

    protected function updateSymlink()
    {
        // Upload folder
        $uploadFolder = storage_path('uploads');
        $uploadFolderSymlink = PUBLIC_PATH . 'uploads';
        if (!is_dir($uploadFolderSymlink)) {
            echo "Symlink folder " . $uploadFolder . " to ". $uploadFolderSymlink . "\n";
            symlink($uploadFolder, $uploadFolderSymlink);
        }

        // static core asset folder
        $assetsFolder = VNCODER_CORE_PATH . 'assets';
        $assetsFolderSymlink = PUBLIC_PATH . 'core';
        if (!is_dir($assetsFolderSymlink)) {
            echo "Symlink folder " . $assetsFolder . " to ". $assetsFolderSymlink . "\n";
            symlink($assetsFolder, $assetsFolderSymlink);
        }

        $frameworkGitIgnore = STORAGE_PATH . 'framework' . DIRECTORY_SEPARATOR . '.gitignore';
        $databaseGitIgnore = STORAGE_PATH . 'framework' . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . '.gitignore';

        if (!file_exists($databaseGitIgnore)) {
            echo "Create .gitignore file in " . $databaseGitIgnore . "\n";
            put_contents($databaseGitIgnore, "*\n!sql.json\n!.gitignore\n");
        }

        if (file_exists($frameworkGitIgnore)) {
            $content = file_get_contents($frameworkGitIgnore);
            if (!str_contains($content, '!database/')) {
                echo "Add '!database/' to " . $frameworkGitIgnore . "\n";
                $content = rtrim($content) . "\n!database/\n";
                put_contents($frameworkGitIgnore, $content);
            }
        } else {
            echo "Create .gitignore file in " . $frameworkGitIgnore . "\n";
            put_contents($frameworkGitIgnore, "*\n!cache/\n!database/\n!debugbar/\n!sessions/\n!views/\n!.gitignore\n");
        }

    }


}
