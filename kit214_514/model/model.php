<?php
include '../config/database.php';

class Model
{

    public function DB()
    {
        return new Database();
    }

    /**
     * 
     * @param string $sql 
     * @param array $params 
     * @return mixed
     */
    public function sql(string $sql, array $params = [])
    {
        return $this->DB()->sql($sql, $params);
    }
}
