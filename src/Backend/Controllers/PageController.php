<?php

namespace VnCoder\Backend\Controllers;

use VnCoder\Backend\Controllers\CrudController;
use VnCoder\Models\VnPages;

class PageController extends CrudController
{
    protected string $crudModel = VnPages::class;

}
