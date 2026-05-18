<?php
class Location extends Model
{
    public function getLocations()
    {
        $this->db->query('SELECT * FROM locations');
        return $this->db->resultSet();
    }
}
