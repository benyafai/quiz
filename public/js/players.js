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
                $('#quizPlayer').hide()
                $('#Scoring').hide()
            } else if ( data.Scoring ) {
                $('#quizPlayer').hide()
                $('#Scoring').show()
                $('#ScoreData').empty();
                $.each(data.Scores, function(i, Score) {
                    $('#ScoreData').append(
                        `<p><span>${Score.P_Name}</span>${Score.A_Correct} / ${Score.Q_Possible}</p>`
                    );
                });
            } else if ( data.Question_ID ) {
                $('#quizPlayer').show()
                $('#Scoring').hide()
                if ( data.Q_Answer && data.Q_Answer.length ) {
                    $('#Q_Answer').val(data.Q_Answer);
                    $('#Q_Answer').show();
                    if ( data.Q_Image_Answer && data.Q_Image_Answer.length ) {
                        $('#Q_Image_Answer').show()
                        $('#Q_Image_Answer').attr("src","/uploads/"+data.Q_Image_Answer);
                    } else {
                        $('#Q_Image_Answer').attr("src","");
                        $('#Q_Image_Answer').hide()
                    }
                    if ( data.Q_Sound_Answer && data.Q_Sound_Answer.length ) {
                        $('#Q_Sound_Answer').show()
                        $('#Q_Sound_Answer').attr("src","/uploads/"+data.Q_Sound_Answer);
                    } else {
                        $('#Q_Sound_Answer').attr("src","");
                        $('#Q_Sound_Answer').hide()
                    }
                } else {
                    $('#Q_Answer').val('');
                    $('#Q_Answer').hide();
                    $('#Q_Image_Answer').hide();
                    $('#Q_Sound_Answer').hide();
                }
                if ( data.Question_ID != $('#Q_ID').val() ) {
                    $('#R_Round').text(data.R_Round);
                    $('#Q_Question').text(data.Q_Question);
                    $('#Q_ID').val(data.Question_ID);
                    if ( data.Q_Image_Question && data.Q_Image_Question.length ) {
                        $('#Q_Image_Question').show()
                        $('#Q_Image_Question').attr("src","/uploads/"+data.Q_Image_Question);
                    } else {
                        $('#Q_Image_Question').hide()
                    }
                    if ( data.Q_Sound_Question && data.Q_Sound_Question.length ) {
                        $('#Q_Sound_Question').attr("src","/uploads/"+data.Q_Sound_Question);
                        $('#Q_Sound_Question').show()
                    } else {
                        $('#Q_Sound_Question').hide()
                    }
                    if ( data.A_Answer && data.A_Answer.length ) {
                        $('#A_Answer').val(data.A_Answer);
                        $('#A_Answer').prop('disabled', true);
                        $('#Submit').hide()
                    } else {
                        $('#A_Answer').val('');
                        $('#A_Answer').prop('disabled', false);
                        $('#Submit').show()
                        $('#A_Answer').focus()
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