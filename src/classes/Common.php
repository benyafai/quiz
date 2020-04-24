<?php
abstract class Common {
    
    protected $db;
    protected $Player;
    protected $JWT;

    public function __construct() {
        $this->db = new PDO(
            "mysql:host=db;dbname=".getenv('MYSQL_DATABASE'),
            getenv('MYSQL_USER'),
            getenv('MYSQL_PASSWORD')
        );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        if ( !$this->db->query("SHOW TABLES LIKE 'games'")->fetch() ) {
            $this->db->query( file_get_contents(__DIR__."/../db/structure.sql") );
        }
        $this->JWT = new JWT();
        if ( isset($_COOKIE['Player']) ) {
            $this->Player = $this->checkPlayer();
            setcookie('Player', $this->JWT->sign([
                "P_ID" => $this->Player->P_ID,
            ]), time() + (86400 * 3), "/");
        }
    }

    public function newUUID() {
        $sql = "SELECT UUID() AS UUID";
        $uuid = $this->db->query($sql)->fetch();
        return $uuid->UUID;
    }

    public function checkPlayer() {
        if ( isset($_COOKIE['Player']) ) {
            $verify = $this->JWT->verify( $_COOKIE['Player'] );
            if ( $verify !== false ) {
                $sql = "SELECT * FROM players WHERE P_ID = :P_ID ";
                $stmt = $this->db->prepare( $sql );
                $stmt->execute([
                    "P_ID" => $verify->P_ID,
                ]);
                $playerdetails = $stmt->fetch();
                if ( $playerdetails ) {
                    $this->Player = $playerdetails;
                }
                return $this->Player;
            }
        } 
        return null;
    }

    private function createDatabaseStructure() {

    }
}