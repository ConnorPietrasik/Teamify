import '../css/Login.css';

// get info from user after log in
/*
function handleCredentialResponse(response) {
  console.log(response);
}
*/

function Login() {
  return (
    <div className="Login">
      <h1>Welcome</h1>

      <div id="g_id_onload"
           data-client_id="82664365493-qm3h7p8dsqkri7f4mbuc0jmjk02ednv7.apps.googleusercontent.com"
           data-context="signin"
           data-ux_mode="popup"
           data-callback="handleCredentialResponse"
           data-auto_prompt="false">
      </div>

      <div class="g_id_signin"
           data-type="standard"
           data-shape="pill"
           data-theme="outline"
           data-text="signin_with"
           data-size="large"
           data-logo_alignment="left">
      </div>

      <button className="signOutBtn" onClick={onSignout}>sign out</button>

    </div>
  );
}

function onSignout() {
}


export default Login;
