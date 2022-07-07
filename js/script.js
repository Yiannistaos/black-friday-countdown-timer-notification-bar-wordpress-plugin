(function(){
    var distance = bf_pass_args.bf_diff_time;
    var endText  = bf_pass_args.bf_end_text;

    // If the count down is finished, write some text
    if (distance < 0) {bfCancelCounter(x);}
    else {
        // Update the count down every 1 second
        var x = setInterval(function(){

            // Time calculations for days, hours, minutes and seconds
            var days    = Math.floor(distance / (60 * 60 * 24));
            var hours   = Math.floor((distance % ( 60 * 60 * 24)) / (60 * 60));
            var minutes = Math.floor((distance % (60 * 60)) / 60);
            var seconds = Math.floor(distance % 60);

            days    = ('0' + days).slice(-2);
            hours   = ('0' + hours).slice(-2);
            minutes = ('0' + minutes).slice(-2);
            seconds = ('0' + seconds).slice(-2);

            distance = distance -1;

            // Display the result in the element with id="blackfriday-countdown-timer"
            var html = '<div class="blackfriday-countdown-timer">';
            html += '<div class="blackfriday-countdown-days">' + days + '<div class="txt">Days</div></div>';
            html += '<div class="blackfriday-countdown-hours">' + hours + '<div class="txt">Hours</div></div>';
            html += '<div class="blackfriday-countdown-minutes">' + minutes + '<div class="txt">Minutes</div></div>';
            html += '<div class="blackfriday-countdown-seconds">' + seconds + '<div class="txt">Seconds</div></div>';
            html += '</div>';

            document.getElementById("blackfriday-countdown-timer").innerHTML = html;

            // If the count down is finished, write some text
            if (distance < 0) {bfCancelCounter(x);}
        }, 1000);

    }

    function bfCancelCounter(timeoutHandle){
        clearInterval(timeoutHandle);
        document.getElementById("blackfriday-html-toolbar-content").innerHTML = endText;
    }
})();