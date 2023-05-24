<form class="wgcontact_captcha_1" name="contactform1" method="post" id="contactform">
    <fieldset>
        <input name="firstname" type="hidden" id="firstname" class="hide-robot">
        <input name="name" class="input_bg" type="text" id="name" value="" placeholder="Name" required>
        <input name="mob" class="input_bg" type="text" id="mob" value="" pattern="[1-9]{1}[0-9]{9}" placeholder="Mobile"
            required>
        <input name="email" class="input_bg" type="text" id="email" value="" placeholder="Email" required>       

        <textarea name="message" class="textarea_bg" id="message" cols="20" rows="1" placeholder="Message"></textarea>
        <input type="submit" name="imageField" id="imageField" class="send" value="Request A Call Back">
        <div id="alert_1"></div>
        <input type="hidden" name="recaptcha_response" id="recaptchaResponse_1" class="recaptchaResponse">
    </fieldset>
</form>


<script>
// Generic function to submit form with captcha verification
function submitFormWithCaptcha(formClass, alertId) {
  $(formClass).submit(function (event) {
    event.preventDefault(); // Prevent direct form submission

    $(alertId).text('Processing...').fadeIn(0); // Display "Processing" to let the user know that the form is being submitted
    grecaptcha.ready(function () {
      grecaptcha.execute('6Lf9m2AgAAAAAG3ElTdK22BX1qHylophGIGComSs', {
        action: 'contact'
      }).then(function (token) {
        var recaptchaResponse = document.getElementById(alertId.substr(1));
        recaptchaResponse.value = token;
        // Make the Ajax call here
        $.ajax({
          url: '/wg-submit.php',
          type: 'post',
          data: $(formClass).serialize(),
          dataType: 'json',
          success: function (_response) {
            // The Ajax request is a success. _response is a JSON object
            var resoutput = _response.output;
            if (resoutput === 1) {
              // In case of error, display it to user
              $(alertId).html("We're sorry. Your message could not be sent. We ask you to contact us by phone.");
            } else if (resoutput === 2) {
              $(alertId).html("Please fill out all fields for your request.");
            } else if (resoutput === 0) {
              // In case of success, display it to user and remove the submit button
              $(alertId).html("Thank you for your message. It has been sent.");
              window.location.href = "https://www.libertyuae.com/thank-you.php";
            } else if (resoutput === 3) {
              // Failed to send email
              $(alertId).html("Oops! Something went wrong while sending your message. Please try again later.");
            } else if (resoutput === 4) {
              // Failed to verify reCAPTCHA
              $(alertId).html("Oops! Something went wrong while verifying reCAPTCHA. Please try again later.");
            } else {
              // Handle any other code or unexpected value
              $(alertId).html("Oops! An unexpected error occurred. Please try again later.");
            }
          },
          error: function (jqXhr, json, errorThrown) {
            // In case of Ajax error, display the result
            var error = jqXhr.responseText;
            $(alertId).html(error);
          }
        });
      });
    });
  });
}

// Contact Page Captcha
submitFormWithCaptcha(".wgcontact_captcha_1", "#alert_1");
// Sidebar Captcha
submitFormWithCaptcha(".wgcontact_captcha_2", "#alert_2");
</script>
