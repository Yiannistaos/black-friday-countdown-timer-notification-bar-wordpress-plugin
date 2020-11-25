
(function () {

    // Set the date we're counting down to
    var countDownDate = new Date("2020-11-28").getTime();

    // Get today's date and time
    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;
    // If the count down is finished, write some text
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("blackfriday-html-toolbar-content").innerHTML = "The Black Friday 2020 has been expired. Thank you!";
    }
    else
    {
        // Update the count down every 1 second
        var x = setInterval(function () {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            days = ('0' + days).slice(-2);
            hours = ('0' + hours).slice(-2);
            minutes = ('0' + minutes).slice(-2);
            seconds = ('0' + seconds).slice(-2);
        
            // Display the result in the element with id="blackfriday-countdown-timer"
            var html = '<div class="blackfriday-countdown-timer">';
            html += '<div class="blackfriday-countdown-days">' + days + '<div class="txt">Days</div></div>';
            html += '<div class="blackfriday-countdown-days">' + hours + '<div class="txt">Hours</div></div>';
            html += '<div class="blackfriday-countdown-days">' + minutes + '<div class="txt">Minutes</div></div>';
            html += '<div class="blackfriday-countdown-days">' + seconds + '<div class="txt">Seconds</div></div>';
            html += '</div>';


            html += "</div>";
            document.getElementById("blackfriday-countdown-timer").innerHTML = html;

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("blackfriday-html-toolbar-content").innerHTML = "The Black Friday 2020 has been expired. Thank you! :)";
            }
        }, 1000);

    }

})();