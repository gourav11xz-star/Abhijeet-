<?php
class Report extends Model
{
    // Add a new report
    public function addReport($data)
    {
        $this->db->query('INSERT INTO reports (user_id, ad_id, reason) VALUES (:user_id, :ad_id, :reason)');

        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':ad_id', $data['ad_id']);
        $this->db->bind(':reason', $data['reason']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get all reports with details
    public function getReports()
    {
        $this->db->query('SELECT reports.*, 
                                 users.name as reporter_name,
                                 ads.title as ad_title,
                                 ads.id as ad_id
                          FROM reports
                          JOIN users ON reports.user_id = users.id
                          JOIN ads ON reports.ad_id = ads.id
                          ORDER BY reports.created_at DESC');
        return $this->db->resultSet();
    }

    // Get report by ID
    public function getReportById($id)
    {
        $this->db->query('SELECT * FROM reports WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update report status
    public function updateStatus($id, $status)
    {
        $this->db->query('UPDATE reports SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    // Delete report
    public function deleteReport($id)
    {
        $this->db->query('DELETE FROM reports WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
