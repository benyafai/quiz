<?php
class Players extends Common {

    public function joinGame( $data ) {
        $UUID = $this->newUUID();
        $sql = "INSERT INTO players ( P_ID, G_ID, P_Name, P_Host ) 
                VALUES ( :P_ID, :G_ID, :P_Name, :P_Host ); ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "P_ID" => $UUID, 
            "G_ID" => $data['G_ID'], 
            "P_Name" => $data['P_Name'], 
            "P_Host" => $data['P_Host'],
        ]);
        setcookie('Player', $this->JWT->sign([
            "P_ID" => $UUID,
        ]), time() + (86400 * 3), "/");
    }

    public function changeName( $data ) {
        $sql = "UPDATE players SET P_Name = :P_Name WHERE P_ID = :P_ID; ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "P_Name" => $data['P_Name'], 
            "P_ID" => $Player->P_ID, 
        ]);
        $Player->Name = $data['Name'];
        setcookie('Player', $this->JWT->sign(
            $Player
        ), time() + (86400 * 3), "/");
    }

    public function getPlayers () {
        $sql = "SELECT * FROM players WHERE G_ID = :G_ID AND P_Host = 0 ORDER BY P_Name ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
        ]);
        $Players = $stmt->fetchAll();
        return $Players;
    }

    public function makeHost( $P_ID ) {
        $sql = "UPDATE players SET P_Host = 1 WHERE P_ID = :P_ID ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "P_ID" => $P_ID,
        ]);
    }
    
    public function makePlayer( $P_ID ) {
        $sql = "UPDATE players SET P_Host = 0 WHERE P_ID = :P_ID ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "P_ID" => $P_ID,
        ]);
    }

    public function kick( $P_ID ) {
        $sql = "DELETE FROM answers WHERE P_ID = :Answers;
                DELETE FROM players WHERE P_ID = :Players; ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "Answers" => $P_ID,
            "Players" => $P_ID,
        ]);
    }
}