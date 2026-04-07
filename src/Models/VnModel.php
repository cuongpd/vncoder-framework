<?php

namespace VnCoder\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use VnCoder\Models\VnModelBase;

/**
 * @method $this->scopeSearch search(string $query, string|array $column ) Search $query in $column
 * @method $this->scopeActive active() Check record status > 0
 *
 */

class VnModel extends VnModelBase
{

    public string $modelName = '';

    public function getCreatedDateAttribute(){
        return date('d-m-Y', $this->created->timestamp);
    }

    public function tableData(){
        $tableColumn = $this->getTableColumns();
        $tableData = array_fill_keys($tableColumn, '');
        return (object) $tableData;
    }

    public function getTableColumns(){
        $tableColumn = Schema::getColumnListing($this->table);
        unset($tableColumn['created'], $tableColumn['updated'], $tableColumn['status']);
        return $tableColumn;
    }
    protected function formConfig(){
        return [];
    }

    public function getFormData($id = 0){
        $formData= [];
        $formConfig = $this->formConfig();
        $formDataType = $this->getTableColumnsWithDataType();
        if($id > 0) {
            $modelData = $this->where('id', $id)->where('status', '>', 0)->first();
            if(!$modelData) return [];
        }else{
            $modelData = $this->tableData();
        }
        $formData['id'] = ['value' => $modelData->id, 'type' => 'hidden', 'required' => ''];
        foreach ($formConfig as $key => $item){
            $item['type'] = $item['type'] ?? $formDataType[$key]['type'];
            $item['value'] = $item['value'] ?? $modelData->$key;
            $item['required'] = (isset($item['required']) && $item['required']) ? 'required' : '';
            $formData[$key] = $item;
        }
        return $formData;
    }

    public function getRequestInputData(Request $request){
        $getTableColumns = $this->getTableColumns();
        $requestData = $request->except('__token');
        $data = [];
        foreach ($getTableColumns as $item){
            if(isset($requestData[$item])){
                $data[$item] = $requestData[$item];
            }
        }
        unset($data['id']);
        return $data;
    }

    public function submitFormData(Request $request){
        $id = $request->input('id', 0);
        $data = $this->getRequestInputData($request);
        $data['updated'] = TIME_NOW;
        if($id > 0){
            $this->where('id', $id)->update($data);
        }else{
            $data['created'] = TIME_NOW;
            $id = $this->insertGetId($data);
        }
        flash_message( $this->modelName . ' : Data has been saved');
        return $id;
    }

    public function getTableColumnsWithDataType(){
        $query = DB::select("DESCRIBE {$this->table}");
        $tableColumnType = [];
        foreach ($query as $column){
            $key = $column->Field;
            $type = $column->Type;
            if (str_contains($type, 'int') || str_contains($type, 'bigint') || str_contains($type, 'decimal')) {
                $tableColumnType[$key] = ['type' => 'number'];
            } elseif (str_contains($type, 'varchar')) {
                if (str_contains($type, '(500)')) {
                    $tableColumnType[$key] = ['type' => 'textarea'];
                } else {
                    $tableColumnType[$key] = ['type' => 'text'];
                }
            } elseif ($type === 'longtext') {
                $tableColumnType[$column->Field] = ['type' => 'textarea'];
            } elseif (str_ends_with($key, '_id')) {
                $tableColumnType[$key] = ['type' => 'select'];
            } else {
                $tableColumnType[$column->Field] = ['type' => 'text'];
            }
        }
        $tableColumnType['id']['type'] = 'hidden';
        return $tableColumnType;
    }

    public function tableConfig(){
        return [];
    }

    public function crudData(){
        return $this->where('status', '>', 0)->get();
    }

    public function crudDeletedData(){
        return $this->where('status', -1)->get();
    }

    public function deleteItem($id){
        return $this->where('id', $id)->update(['status' => -1]);
    }

    public function restoreItem($id){
        return $this->where('id', $id)->update(['status' => 1]);
    }

    public static function getInfo(int $id = 0)
    {
        return self::where('id', $id)->first();
    }

}
