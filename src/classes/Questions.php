<?php
use Psr\Http\Message\UploadedFileInterface;
class Questions extends Common {
 
    public function getRounds() {
        $sql = "SELECT * FROM rounds WHERE G_ID = :G_ID ORDER BY R_Order ASC";
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
            "G_ID" => $this->Player->G_ID,
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

    public function addQuestion( $data, $questionFile = null, $answerFile = null ) {
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
        if ( $questionFile && $questionFile->getError() === UPLOAD_ERR_OK ) {
            if ($questionFile->getClientMediaType() == 'image/png' ||
                $questionFile->getClientMediaType() == 'image/jpeg' ) {
                $image_q = $this->moveUploadedFile( $questionFile );
            } else if ( $questionFile->getClientMediaType() == 'audio/x-m4a' ) {
                $sound_q = $this->moveUploadedFile( $questionFile );
            } else if ( $questionFile->getClientMediaType() == 'video/mp4' ) {
                $video_q = $this->moveUploadedFile( $questionFile );
            } else {
                die("We can't support that file type, go back and try again");
            }
        }
        if ( $answerFile && $answerFile->getError() === UPLOAD_ERR_OK ) {
            if ($answerFile->getClientMediaType() == 'image/png' ||
                $answerFile->getClientMediaType() == 'image/jpeg' ) {
                $image_a = $this->moveUploadedFile( $answerFile );
            } else if ( $answerFile->getClientMediaType() == 'audio/x-m4a' ) {
                $sound_a = $this->moveUploadedFile( $answerFile );
            } else if ( $answerFile->getClientMediaType() == 'video/mp4' ) {
                $video_a = $this->moveUploadedFile( $answerFile );
            } else {
                die("We can't support that file type, go back and try again");
            }
        }
        $sql = "INSERT INTO questions ( Q_ID, R_ID, Q_Question, Q_Answer, Q_Order, Q_Image_Question, Q_Image_Answer, Q_Sound_Question, Q_Sound_Answer, Q_Video_Question, Q_Video_Answer, Q_Points, Q_Multi )
                VALUES ( UUID(), :R_ID, :Q_Question, :Q_Answer, :Q_Order, :Q_Image_Question, :Q_Image_Answer, :Q_Sound_Question, :Q_Sound_Answer, :Q_Video_Question, :Q_Video_Answer, :Q_Points, :Q_Multi ); ";
        $stmt = $this->db->prepare( $sql );
        $stmt->execute([
            "R_ID" => $data['R_ID'], 
            "Q_Question" => $data['Q_Question'], 
            "Q_Answer" => $data['Q_Answer'], 
            "Q_Order" => $Order,
            "Q_Image_Question" => $image_q ? $image_q : null,
            "Q_Image_Answer" => $image_a ? $image_a : null,
            "Q_Sound_Question" => $sound_q ? $sound_q : null,
            "Q_Sound_Answer" => $sound_a ? $sound_a : null,
            "Q_Video_Question" => $video_q ? $video_q : null,
            "Q_Video_Answer" => $video_a ? $video_a : null,
            "Q_Points" => $data['Q_Points'],
            "Q_Multi" => $data['Q_Multi'],
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
        $sql = "SELECT r.R_Round, r.R_Order, q.Q_ID, q.Q_Question, q.Q_Answer, q.Q_Order, q.Q_Image_Question, q.Q_Image_Answer, q.Q_Sound_Question, q.Q_Sound_Answer, q.Q_Video_Question, q.Q_Video_Answer, q.Q_Multi
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