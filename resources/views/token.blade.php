{{--
 * LaraClassified - Geo Classified Ads CMS
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@section('content')
	@include('common.spacer')
	<div class="main-container">
		<div class="container">
			<div class="row">

				@if (isset($errors) and $errors->any())
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<ul class="list list-check">
								@foreach ($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif

				@if (session('code'))
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<p>{{ session('code') }}</p>
						</div>
					</div>
				@endif

				@if (Session::has('flash_notification'))
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
					
				<div class="col-lg-12">
					<div class="alert alert-info">
						{{ getTokenMessage() }}:
					</div>
				</div>

				<div class="col-sm-5 login-box">
					<div class="panel panel-default">
						<div class="panel-intro text-center">
							<h2 class="logo-title">
								<span class="logo-icon"> </span> {{ t('Code') }} <span> </span>
							</h2>
						</div>
						
						<div class="panel-body">
							<form id="tokenForm" role="form" method="POST" action="{{ lurl(Request::path()) }}">
								{!! csrf_field() !!}
								
								<!-- Token -->
								<div style="display:none" class="form-group <?php echo (isset($errors) and $errors->has('code')) ? 'has-error' : ''; ?>">
									<label for="code" class="control-label">{{ getTokenLabel() }}:</label>
									<div class="input-icon"><i class="fa icon-lock-2"></i>
										<input id="code" name="code" type="hidden" placeholder="{{ t('Enter the validation code') }}" value="{{ session('regUser')->phone_token }}" value="{{ old('code') }}">
									</div>
								</div>
								
								<!-- Submit -->
								{{-- <div class="form-group"> --}}
									{{-- <button id="tokenBtn" type="submit" class="btn btn-primary btn-lg btn-block">{{ t('Submit') }}</button> --}}
								{{-- </div> --}}
              </form>
              
              <form class="text-center" id="sign-in-form" action="#">
                <!-- Input to enter the phone number -->
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                  <input disabled class="form-control mdl-textfield__input" type="text" pattern="\+[0-9\s\-\(\)]+" id="phone-number" value="{{ session('phone') }}">
                  {{-- <label class="mdl-textfield__label" for="phone-number">Enter your phone number...</label> --}}
                  {{-- <span class="mdl-textfield__error">Input is not an international phone number!</span> --}}
                </div>
                <br><code>Ex. +919898989898</code><br><br>
                <!-- Sign-in button -->
                <button disabled class="btn btn-primary btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="sign-in-button">Press to Send Code</button>
              </form>

              <!-- Button that handles sign-out -->
              <button style="display:none" class="btn btn-primary btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="sign-out-button">Re-send</button>

              <form style="display:none" id="verification-code-form" action="#">
                <!-- Input to enter the verification code -->
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                  <input placeholder="Enter your verification code here..." class="form-control mdl-textfield__input" type="text" id="verification-code">
                  <label class="mdl-textfield__label" for="verification-code">Enter the verification code...</label>
                </div>

                <!-- Button that triggers code verification -->
                <input type="submit" class="btn btn-primary btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="verify-code-button" value="Verify Code"/>
                <!-- Button to cancel code verification -->
                <button class="btn btn-danger btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="cancel-verify-code-button">Cancel</button>
              </form>
						</div>
						
						<div class="panel-footer">
							<p class="text-center"></p>
							<div style=" clear:both"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_scripts')
	<script>
		$(document).ready(function () {
			$("#tokenBtn").click(function () {
				$("#tokenForm").submit();
				return false;
			});
		});
	</script>

<script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script>
<script type="text/javascript">
    // Initialize Firebase
    var config = {
        // apiKey: "AIzaSyDkxiArVnAS7fASiGd64m9-eaowNCUAI4g",
        // authDomain: "dealnotdealios.firebaseapp.com",
        // databaseURL: "https://dealnotdealios.firebaseio.com",
        // // projectId: "dealnotdealios",
        // storageBucket: "dealnotdealios.appspot.com",
        // messagingSenderId: "976649279867",
        // // appId: "1:976649279867:web:6a4c11707b2e6f913705de",
        // // measurementId: "G-X5GPSFGCKJ"
        apiKey: "AIzaSyCQSZX-HXslGe8gVHbb6_D35bdYQrS2WFw",
        authDomain: "dealnotdealweb.firebaseapp.com",
        databaseURL: "https://dealnotdealweb.firebaseio.com",
        // projectId: "dealnotdealweb",
        storageBucket: "dealnotdealweb.appspot.com",
        messagingSenderId: "670297019656",
        // appId: "1:670297019656:web:bf1e222cf20dca3e0208bd",
        // measurementId: "G-HD7HZT1YR1"
    };
    firebase.initializeApp(config);

    var database = firebase.database();
  /**
   * Set up UI event listeners and registering Firebase auth listeners.
   */
  window.onload = function() {
    // Listening for auth state changes.
    firebase.auth().onAuthStateChanged(function(user) {
      if (user) {
        // User is signed in.
        var uid = user.uid;
        var email = user.email;
        var photoURL = user.photoURL;
        var phoneNumber = user.phoneNumber;
        var isAnonymous = user.isAnonymous;
        var displayName = user.displayName;
        var providerData = user.providerData;
        var emailVerified = user.emailVerified;
      }
      updateSignInButtonUI();
      updateSignInFormUI();
      updateSignOutButtonUI();
      updateSignedInUserStatusUI();
      updateVerificationCodeFormUI();
    });

    // Event bindings.
    document.getElementById('sign-out-button').addEventListener('click', onSignOutClick);
    document.getElementById('phone-number').addEventListener('keyup', updateSignInButtonUI);
    document.getElementById('phone-number').addEventListener('change', updateSignInButtonUI);
    document.getElementById('verification-code').addEventListener('keyup', updateVerifyCodeButtonUI);
    document.getElementById('verification-code').addEventListener('change', updateVerifyCodeButtonUI);
    document.getElementById('verification-code-form').addEventListener('submit', onVerifyCodeSubmit);
    document.getElementById('cancel-verify-code-button').addEventListener('click', cancelVerification);

    // [START appVerifier]
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('sign-in-button', {
      'size': 'invisible',
      'callback': function(response) {
        // reCAPTCHA solved, allow signInWithPhoneNumber.
        onSignInSubmit();
      }
    });
    // [END appVerifier]

    recaptchaVerifier.render().then(function(widgetId) {
      window.recaptchaWidgetId = widgetId;
      updateSignInButtonUI();
    });
  };

  /**
   * Function called when clicking the Login/Logout button.
   */
  function onSignInSubmit() {
    if (isPhoneNumberValid()) {
      window.signingIn = true;
      updateSignInButtonUI();
      var phoneNumber = getPhoneNumberFromUserInput();
      var appVerifier = window.recaptchaVerifier;
      firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
          .then(function (confirmationResult) {
            // SMS sent. Prompt user to type the code from the message, then sign the
            // user in with confirmationResult.confirm(code).
            window.confirmationResult = confirmationResult;
            window.signingIn = false;
            updateSignInButtonUI();
            updateVerificationCodeFormUI();
            updateVerifyCodeButtonUI();
            updateSignInFormUI();
          }).catch(function (error) {
            // Error; SMS not sent
            console.error('Error during signInWithPhoneNumber', error);
            window.alert('Error during signInWithPhoneNumber:\n\n'
                + error.code + '\n\n' + error.message);
            window.signingIn = false;
            updateSignInFormUI();
            updateSignInButtonUI();
          });
    }
  }

  /**
   * Function called when clicking the "Verify Code" button.
   */
  function onVerifyCodeSubmit(e) {
    e.preventDefault();
    if (!!getCodeFromUserInput()) {
      window.verifyingCode = true;
      updateVerifyCodeButtonUI();
      var code = getCodeFromUserInput();
      confirmationResult.confirm(code).then(function (result) {
        console.log('Done');
        $('#sign-out-button').fadeOut();
        document.getElementById('sign-out-button').style.display = 'none';
        $("#tokenForm").submit();
        // User signed in successfully.
        var user = result.user;
        window.verifyingCode = false;
        window.confirmationResult = null;
        updateVerificationCodeFormUI();
      }).catch(function (error) {
        // User couldn't sign in (bad verification code?)
        console.log('Not');
        console.error('Error while checking the verification code', error);
        window.alert('Error while checking the verification code:\n\n'
            + error.code + '\n\n' + error.message);
        window.verifyingCode = false;
        updateSignInButtonUI();
        updateVerifyCodeButtonUI();
      });
    }
  }

  /**
   * Cancels the verification code input.
   */
  function cancelVerification(e) {
    e.preventDefault();
    window.confirmationResult = null;
    updateVerificationCodeFormUI();
    updateSignInFormUI();
  }

  /**
   * Signs out the user when the sign-out button is clicked.
   */
  function onSignOutClick() {
    firebase.auth().signOut();
  }

  /**
   * Reads the verification code from the user input.
   */
  function getCodeFromUserInput() {
    return document.getElementById('verification-code').value;
  }

  /**
   * Reads the phone number from the user input.
   */
  function getPhoneNumberFromUserInput() {
    return document.getElementById('phone-number').value;
  }

  /**
   * Returns true if the phone number is valid.
   */
  function isPhoneNumberValid() {
    var pattern = /^\+[0-9\s\-\(\)]+$/;
    var phoneNumber = getPhoneNumberFromUserInput();
    return phoneNumber.search(pattern) !== -1;
  }

  /**
   * Re-initializes the ReCaptacha widget.
   */
  function resetReCaptcha() {
    if (typeof grecaptcha !== 'undefined'
        && typeof window.recaptchaWidgetId !== 'undefined') {
      grecaptcha.reset(window.recaptchaWidgetId);
    }
  }

  /**
   * Updates the Sign-in button state depending on ReCAptcha and form values state.
   */
  function updateSignInButtonUI() {
    document.getElementById('sign-in-button').disabled =
        !isPhoneNumberValid()
        || !!window.signingIn;
  }

  /**
   * Updates the Verify-code button state depending on form values state.
   */
  function updateVerifyCodeButtonUI() {
    document.getElementById('verify-code-button').disabled =
        !!window.verifyingCode
        || !getCodeFromUserInput();
  }

  /**
   * Updates the state of the Sign-in form.
   */
  function updateSignInFormUI() {
    if (firebase.auth().currentUser || window.confirmationResult) {
      document.getElementById('sign-in-form').style.display = 'none';
    } else {
      resetReCaptcha();
      document.getElementById('sign-in-form').style.display = 'block';
    }
  }

  /**
   * Updates the state of the Verify code form.
   */
  function updateVerificationCodeFormUI() {
    if (!firebase.auth().currentUser && window.confirmationResult) {
      document.getElementById('verification-code-form').style.display = 'block';
    } else {
      document.getElementById('verification-code-form').style.display = 'none';
    }
  }

  /**
   * Updates the state of the Sign out button.
   */
  function updateSignOutButtonUI() {
    if (firebase.auth().currentUser) {
      document.getElementById('sign-out-button').style.display = 'block';
    } else {
      document.getElementById('sign-out-button').style.display = 'none';
    }
  }

  /**
   * Updates the Signed in user status panel.
   */
  function updateSignedInUserStatusUI() {
    var user = firebase.auth().currentUser;
    if (user) {
      document.getElementById('sign-in-status').textContent = 'Signed in';
      document.getElementById('account-details').textContent = JSON.stringify(user, null, '  ');
    } else {
      document.getElementById('sign-in-status').textContent = 'Signed out';
      document.getElementById('account-details').textContent = 'null';
    }
  }
</script>	
@endsection