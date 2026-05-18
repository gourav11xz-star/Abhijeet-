<?php
class Message extends Model
{
    // Get all conversations for the current user
    // Group by Ad and Other User to create unique conversation threads
    public function getConversations($userId)
    {
        // Get the latest message for each unique conversation (User + Ad pair)
        $sql = "SELECT 
                    m.*,
                    ads.title as ad_title, 
                    ads.images as ad_images,
                    (CASE WHEN m.sender_id = :user_id THEN u_recv.name ELSE u_send.name END) as user_name,
                    (CASE WHEN m.sender_id = :user_id THEN u_recv.id ELSE u_send.id END) as other_user_id,
                    (CASE WHEN m.sender_id = :user_id THEN u_recv.avatar ELSE u_send.avatar END) as user_avatar
                FROM messages m
                JOIN ads ON m.ad_id = ads.id
                LEFT JOIN users u_send ON m.sender_id = u_send.id
                LEFT JOIN users u_recv ON m.receiver_id = u_recv.id
                WHERE m.id IN (
                    SELECT MAX(id)
                    FROM messages
                    WHERE sender_id = :user_id OR receiver_id = :user_id
                    GROUP BY ad_id, (CASE WHEN sender_id = :user_id THEN receiver_id ELSE sender_id END)
                )
                ORDER BY m.created_at DESC";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    // Get messages for a specific conversation
    public function getConversationMessages($userId, $otherUserId, $adId)
    {
        $sql = "SELECT m.*, 
                       u.name as sender_name, u.avatar as sender_avatar 
                FROM messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.ad_id = :ad_id 
                AND ((m.sender_id = :user_id AND m.receiver_id = :other_id) 
                  OR (m.sender_id = :other_id AND m.receiver_id = :user_id))
                ORDER BY m.created_at ASC";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':other_id', $otherUserId);
        $this->db->bind(':ad_id', $adId);

        return $this->db->resultSet();
    }

    public function sendMessage($data)
    {
        $this->db->query('INSERT INTO messages (sender_id, receiver_id, ad_id, message) VALUES (:sender_id, :receiver_id, :ad_id, :message)');
        $this->db->bind(':sender_id', $data['sender_id']);
        $this->db->bind(':receiver_id', $data['receiver_id']);
        $this->db->bind(':ad_id', $data['ad_id']);
        $this->db->bind(':message', $data['message']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function markAsRead($userId, $otherUserId, $adId)
    {
        $sql = "UPDATE messages SET is_read = 1 
                WHERE receiver_id = :user_id 
                AND sender_id = :other_id 
                AND ad_id = :ad_id";

        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':other_id', $otherUserId);
        $this->db->bind(':ad_id', $adId);

        return $this->db->execute();
    }

    public function getUnreadCount($userId)
    {
        $this->db->query('SELECT COUNT(*) as count FROM messages WHERE receiver_id = :user_id AND is_read = 0');
        $this->db->bind(':user_id', $userId);
        $row = $this->db->single();
        return $row->count;
    }
}
