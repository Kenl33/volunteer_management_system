<?php

class ReportGenerator {
    private $db;

    public function __construct($db_connection) {
        $this->db = $db_connection;
    }

    /**
     * Aggregates total metrics for a general dashboard report.
     */
    public function getSummaryStats() {
        $stats = [];

        // Total volunteers
        $query = "SELECT COUNT(id) as total FROM volunteers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['total_volunteers'] = $stmt->fetch()['total'];

        // Total events hosted
        $query = "SELECT COUNT(id) as total FROM events";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['total_events'] = $stmt->fetch()['total'];

        // Total hours contributed across all tasks
        $query = "SELECT SUM(hours_worked) as total FROM participation";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stats['total_hours'] = $stmt->fetch()['total'] ?? 0;

        return $stats;
    }


    public function getVolunteerParticipationReport() {
                $query = "SELECT 
                                        v.first_name, 
                                        v.last_name, 
                                        COUNT(p.id) as tasks_completed, 
                                        COALESCE(SUM(p.hours_worked), 0) as total_hours
                                    FROM volunteers v
                                    LEFT JOIN participation p ON v.id = p.volunteer_id
                                    GROUP BY v.id
                                    ORDER BY total_hours DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getVolunteersWithTaskCounts() {
        $query = "WITH TaskCounts AS (
                      SELECT volunteer_id, COUNT(id) as total_tasks
                      FROM participation
                      GROUP BY volunteer_id
                  )
                  SELECT v.first_name, v.last_name, COALESCE(tc.total_tasks, 0) as total_tasks
                  FROM volunteers v
                  LEFT JOIN TaskCounts tc ON v.id = tc.volunteer_id
                  ORDER BY total_tasks DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getEventEngagementReport() {
                $query = "SELECT 
                                        e.event_name, 
                                        e.event_date, 
                                        COUNT(DISTINCT t.id) as task_count,
                                        COALESCE(SUM(p.hours_worked), 0) as total_hours
                                    FROM events e
                                    LEFT JOIN tasks t ON e.id = t.event_id
                                    LEFT JOIN participation p ON p.task_id = t.id
                                    GROUP BY e.id
                                    ORDER BY e.event_date DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
