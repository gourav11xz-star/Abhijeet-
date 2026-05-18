<?php
class LoginAttempt extends Model
{
    public function recordAttempt($email, $ip)
    {
        $this->db->query('INSERT INTO login_attempts (ip_address, email) VALUES (:ip, :email)');
        $this->db->bind(':ip', $ip);
        $this->db->bind(':email', $email);
        $this->db->execute();
    }

    public function countAttempts($email, $ip, $minutes = 15)
    {
        $time = date('Y-m-d H:i:s', strtotime("-$minutes minutes"));

        $this->db->query('SELECT COUNT(*) as count FROM login_attempts WHERE email = :email AND ip_address = :ip AND attempted_at > :time');
        $this->db->bind(':email', $email);
        $this->db->bind(':ip', $ip);
        $this->db->bind(':time', $time);

        $row = $this->db->single();
        return $row->count;
    }

    public function clearAttempts($email, $ip)
    {
        // Optional: clear attempts on successful login
        // But maybe good to keep history? Let's just not clear for now or delete old ones
        $this->db->query('DELETE FROM login_attempts WHERE email = :email AND ip_address = :ip');
        $this->db->bind(':email', $email);
        $this->db->bind(':ip', $ip);
        $this->db->execute();
    }
}
