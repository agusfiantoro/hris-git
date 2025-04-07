<?php
namespace App\Traits;

/**
 * StandardModelScope Trait
 * provides a powerful, convenient, and modular local model scoping.
 * @author Faisal
 */
trait StandardModelScope {

    /**
     * Scope a query to only show data from current company (by active session or by $idCompany argument, if provided)
     * @param int|null $idCompany
     */
    public function scopeCurrentCompany($query, $idCompany = null) {
        $query->where("$this->table.id_company", $idCompany ?? session('id_company'));
    }

    /**
     * Scope a query to only show active ('A') data or by provided $status argument
     * @param string $status
     */
    public function scopeActive($query, $status = 'A') {
        $query->where("$this->table.status", $status);
    }

    /**
     * Scope a query to sort in the primary key order.
     * @param   boolean $reverse    Reverse the order. Descending if true and vice-versa
     */
    public function scopeChronological($query, $reverse = false) {
        $query->orderBy($this->table.".".$this->primaryKey , $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query where data is created by selected user.
     * @param   int $idUser    User ID
     */
    public function scopeCreatedBy($query, $idUser = null) {
        $query->where("$this->table.created_by", $idUser ?? session('id_user'));
    }

    /**
     * Scope a query to return Select2 optimized result
     * @param   string  $primaryKeyColumn       Table primary key column name (default: this table's primary key)
     * @param   string  $description            Table description column name (default: `description` column on this table)
     * @param   array   $additionalSelects      Other columns to select
     */
    public function scopeFormatSelect2($query, $primaryKeyColumn = null, $description = null, $additionalSelects = []) {
        if(!$primaryKeyColumn) {
            $primaryKeyColumn = "$this->table.$this->primaryKey";
        }
        if(!$description) {
            $description = "$this->table.description";
        }
        $query->select(array_merge(["$primaryKeyColumn as id", "$description as text"], $additionalSelects));
    }

}