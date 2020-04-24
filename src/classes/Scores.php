<?php
class Scores extends Common {

    public function allScores() {
        $sql = "SELECT p.P_Name, p.P_ID, SUM(a.A_Correct) AS A_Correct, SUM(q.Q_Points) AS Q_Possible
                FROM answers a
                LEFT JOIN players p ON a.P_ID = p.P_ID
                LEFT JOIN questions q ON a.Q_ID = q.Q_ID
                WHERE a.G_ID = :G_ID
                GROUP BY P_Name, P_ID
                ORDER BY A_Correct DESC, P_Name ASC";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
        ]);
        $Scores = $stmt->fetchAll();
        return $Scores;
    }

    public function getScore() {
        $sql = "SELECT p.P_Name, p.P_ID, SUM(a.A_Correct) AS A_Correct, SUM(q.Q_Points) AS Q_Possible
                FROM answers a
                LEFT JOIN players p ON a.P_ID = p.P_ID
                LEFT JOIN questions q ON a.Q_ID = q.Q_ID
                WHERE a.G_ID = :G_ID
                AND a.P_ID = :P_ID
                AND p.P_Host = 0
                GROUP BY P_Name, P_ID ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
            "P_ID" => $this->Player->P_ID,
        ]);
        $Score = $stmt->fetch();
        return $Score;
    }

}