<?php
class Games extends Common {
    
    public function getGame( $G_ID ) {
        $sql = "SELECT * FROM games WHERE G_ID = :G_ID AND G_Ended IS NULL";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $G_ID,
        ]);
        $Game = $stmt->fetch();
        return $Game;
    }

    public function newGame( $data ) {
        $UUID = $this->newUUID();
        $sql = "INSERT INTO games ( G_ID, G_Name, G_Started, G_Ended, G_Current )
                VALUES ( :G_ID, :G_Name, :G_Started, :G_Ended, :G_Current ); ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $UUID, 
            "G_Name" => $data['G_Name'], 
            "G_Started" => date("Y-m-d H:i:s"), 
            "G_Ended" => null, 
            "G_Current" => "Begin",
        ]);
        return $UUID;
    }

    public function endGame( $G_ID ) {
        $sql = "UPDATE games SET G_Ended = :G_Ended WHERE G_ID = :G_ID ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $G_ID,
            "G_Ended" => date("Y-m-d H:i:s"),
        ]);
    }

    public function currentQuestion() {
        $sql = "SELECT G_Current FROM games WHERE G_ID = :G_ID";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
        ]);
        $Q_Current = $stmt->fetchColumn();
        return $Q_Current;
    }
    
    public function setCurrent( $G_Current ) {
        $sql = "UPDATE games SET G_Current = :G_Current WHERE G_ID = :G_ID ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_Current" => $G_Current,
            "G_ID" => $this->Player->G_ID,
        ]);
    }
    
}