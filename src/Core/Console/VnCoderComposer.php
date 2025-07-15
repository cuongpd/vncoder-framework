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
        $this->updateMenuAdmin();
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

        // App folder
        // $uploadFolder = storage_path('app');
        // $uploadFolderSymlink = PUBLIC_PATH . 'app';
        // if (!is_dir($uploadFolderSymlink)) {
        //     echo "Symlink folder " . $uploadFolder . " to ". $uploadFolderSymlink . "\n";
        //     symlink($uploadFolder, $uploadFolderSymlink);
        // }

        // static core asset folder
        $assetsFolder = VNCODER_CORE_PATH . 'assets';
        $assetsFolderSymlink = PUBLIC_PATH . 'core';
        if (!is_dir($assetsFolderSymlink)) {
            echo "Symlink folder " . $assetsFolder . " to ". $assetsFolderSymlink . "\n";
            symlink($assetsFolder, $assetsFolderSymlink);
        }
    }

    protected function updateMenuAdmin(){
        $menuData = \VnCoder\Backend\Models\VnAdmin::menuData();
        $htmlData = view('backend::core.menu', ['menuData' => $menuData])->render();
        file_put_contents(ADMIN_PATH . 'Views/menu/admin.blade.php', $htmlData);
        echo "Update Menu Admin Success\n";
    }

}
