<?php

namespace AppsLab\LaravelEasySearch\Repositories;

use Illuminate\Support\Facades\Schema;

class DatabaseRepository
{
    public $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public static function conn($tableName)
    {
        return new DatabaseRepository($tableName);
    }

    /**
     * Check if table exists.
     *
     * @param string $tableName
     * @return bool
     */
    public function tableExists(): bool
    {
        return Schema::hasTable($this->tableName);
    }

    /**
     * Validate user columns against table columns.
     * @param $filterColumns
     * @return array
     */
    public function validateTableColumns($filterColumns): array
    {
        $tableColumns = $this->getTableColumns();

        $filtered = array_filter($filterColumns, function ($column) use ($tableColumns) {
            return in_array(strtolower($column), $tableColumns);
        });

        return $filtered;
    }

    /**
     * Remove excluded columns.
     *
     * @param array $columns
     * @param array $excludeColumns
     * @return array
     */
    public function excludeColumns(array $columns, array $excludeColumns)
    {
        if (count($columns) < 2) {
            $columns = strpos($columns[0], ',') ? explode(',', $columns[0]) : $columns;
        }
        $filtered = array_filter($columns, function ($column) use ($excludeColumns) {
            return ! in_array($column, $excludeColumns);
        });

        return $filtered;
    }

    /**
     * Exclude columns and return the required columns.
     *
     * @param array $excludedColumns
     * @return array
     */
    public function getTableColumns(): array
    {
        return Schema::getColumnListing($this->tableName);
    }

    /**
     * Return column type name.
     *
     * @param $columnName
     * @return string
     */
    public function getColumnType($columnName): string
    {
        return Schema::getColumnType($this->tableName, $columnName);
    }
}
