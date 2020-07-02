<?php
class Answers extends Common {

    public function getAnswers() {
        $sql = "SELECT * FROM answers WHERE G_ID = :G_ID  ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
        ]);
        $Answers = $stmt->fetchAll();
        return $Answers;
    }
    
    public function getAnswersForQuestion( $Q_ID ) {
        $sql = "SELECT a.*, p.P_Name
                FROM answers a
                LEFT JOIN players p ON a.P_ID = p.P_ID
                WHERE a.G_ID = :G_ID 
                    AND a.Q_ID = :Q_ID 
                ORDER BY p.P_Name";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
            "Q_ID" => $Q_ID,
        ]);
        $Answers = $stmt->fetchAll();
        return $Answers;
    }

    public function saveAnswer( $data ) {
        $sql = "SELECT P_ID FROM answers WHERE Q_ID = :Q_ID AND P_ID = :P_ID";
        $stmt = $this->db->prepare ( $sql );
        $stmt->execute([
            "Q_ID" => $data['Q_ID'],
            "P_ID" => $this->Player->G_ID,
        ]);
        $alreadyAnswered = $stmt->fetchColumn();
        if ( !$alreadyAnswered && $data['A_Answer'] ) {
            $sql = "INSERT INTO answers ( A_ID, G_ID, Q_ID, P_ID, A_Answer, A_Marked, A_Correct )
                    VALUES ( UUID(), :G_ID, :Q_ID, :P_ID, :A_Answer, :A_Marked, :A_Correct ) ";
            $stmt = $this->db->prepare ( $sql );
            $stmt->execute([
                "G_ID" => $this->Player->G_ID,
                "Q_ID" => $data['Q_ID'],
                "P_ID" => $this->Player->P_ID,
                "A_Answer" => $data['A_Answer'],
                "A_Marked" => 0,
                "A_Correct" => null,
            ]);
        }

    }

    public function getAnswer( $Q_ID ) {
        $sql = "SELECT * FROM answers 
                WHERE Q_ID = :Q_ID
                AND P_ID = :P_ID";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "Q_ID" => $Q_ID,
            "P_ID" => $this->Player->P_ID,
        ]);
        $Questions = $stmt->fetch();
        return $Questions;
    }

    public function mark( $A_ID, $A_Correct ) {
        $sql = "UPDATE answers SET A_Marked = :A_Marked, A_Correct = :A_Correct WHERE A_ID = :A_ID ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "A_ID" => $A_ID,
            "A_Correct" => $A_Correct,
            "A_Marked" => 1,
        ]);
    }

}