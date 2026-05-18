<?php
class Setting extends Model
{
    public function getSettings()
    {
        $this->db->query("SELECT * FROM settings");
        return $this->db->resultSet();
    }

    public function getSettingByKey($key)
    {
        $this->db->query("SELECT setting_value FROM settings WHERE setting_key = :key");
        $this->db->bind(':key', $key);
        $row = $this->db->single();
        return $row ? $row->setting_value : null;
    }

    public function updateSetting($key, $value)
    {
        $this->db->query("UPDATE settings SET setting_value = :value WHERE setting_key = :key");
        $this->db->bind(':value', $value);
        $this->db->bind(':key', $key);
        return $this->db->execute();
    }
}
