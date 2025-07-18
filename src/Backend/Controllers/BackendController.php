<?php

namespace VnCoder\Backend\Controllers;

use Illuminate\Support\Str;
use VnCoder\Backend\Models\Admin;
use VnCoder\Backend\Models\VnAdmin;
use VnCoder\Controllers\VnController;
use Illuminate\Pagination\Paginator;

class BackendController extends VnController
{
    protected int    $id = 0;
    protected bool   $isBackend = true;
    protected string $__query;
    protected string $module, $controller, $action;
    protected string $linkController;
    protected bool   $usingFormEditor = false;
    protected bool   $isBackendCore = false;
    protected string $currentBackendUrl = '';
    protected string $parentBackendUrl = '';
    protected bool   $hideSidebar = false;

    public function siteInit()
    {
        $request = request();
        $this->module = $request->server('__ADMIN__MODULE__NAME', '');
        $this->controller = $request->server('__ADMIN__CONTROLLER__NAME', '');
        $this->action = $request->server('__ADMIN__ACTION__NAME', 'index');
        $this->isBackendCore = $request->server('__ADMIN__CORE__ROUTER', false);
        $this->currentBackendUrl = $this->module ? backend($this->module . '/' . $this->controller . '/' . $this->action) : backend($this->controller . '/' . $this->action);
        if (Str::endsWith($this->currentBackendUrl, '-data')) {
            $this->parentBackendUrl = Str::beforeLast($this->currentBackendUrl, '-data');
        } else {
            $this->parentBackendUrl = $this->module ? backend($this->module . '/' . $this->controller) : backend($this->controller);
        }

        $this->__query = $request->input('__query', '');
        $this->id = (int) $request->input('id', 0);
        $this->setData['__query'] = $this->__query;

        if (str_contains($this->action, 'add') || str_contains($this->action, 'create') || str_contains($this->action, 'edit')) {
            $this->usingFormEditor = true;
        }

        $this->linkController = backend($this->controller);
        $this->setData['__module'] = $this->module;
        $this->setData['__controller'] = $this->controller;
        $this->setData['__action'] = $this->action;
        $this->setData['__userData'] = Admin::getUserData();

        $this->setData['__currentBackendUrl'] = $this->currentBackendUrl;
        $this->setData['__parentBackendUrl'] = $this->parentBackendUrl;
        $this->setData['__currentBackendUrlData'] = $this->currentBackendUrl . '-data';
        $this->setData['__currentBackendUrlAjax'] = $this->currentBackendUrl . '-ajax';
        $this->setData['__currentBackendUrlEdit'] = $this->parentBackendUrl . '/edit';
        $this->setData['__currentBackendUrlDelete'] = $this->parentBackendUrl . '/delete';
        $this->setData['__currentBackendUrlRestore'] = $this->parentBackendUrl . '/restore';

        $this->setData['__debugbar'] = $request->cookie('__debugbar', 'off') == 'on' ? 'ON' : 'OFF';

        // Init Paginator
        Paginator::defaultView('backend::core.pagination.default');

    }

    public function redirectUrl(string $action = '')
    {
        return redirect($this->linkAction($action));
    }

    /**
     * Lấy link backend theo Action
     * @return string
     */
    public function linkAction(string $action = '')
    {
        return $action ? $this->parentBackendUrl . '/' . $action : $this->parentBackendUrl;
    }

    public function views($bladeName = '', $isBackendCore = false)
    {
        $this->setData['__backendMenu'] = VnAdmin::menuData();
        $this->setData['__backendMenuIcon'] = VnAdmin::$menuIcon;

        if ($this->usingFormEditor) {
            $this->initFormEditor();
        }
        if ($this->isBackendCore || $isBackendCore) {
            $this->setData['__bladeYieldRender'] = 'backend::pages.' . $bladeName;
        } else {
            if($this->module){
                $this->setData['__bladeYieldRender'] = 'admin::modules.' . $this->module . '.' . $this->controller . '.' . $bladeName;
            }else{
                $this->setData['__bladeYieldRender'] = 'admin::pages.' . $this->controller . '.' . $bladeName;
            }
        }
        $this->setData['__hideSidebar'] = $this->hideSidebar;
        return parent::views($bladeName);
    }

    protected function gotoActionPage($action, $message = ''){
        if($message) flash_message($message);
        return redirect()->to($this->linkAction($action));
    }

    protected function initDataTable($buttonActive = false)
    {
        // Enable Export Button
        if($buttonActive) $this->header('<script>const DATATABLE_EXPORT_BUTTON = true;</script>');
        // Buttons | FixedHeader | Responsive | KeyTable | Select
        $this->linkCSS('core/plugins/data-tables/datatables/datatables.min.css');
        $this->linkCSS('core/plugins/data-tables/style.css');
        $this->linkJS('core/plugins/data-tables/datatables/datatables.min.js');
        $this->linkJS('core/plugins/data-tables/init.js');
    }

    protected function initFormEditor()
    {
        $this->linkCSS('core/plugins/form-ui/bootstrap-maxlength.css');
        $this->linkCSS('core/plugins/form-ui/air-datepicker.css');

        $this->linkJS('core/plugins/form-ui/bootstrap-maxlength.js');
        $this->linkJS('core/plugins/form-ui/air-datepicker.js');
        $this->linkJS('core/plugins/form-ui/choices.min.js');
        $this->linkJS('core/plugins/tinymce/tinymce.min.js');
    }

    protected function initFlatpickr()
    {
        $this->linkCSS('core/plugins/flatpickr/flatpickr.min.css');
        $this->linkJS('core/plugins/flatpickr/flatpickr.min.js');
    }

    protected function initCodeMirror()
    {
        $this->linkCSS('core/plugins/codemirror/codemirror.min.css');
        $this->linkJS('core/plugins/codemirror/codemirror.min.js');
    }

    protected function initClipboard(){
        $this->linkCSS('core/plugins/clipboard/clipboard.min.js');
    }
    
    protected function initApexCharts()
    {
        $this->linkJS('core/plugins/apexcharts/apexcharts.min.js');
    }

}
