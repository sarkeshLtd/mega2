<?php
/**
 * @author babak alizadeh
 * @copy left LGPL V3
 */

namespace Mega\Cls\page;
use \Mega\Cls\Database as db;

class paging {

    /*
     * find this has next page
     * @param string $table, table name
     * @param integer $thisPage, this page number, default is 1
     * @param integer $countPerPage, number of elements per page
     * $return boolean
     */
    public static function hasNext($table,$thisPage=1,$countPerPage){
        $orm = db\orm::singleton();
        $startFrom = ($thisPage * $countPerPage);
        $countAll = $orm->count($table);
        if($startFrom  >= $countAll)
            return false;
        return true;
    }

    /*
     * find this has next page with sql string
     * @param string $sql, sql syntax
     * @param integer $thisPage, this page number, default is 1
     * @param integer $countPerPage, number of elements per page
     * @param array $params, sql parameters
     * $return boolean
     */
    public static function hasNextWithSql($sql,$thisPage=1,$countPerPage,$params = []){
        $orm = db\orm::singleton();
        $startFrom = ($thisPage * $countPerPage);
        $countAll = $orm->exec($sql,$params,ROWS_COUNT);
        if($startFrom  >= $countAll)
            return false;
        return true;
    }

    /*
     * find this has previous page
     * @param string $table, table name
     * @param integer $thisPage, this page number, default is 1
     * @param integer $countPerPage, number of elements per page
     * $return boolean
     */
    public static function hasPre($table,$thisPage=1,$countPerPage,$params = ''){
        if($thisPage <= 1) return false;
        return true;
    }

    /**
     * get content of page
     * @param string $sql
     * @param array $params
     * @param integer $page number
     * @param integer $countPerPage
     * @return array object
     */
    public static function getPageContent($sql,$params,$pageNumber,$countPerPage){
        $orm = db\orm::singleton();
        $sql = str_replace(';','',$sql);
        $sql .= ' limit ? OFFSET ?;';
        $startFrom = (($pageNumber - 1) * $countPerPage);
        $params = array_merge($params,[$countPerPage,$startFrom]);
        return $orm->exec($sql,$params,SELECT);
    }


}