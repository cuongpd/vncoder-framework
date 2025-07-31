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
    }


}
