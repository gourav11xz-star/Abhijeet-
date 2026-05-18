<?php
class User extends Model
{
    // Find user by email
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Register User
    public function register($data)
    {
        $this->db->query('INSERT INTO users (name, email, password_hash) VALUES(:name, :email, :password)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);

        // Execute
        if ($this->db->execute()) {
            // Get ID
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password)
    {
        $row = $this->findUserByEmail($email);

        if ($row == false)
            return false;

        $hashed_password = $row->password_hash;
        if (password_verify($password, $hashed_password)) {
            $this->db->query('UPDATE users SET last_login = NOW() WHERE id = :id');
            $this->db->bind(':id', $row->id);
            $this->db->execute();
            return $row;
        } else {
            return false;
        }
    }

    // Get user by ID
    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        return $row;
    }

    // Update Profile
    public function updateProfile($data)
    {
        // If avatar is provided
        if (!empty($data['avatar'])) {
            $this->db->query('UPDATE users SET name = :name, phone = :phone, avatar = :avatar WHERE id = :id');
            $this->db->bind(':avatar', $data['avatar']);
        } else {
            $this->db->query('UPDATE users SET name = :name, phone = :phone WHERE id = :id');
        }

        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':id', $data['id']);

        return $this->db->execute();
    }


    // Get All Users (Admin)
    public function getUsers()
    {
        $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $this->db->resultSet();
    }

    // Delete User
    public function deleteUser($id)
    {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    // Ban User
    public function banUser($id)
    {
        $this->db->query('UPDATE users SET is_banned = 1 WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Unban User
    public function unbanUser($id)
    {
        $this->db->query('UPDATE users SET is_banned = 0 WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
