<?php

namespace VnCoder\Helper;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    protected string $message = '';

    public function updateDatabase(){
        $currentDatabase = $this->getCurrentDatabase();
        $updateDatabase = $this->getUpdateDatabase();
        foreach ($updateDatabase as $tableName => $columns){
            if(isset($currentDatabase[$tableName])){
                $this->updateTable($tableName, $columns, $currentDatabase[$tableName]);
            }else{
                $this->createTable($tableName, $columns);
            }
        }
    }

    public function getCurrentDatabase(){
        $tableData = [];
        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            $columns = DB::select("SHOW COLUMNS FROM $tableName");
            foreach ($columns as $column){
                $tableData[$tableName][$column->Field] = [
                    'type' => $column->Type,
                    'null' => $column->Null,
                    'key' => $column->Key,
                    'default' => $column->Default,
                    'extra' => $column->Extra,
                ];
            }
        }
        return $tableData;

    }

    public function saveCurrentDatabase(){
        $tableData = $this->getCurrentDatabase();
        file_put_contents(storage_path('framework/database/update-sql.json'), json_encode($tableData));
    }

    public function getUpdateDatabase(){
        $sqlUpdate = storage_path('framework/database/update-sql.json');
        if(!file_exists($sqlUpdate)){
            return [];
        }
        return json_decode(file_get_contents($sqlUpdate), true);
    }

    public function getMessage(){
        return $this->message;
    }

    protected function tableExists($tableName)
    {
        return DB::getSchemaBuilder()->hasTable($tableName);
    }

    protected function createTable($tableName, $columns)
    {
        $columnsSql = $this->generateColumnsSql($columns);
        $primaryKey = $this->getPrimaryKey($columns);
        $sql = "CREATE TABLE $tableName ($columnsSql, PRIMARY KEY ($primaryKey))";
        DB::statement($sql);
    }

    protected function updateTable($tableName, $columns, $currentColumns)
    {
        $runUpdate = false;
        $columnKeyUpdate = array_diff_key($columns, $currentColumns);
        foreach ($columnKeyUpdate as $columnName => $columnData){
            $this->message .= "Add column $columnName to table $tableName \n";
            DB::statement($this->generateAddColumnSql($tableName, $columnName, $columnData));
            $runUpdate = true;
        }

        $columnKeyRemove = array_diff_key($currentColumns, $columns);
        foreach ($columnKeyRemove as $columnName => $columnData) {
            $this->message .= "Remove column $columnName from table $tableName \n";
            DB::statement($this->generateDropColumnSql($tableName, $columnName));
            $runUpdate = true;
        }

        $columnKeyInAll = array_intersect_key($columns, $currentColumns);
        foreach ($columnKeyInAll as $columnName => $columnData) {
            if($columns[$columnName] != $currentColumns[$columnName]){
                $this->message .= "Modify column $columnName in table $tableName \n";
                DB::statement($this->generateModifyColumnSql($tableName, $columnName, $columns[$columnName]));
                $runUpdate = true;
            }
        }
        if($runUpdate){
            $this->setOrderColumnTable($tableName, $columns);
        }
    }

    protected function setOrderColumnTable($tableName, $columnsArray)
    {
        $columnsData = [];
        if (isset($columnsArray['id'])) {
            $columnsData['id'] = $columnsArray['id'];
            unset($columnsArray['id']);
        }
        foreach ($columnsArray as $key => $value) {
            if (!in_array($key, ['created', 'updated', 'status'])) {
                $columnsData[$key] = $value;
            }
        }
        foreach (['created', 'updated', 'status'] as $key) {
            if (isset($columnsArray[$key])) {
                $columnsData[$key] = $columnsArray[$key];
            }
        }

        $alterCommands = [];
        $previousColumn = null;
        foreach ($columnsData as $k => $v) {
            $columnName = $k;
            $columnType = $v['type'];
            $columnNull = $v['null'] == 'NO' ? 'NOT NULL' : 'NULL';
            $columnDefault = $v['default'] !== null ? "DEFAULT '{$v['default']}'" : '';
            $columnExtra = $v['extra'] ?? '';

            $command = "MODIFY COLUMN $columnName $columnType $columnNull $columnDefault $columnExtra";
            if ($previousColumn === null) {
                $command .= " FIRST";
            } else {
                $command .= " AFTER $previousColumn";
            }
            $alterCommands[] = $command;
            $previousColumn = $columnName;
        }

        if (!empty($alterCommands)) {
            $alterTableSQL = "ALTER TABLE $tableName " . implode(', ', $alterCommands);
            DB::statement($alterTableSQL);
        }
    }

    protected function generateColumnsSql($columns)
    {
        $columnsSql = [];
        foreach ($columns as $name => $column) {
            $columnsSql[] = "`$name` {$column['type']} " .
                ($column['null'] == 'NO' ? 'NOT NULL' : 'NULL') .
                ($column['default'] !== null ? " DEFAULT '{$column['default']}'" : '') .
                ($column['extra'] ? " {$column['extra']}" : '');
        }
        return implode(', ', $columnsSql);
    }

    protected function generateAddColumnSql($tableName, $columnName, $column)
    {
        return "ALTER TABLE $tableName ADD COLUMN `$columnName` {$column['type']} " .
            ($column['null'] == 'NO' ? 'NOT NULL' : 'NULL') .
            ($column['default'] !== null ? " DEFAULT '{$column['default']}'" : '') .
            ($column['extra'] ? " {$column['extra']}" : '');
    }

    protected function generateDropColumnSql($tableName, $columnName)
    {
        return "ALTER TABLE $tableName DROP COLUMN `$columnName`";
    }

    protected function generateModifyColumnSql($tableName, $columnName, $column)
    {
        return "ALTER TABLE $tableName MODIFY COLUMN `$columnName` {$column['type']} " .
            ($column['null'] == 'NO' ? 'NOT NULL' : 'NULL') .
            ($column['default'] !== null ? " DEFAULT '{$column['default']}'" : '') .
            ($column['extra'] ? " {$column['extra']}" : '');
    }

    protected function getPrimaryKey($columns)
    {
        foreach ($columns as $name => $column) {
            if (isset($column['key']) && $column['key'] == 'PRI') {
                return $name;
            }
        }
        return 'id';
    }
}