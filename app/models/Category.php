<?php
class Category extends Model
{
    public function getCategories()
    {
        $this->db->query('SELECT * FROM categories WHERE status = "active"');
        return $this->db->resultSet();
    }
    public function getCategoryById($id)
    {
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addCategory($data)
    {
        $this->db->query('INSERT INTO categories (name, slug, icon) VALUES(:name, :slug, :icon)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':icon', $data['icon']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteCategory($id)
    {
        $this->db->query('DELETE FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);

        try {
            if ($this->db->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Catch foreign key constraint violation (23000)
            // We can log it or just return false to indicate failure
            return false;
        }
    }
}
