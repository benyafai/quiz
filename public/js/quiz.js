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
                if ( data.Question_ID != $('#Q_ID').val() ) {
                    $('#R_Round').text(data.R_Round);
                    $('#Q_Question').text(data.Q_Question);
                    $('#Q_ID').val(data.Question_ID);
                    if ( data.Q_Image && data.Q_Image.length ) {
                        $('#Q_Image').show()
                        $('#Q_Image').attr("src","/uploads/"+data.Q_Image);
                    } else {
                        $('#Q_Image').hide()
                    }
                    if ( data.Q_Sound && data.Q_Sound.length ) {
                        $('#Q_Sound').show()
                        $('#Q_Sound').attr("src","/uploads/"+data.Q_Sound);
                    } else {
                        $('#Q_Sound').hide()
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