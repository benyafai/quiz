<?php
use Psr\Http\Message\UploadedFileInterface;
class Questions extends Common {
 
    public function getRounds() {
        $sql = "SELECT * FROM rounds WHERE G_ID = :G_ID ORDER BY R_Order";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
        ]);
        $Rounds = $stmt->fetchAll();
        return $Rounds;
    }

    public function getQuestions() {
        $sql = "SELECT r.*, q.* 
                FROM questions q 
                LEFT JOIN rounds r ON q.R_ID = r.R_ID
                WHERE r.G_ID = :G_ID
                ORDER BY R_Order, Q_Order";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID,
        ]);
        $Questions = $stmt->fetchAll();
        return $Questions;
    }

    public function addRound( $data ) {
        $sql = "SELECT MAX(R_Order) FROM rounds WHERE G_ID = :G_ID";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $Player->G_ID,
        ]);
        $Max = $stmt->fetchColumn();
        if ( !$Max ) {
            $Order = 1;
        } else {
            $Order = $Max +1;
        }
        $sql = "INSERT INTO rounds ( R_ID, G_ID, R_Round, R_Order )
                VALUES ( UUID(), :G_ID, :R_Round, :R_Order ); ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "G_ID" => $this->Player->G_ID, 
            "R_Round" => $data['R_Round'], 
            "R_Order" => $Order,
        ]);
    }

    public function addQuestion( $data, $uploadedFile = null ) {
        $sql = "SELECT MAX(Q_Order) FROM questions WHERE R_ID = :R_ID";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "R_ID" => $data['R_ID'],
        ]);
        $Max = $stmt->fetchColumn();
        if ( !$Max ) {
            $Order = 1;
        } else {
            $Order = $Max +1;
        }
        if ( $uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK ) {
            if ($uploadedFile->getClientMediaType() == 'image/png' ||
                $uploadedFile->getClientMediaType() == 'image/jpeg' ) {
                $image = $this->moveUploadedFile( $uploadedFile );
            } else if ( $uploadedFile->getClientMediaType() == 'audio/x-m4a' ) {
                $sound = $this->moveUploadedFile( $uploadedFile );
            } else {
                die("We can't support that file type, go back and try again");
            }
        }
        $sql = "INSERT INTO questions ( Q_ID, R_ID, Q_Question, Q_Answer, Q_Order, Q_Image, Q_Sound, Q_Video, Q_Points )
                VALUES ( UUID(), :R_ID, :Q_Question, :Q_Answer, :Q_Order, :Q_Image, :Q_Sound, :Q_Video, :Q_Points ); ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "R_ID" => $data['R_ID'], 
            "Q_Question" => $data['Q_Question'], 
            "Q_Answer" => $data['Q_Answer'], 
            "Q_Order" => $Order,
            "Q_Image" => $image ? $image : null,
            "Q_Sound" => $sound ? $sound : null,
            "Q_Video" => $video ? $video : null,
            "Q_Points" => $data['Q_Points'],
        ]);
    }

    private function moveUploadedFile( UploadedFileInterface $uploadedFile ) {
        $directory = __DIR__.'/../../public/uploads';
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }

    public function currentQuestion( $Q_ID ) {
        $sql = "SELECT r.R_Round, r.R_Order, q.Q_ID, q.Q_Question, q.Q_Order, q.Q_Image, q.Q_Sound
                FROM questions q 
                LEFT JOIN rounds r ON q.R_ID = r.R_ID
                WHERE q.Q_ID = :Q_ID
                ORDER BY R_Order, Q_Order";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "Q_ID" => $Q_ID,
        ]);
        $Questions = $stmt->fetch();
        return $Questions;
    }

}