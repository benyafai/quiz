<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\PhpRenderer as View;
require __DIR__ . '/../vendor/autoload.php';
$app = \DI\Bridge\Slim\Bridge::create();

$app->add(function (Request $request, RequestHandler $handler) {
    $view = new View;
    $view->setTemplatePath(__DIR__ . '/../src/templates/');
    $view->setLayout("layout.phtml");
    $request = $request->withAttribute('view', $view);
    $response = $handler->handle($request);
    return $response;
});
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);
$app->post('/newgame', function (Request $request, Response $response) {
    $G = new Games();
    $data = $request->getParsedBody();
    if ( $data['Password'] == getenv('LOGIN') ) {
        $Game = $G->newGame( $data );
        $P = new Players();
        $P->joinGame([
            "G_ID" => $Game,
            "P_Name" => $data['P_Name'],
            "P_Host" => 1,
        ]);
        return $response
            ->withHeader('Location', '/game/'.$Game.'#scroll')
            ->withStatus(200);
    } else {
        return $request->getAttribute('view')->render($response, "newGame.phtml", [
            "Player" => $Player, 
            "openGraphTitle" => "New quiz", 
            "errorMessage" => "Failed", 
        ]);
    }
})->setName('newGame');

$app->map(['GET', 'POST'], '/join/{activeGame}', function (Request $request, Response $response, $activeGame) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( $Player && $Game ) {
        return $response
            ->withHeader('Location', '/game/'.$Player->G_ID.'#scroll')
            ->withStatus(200);
    }
    $Game = $G->getGame( $activeGame );
    if ( $Game ) {
        if ( $request->getMethod() == 'POST') {
            $data = $request->getParsedBody();
            $P = new Players();
            $P->joinGame([
                "G_ID" => $Game->G_ID,
                "P_Name" => $data['P_Name'],
                "P_Host" => 0,
            ]);
            return $response
                ->withHeader('Location', '/game/'.$Game->G_ID.'#scroll')
                ->withStatus(200);
        }
        return $request->getAttribute('view')->render($response, "joinGame.phtml", [
            "openGraphTitle" => "Join ".$Game->G_Name, 
            "Game" => $Game,
        ]);
    } else {
        return $request->getAttribute('view')->render($response, "404.phtml", [
            "openGraphTitle" => "No Game Found", 
        ]);
    }
})->setName('joinGame');

$app->get('/end/{G_ID}', function (Request $request, Response $response, $G_ID) {
    $G = new Games();
    $Game = $G->getGame( $G_ID );
    if ( $Game ) {
        $G->endGame( $G_ID );
        setcookie('Player', null, time() -10, "/");
        return $response
            ->withHeader('Location', '/')
            ->withStatus(200);
    } else {
        return $request->getAttribute('view')->render($response, "404.phtml", [
            "openGraphTitle" => "No Game Found", 
        ]);
    }
})->setName('joinGame');

$app->map(['GET', 'POST'], '/game/{activeGame}', function (Request $request, Response $response, $activeGame) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $activeGame );
    if ( !$Player && !$Game ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    if ( $Player && !$Game ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    if ( $Game && !$Player ) {
        return $response
            ->withHeader('Location', '/join/'.$activeGame)
            ->withStatus(401);
    }
    if ( $Player->P_Host == 1 ) {
        $Q = new Questions();
        $P = new Players();
        $A = new Answers();
        $S = new Scores();
        $Rounds = $Q->getRounds();
        $Questions = $Q->getQuestions();
        $allPlayers = $P->getPlayers();
        $Answers = $A->getAnswers();
        $currentQuestion = $G->currentQuestion();
        $Scores = $S->allScores();
        return $request->getAttribute('view')->render($response, "quizHost.phtml", [
            "openGraphTitle" => $Game->G_Name, 
            "Host" => $Player,
            "Players" => $allPlayers,
            "Game" => $Game,
            "Rounds" => $Rounds,
            "Questions" => $Questions,
            "Answers" => $Answers,
            "currentQuestion" => $currentQuestion,
            "Scores" => $Scores,
        ]);
    } else {
        // Player
        if ( $request->getMethod() == 'POST') {
            $A = new Answers();
            $data = $request->getParsedBody();
            $A->saveAnswer( $data );
            return $response
                ->withHeader('Location', '/game/'.$Player->G_ID)
                ->withStatus(200);
        }
        return $request->getAttribute('view')->render($response, "quizPlayer.phtml", [
            "openGraphTitle" => $Game->G_Name, 
            "Player" => $Player,
            "Game" => $Game,
        ]);
    }
})->setName('Game');

