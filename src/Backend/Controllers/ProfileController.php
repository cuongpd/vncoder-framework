<?php

namespace VnCoder\Backend\Controllers;

use VnCoder\Backend\Controllers\BackendController;

class ProfileController extends BackendController
{
    public function Index_Action()
    {
        $this->title = 'Thông tin tài khoản';
        return $this->views('profile.info');
    }

    public function Update_Action()
    {
    }

    public function Change_Password_Action()
    {
    }
}
