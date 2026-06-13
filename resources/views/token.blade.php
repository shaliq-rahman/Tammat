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
<p style="color:white">
@include('common.spacer')
</p>
	<div class="main-container">
		<div class="container">
			<div class="row">

				<?php if(isset($errors) and $errors->any()){?>
                 
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
				
              
              <?php  }?>

				<?php if(session('code')){?>
					<div class="col-lg-12">
						<div class="alert alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<p>{{ session('code') }}</p>
						</div>
					</div>
				<?php }?>

				<?php if(Session::has('flash_notification')){?>
					<div class="container" style="margin-bottom: -10px; margin-top: -10px;">
						<div class="row">
							<div class="col-lg-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				<?php }?>
					
				<div class="col-lg-12">
					<div class="alert alert-info">
						{{ getTokenMessage() }}:
					</div>
				</div>

				<div class="col-sm-5 login-box">
					<div class="panel panel-default">
						<div class="panel-intro text-center" style="display:none">
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
                    <div style="display:none" >
                    
                            <!-- Input to enter the phone number -->
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                              <input disabled class="form-control mdl-textfield__input" type="text" pattern="\+[0-9\s\-\(\)]+" id="phone-number" value="{{ session('phone') }}">
                              {{-- <label class="mdl-textfield__label" for="phone-number">Enter your phone number...</label> --}}
                              {{-- <span class="mdl-textfield__error">Input is not an international phone number!</span> --}}
                            </div>
                            <br><code>Ex. +91989898989844</code><br><br>
                      
                    </div>
                
                    <div class="panel-intro text-center">
                      <h2 class="logo-title">
                        <span class="logo-icon">   <h1 style="font-size: 64px;font-weight: bold;">1 </h1></span>
                      </h2>
				            </div>
                
                
                <!-- Sign-in button -->
                <button disabled class="btn btn-primary btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="sign-in-button">{{ t('Press to Send Code') }}</button>
                
                <div class="panel-intro text-center">
                  <h2 class="logo-title">
                    <span class="logo-icon">   <h1 style="font-size: 64px;font-weight: bold;">2 </h1></span>
                  </h2>
				        </div>
                        
                        
              </form>

              <!-- Button that handles sign-out -->
              <button style="display:none" class="btn btn-primary btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="sign-out-button">{{ t('Re-send') }}</button>
              <input type="hidden" id="sign-in-status" name="sign-in-status"/>
              <input type="hidden" id="account-details" name="account-details"/>  
              
              <form  id="verification-code-form" action="#">
                <!-- Input to enter the verification code -->
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                  <input placeholder="{{ t('Enter your verification code here...') }}" class="form-control mdl-textfield__input" type="text" id="verification-code">
                  <label class="mdl-textfield__label" for="verification-code">{{ t('Enter the verification code...') }}</label>
                </div>

                <!-- Button that triggers code verification -->
                <input type="submit" class="btn btn-primary btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="verify-code-button" value="{{ t('Verify Code') }}"/>
                <!-- Button to cancel code verification -->
                <button class="btn btn-danger btn-lg btn-block mdl-button mdl-js-button mdl-button--raised" id="cancel-verify-code-button">{{ t('Cancel') }}</button>
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
      
      /*
      this is last configration   
        apiKey: "AIzaSyCQSZX-HXslGe8gVHbb6_D35bdYQrS2WFw",
        authDomain: "dealnotdealweb.firebaseapp.com",
        databaseURL: "https://dealnotdealweb.firebaseio.com",
        // projectId: "dealnotdealweb",
        storageBucket: "dealnotdealweb.appspot.com",
        messagingSenderId: "670297019656",
        // appId: "1:670297019656:web:bf1e222cf20dca3e0208bd",
        // measurementId: "G-HD7HZT1YR1"
        */

        
        apiKey: "AIzaSyD8GdePwqtn_AUN98KKj8eddsxOwND3Wkg",
  authDomain: "fifth-tensor-355408.firebaseapp.com",
  databaseURL: "https://fifth-tensor-355408-default-rtdb.firebaseio.com/",
  projectId: "fifth-tensor-355408",
  storageBucket: "fifth-tensor-355408.appspot.com",
  messagingSenderId: "551696870023",
  appId: "1:551696870023:web:c11b31855c04aab32fe622",
  measurementId: "G-019G8VRYP2"
 // 551696870023-nejripgpfuvtcj2k37rkishokt3g0rnc.apps.googleusercontent.com
    

    };
    firebase.initializeApp(config);

    var database = firebase.database();
   // let confirmationResult; // Declaration in a scope accessible to both functions
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
          //reCAPTCHA solved, allow signInWithPhoneNumber.
         // console.log('allow signInWithPhoneNumber');
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
            
          //  console.log('confirmationResult');
          //  console.log(window.confirmationResult);
          //  console.log(confirmationResult);

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
   
   // console.log(e);
   // console.log(getCodeFromUserInput());


    console.log('aaaaaaaa');    

    console.log(window.confirmationResult);
    console.log(confirmationResult);
    console.log('bbbbbbbbb');

    var verificationCode = getCodeFromUserInput();

 


     //for test 2s    
    e.preventDefault();
    if (!!getCodeFromUserInput()) {

      //console.log('cccccc');
      //console.log(confirmationResult);
      //console.log(window.confirmationResult);
      //console.log('dddddddd');

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
    //console.log("1111");
    //console.log(window.confirmationResult);
    //console.log("2222");
    //console.log(firebase.auth().currentUser);
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
    //console.log("start user Info");
   // console.log(user);
   // console.log("end user Info");
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