$app->map(['GET', 'POST'], '/ajax', function (Request $request, Response $response ) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game ) { 
        setcookie('Player', null, time() -10, "/");
        $payload = [
            "Refresh" => "Refresh",
        ];
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withHeader('Content-Type', 'application/json');
    } 
    $currentQuestion = $G->currentQuestion();
    if ( $currentQuestion == 'Begin' ) {
        $payload = [
            "Begin" => "Begin"
        ];
    } else if ( $currentQuestion == 'Scoring' ) {
        $S = new Scores();
        $Scores = $S->allScores();
        $payload = [
            "Scoring" => "Scoring",
            "Me" => $Player->P_ID,
            "Scores" => $Scores,
        ];
    } else {
        $Q = new Questions();
        $A = new Answers();
        $Question = $Q->currentQuestion( $currentQuestion );
        $Answer = $A->getAnswer( $currentQuestion );
        $payload = [
            "R_Round" => $Question->R_Round,
            "Question_ID" => $Question->Q_ID,
        ];
        if ( $Answer->A_Answer ) {
            $payload["A_Answer"] = $Answer->A_Answer;
        }
        if ( $Question->Q_Image_Question ) {
            $payload["Q_Image_Question"] = $Question->Q_Image_Question;
        } else if ( $Question->Q_Sound_Question ) {
            $payload["Q_Sound_Question"] = $Question->Q_Sound_Question;
        } else if ( $Question->Q_Video_Question ) {
            $payload["Q_Video_Question"] = $Question->Q_Video_Question;
        } else {
            $payload['Q_Question'] = $Question->Q_Question;    
        }
        if ( $Game->G_ShowAnswers == 1 ) {
            $payload["Q_Answer"] = "The answer: ".$Question->Q_Answer;
            if ( $Question->Q_Image_Answer ) {
                $payload["Q_Image_Answer"] = $Question->Q_Image_Answer;
            } else if ( $Question->Q_Sound_Answer ) {
                $payload["Q_Sound_Answer"] = $Question->Q_Sound_Answer;
            } else if ( $Question->Q_Video_Answer ) {
                $payload["Q_Video_Answer"] = $Question->Q_Video_Answer;
            }
            if ( $Answer->A_Marked == 1 && $Answer->A_Correct > 0 ) {
                $payload["Score"] = "Correct";
            } else if ( $Answer->A_Marked == 1 && $Answer->A_Correct == 0 ) {
                $payload["Score"] = "Incorrect";
            } else {
                $payload["Score"] = "Waiting";
            }
        }
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withHeader('Content-Type', 'application/json');
})->setName('ajax');

$app->map(['GET', 'POST'], '/ajaxHost/{Q_ID}', function (Request $request, Response $response, $Q_ID ) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game ) { 
        setcookie('Player', null, time() -10, "/");
        $payload = [
            "Refresh" => "Refresh",
        ];
        $response->getBody()->write(json_encode($payload));
        return $response
            ->withHeader('Content-Type', 'application/json');
    } 
    $currentQuestion = $G->currentQuestion();
    if ( $Player->P_Host == 0 || $currentQuestion == 'Begin' || $currentQuestion == 'Scoring') {
        $payload = [
        ];
    } else {
        $A = new Answers();
        $Answers = $A->getAnswersForQuestion( $Q_ID );
        $payload = $Answers;
    }
    $response->getBody()->write(json_encode($payload));
    return $response
        ->withHeader('Content-Type', 'application/json');
})->setName('ajaxHost');

$app->post('/add', function (Request $request, Response $response) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    $data = $request->getParsedBody();
    $uploadedFiles = $request->getUploadedFiles();
    $questionFile = $uploadedFiles['questionFile'];
    $answerFile = $uploadedFiles['answerFile'];
    switch ( $data['Submit'] ) {
        case "Round":
            $Q = new Questions();
            $Q->addRound( $data );
            break;
        case "Question":
            $Q = new Questions();
            $Q->addQuestion( $data, $questionFile, $answerFile );
            break;
    }
    return $response
        ->withHeader('Location', '/')
        ->withStatus(200);
})->setName('add');

$app->get('/setQuestion/{Question}', function (Request $request, Response $response, $Question) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    $G->setCurrent( $Question );
    return $response
        ->withHeader('Location', '/game/'.$Player->G_ID.'#scroll')
        ->withStatus(401);
})->setName('setQuestion');

$app->get('/showAnswers/{showHide}', function (Request $request, Response $response, $showHide) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    $G->showAnswers( $showHide );
    return $response
        ->withHeader('Location', '/game/'.$Player->G_ID.'#scroll')
        ->withStatus(401);
})->setName('showAnswers');

$app->get('/marking/{A_ID}/{A_Correct}', function (Request $request, Response $response, $A_ID, $A_Correct) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game || $Player->P_Host == 0 ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    $A = new Answers();
    $A->mark( $A_ID, $A_Correct );
    return $response
        ->withHeader('Location', '/game/'.$Player->G_ID.'#scroll')
        ->withStatus(401);
})->setName('marking');

$app->get('/player/{P_ID}/{Action}', function (Request $request, Response $response, $P_ID, $Action) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( !$Player || !$Game || $Player->P_Host == 0 ) { 
        return $response
            ->withHeader('Location', '/')
            ->withStatus(401);
    } 
    $P = new Players();
    switch ( $Action ) {
        case "host":
            $P->makeHost( $P_ID );
            break;
        case "player":
            $P->makePlayer( $P_ID );
            break;
        case "kick":
            $P->kick( $P_ID );
            break;
    }
    return $response
        ->withHeader('Location', '/game/'.$Player->G_ID.'#scroll')
        ->withStatus(401);
})->setName('marking');

$app->get('/', function (Request $request, Response $response) {
    $G = new Games();
    $Player = $G->checkPlayer();
    $Game = $G->getGame( $Player->G_ID );
    if ( $Player && $Game ) {
        return $response
            ->withHeader('Location', '/game/'.$Player->G_ID.'#scroll')
            ->withStatus(200);
    } else if ( $Player && !$Game ) { 
        setcookie('Player', null, time() -10, "/");
        return $response
            ->withHeader('Location', '/')
            ->withStatus(200);
    } 
    return $request->getAttribute('view')->render($response, "newGame.phtml", [
        "Player" => $Player, 
        "openGraphTitle" => "Quiz Time!", 
    ]);
})->setName('home');

$app->run();