//<![CDATA[
window.onload = function() {
    setTimeout(doAjax, 0);   
}
function enterSubmit(key) {
    var enterornot=key.keyCode? key.keyCode : key.charCode
    if ( enterornot == 13 ) {
        document.quizPlayer.submit();
    }
}
function submitForm() {
    document.quizPlayer.submit();
}
var interval = 3000;  // 1000 = 1 second, 3000 = 3 seconds
function doAjax() {
    $.ajax({
        type: 'GET',
        url: '/ajax',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (data) {
            if ( data.Refresh ) {
                window.location.href = '/';
            } else if ( data.Begin ) {
                $('#Q_ID').val(null);
                $('#quizData').html(null)
            } else if ( data.Scoring ) {
                $('#Q_ID').val(null);
                $('#quizData').html('<h2>Scores</h2>');
                $.each(data.Scores, function(i, Score) {
                    $('#quizData').append(
                        `<p class="ScoreData"><span>${Score.P_Name}</span>${Score.A_Correct} / ${Score.Q_Possible}</p>`
                    );
                });
            } else if ( data.Question_ID ) {
                if ( data.Question_ID != $('#Q_ID').val() ) {
                    $('#Q_ID').val(data.Question_ID);
                    $('#quizData').html('<h2 id="R_Round">'+data.R_Round+'</h2>');
                    if ( data.Q_Question ) {
                        $('#quizData').append('<h3 id="Q_Question">'+data.Q_Question+'</h3>');
                    }
                    if ( data.Q_Image_Question ) {
                        $('#quizData').append('<img id="Q_Image_Question" class="playerImage" src="/uploads/'+data.Q_Image_Question+'" />');
                    }
                    if ( data.Q_Sound_Question ) {
                        $('#quizData').append('<audio controls><source src="/uploads/'+data.Q_Sound_Question+'" type="audio/mp4"></audio>');
                    }
                    if ( data.Q_Video_Question ) {
                        $('#quizData').append('<video src="/uploads/'+data.Q_Video_Question+'" controls width="320" height="240"></video>');
                    }
                    if ( data.Q_Image_Answer ) {
                        $('#quizData').append('<img id="Q_Image_Answer" class="playerImage" src="/uploads/'+data.Q_Image_Answer+'" />');
                    }
                    if ( data.Q_Sound_Answer ) {
                        $('#quizData').append('<audio controls><source src="/uploads/'+data.Q_Sound_Answer+'" type="audio/mp4"></audio>');
                    }
                    if ( data.Q_Video_Answer ) {
                        $('#quizData').append('<video src="/uploads/'+data.Q_Video_Answer+'" controls width="320" height="240"></video>');
                    }
                    if ( data.A_Answer ) {
                        $('#quizData').append('<input class="'+data.Score+'" id="A_Answer" type="text" name="A_Answer" value="'+data.A_Answer+'" disabled />');
                    } else {
                        $('#quizData').append('<input id="A_Answer" type="text" name="A_Answer" value="" required="required" autocomplete="off" onKeyPress="enterSubmit(event); autofocus" />');
                        $('#quizData').append('<button id="Submit" type="Submit" onclick="submitForm();">Send Answer</button>');
                    }
                    if ( data.Q_Answer ) {
                        $('#quizData').append('<input id="Q_Answer" type="text" value="'+data.Q_Answer+'" disabled />');
                    }
                }
            }
        },
        complete: function (data) {
            // Schedule the next
            setTimeout(doAjax, interval);
        }
    });
}