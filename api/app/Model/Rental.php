<?php
/**
 * Created by PhpStorm.
 * User: pablo.juarez
 * Date: 4/29/2019
 * Time: 2:24 PM
 */

namespace App\Model;

use App\Db\Db;

class Rental extends Model
{
    public $id;
    public $price;
    public $start_date;
    public $end_date;

    public function __construct()
    {

    }

    public function save() {
        $table = $this->getTableName();
        $this->sanitize();
        $query = "insert into $table VALUES (null, {$this->price}, '{$this->start_date}', '{$this->end_date}')";
        $db = Db::instance();
        return $db->query($query);
    }

    public function getAll() {
        $db = Db::instance();
        $table = $this->getTableName();
        $result = $db->query("select * from $table ORDER  by start_date");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Retrieve first|all coincidence from interval given with a price given
     * @param bool $all
     * @return array|null
     */
    public function checkForMerge($all = false) {
        $table = $this->getTableName();
        $this->sanitize();
        $query = "select * from $table WHERE price = {$this->price} and '{$this->start_date}' < end_date";
        $db = Db::instance();
        $result = $db->query($query);
        if ($all) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return $result->fetch_assoc();
    }

    /**
     * Retrieve coincidence of intervals that price does not match
     * @return array|null
     */
    public function brakeInterval() {
        $table = $this->getTableName();
        $this->sanitize();
        $query = "select * from $table WHERE price != {$this->price} and '{$this->start_date}' < end_date";
        $db = Db::instance();
        $result = $db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update($id, $price, $start, $end) {
        $table = $this->getTableName();
        $cleanArgs = $this->sanitizeVars(func_get_args());
        list($idClean, $priceClean, $startClean, $endClean) = $cleanArgs;
        $query = "UPDATE $table set price = $priceClean, start_date = '$startClean', end_date = '$endClean'  WHERE id = $idClean";
        $db = Db::instance();
        $result = $db->query($query);
        return $result;
    }

    public function exists() {
        $table = $this->getTableName();
        $this->sanitize();
        $query = "select * from $table WHERE start_date = '{$this->start_date}' AND end_date = '{$this->end_date}' AND price = {$this->price}";
        $db = Db::instance();
        $result = $db->query($query);
        return $result->fetch_assoc();
    }

    public function intervalExists() {
        $table = $this->getTableName();
        $this->sanitize();
        $query = "select * from $table WHERE start_date = '{$this->start_date}' AND end_date = '{$this->end_date}'";
        $db = Db::instance();
        $result = $db->query($query);
        return $result->fetch_assoc();
    }

    public function priceExists() {
        $table = $this->getTableName();
        $this->sanitize();
        $query = "select * from $table WHERE price = {$this->price}";
        $db = Db::instance();
        $result = $db->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }



    public function deleteById($id) {
        $table = $this->getTableName();
        $query = "DELETE FROM $table WHERE id = $id";
        $db = Db::instance();
        return $db->query($query);
    }


    public function flush() {
        $table = $this->getTableName();
        $query = "TRUNCATE TABLE $table";
        $db = Db::instance();
        return $db->query($query);
    }

}