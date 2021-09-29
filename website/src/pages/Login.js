import '../css/Login.css';
import Home from './Home';
import React, { useState } from 'react';
import jwt_decode from "jwt-decode";

// To let user log in / sign up with username & password
class Login extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      username: "",
      password: "",
    };
    this.authenticate = this.authenticate.bind(this);
    this.handleCredentialResponse = this.handleCredentialResponse.bind(this);
  }

  // after user clicks sign in / sign up button
  authenticate(event) {
     event.preventDefault();  // prevent page refresh
      console.log(this.state.username);
      console.log(this.state.password);

      this.props.updateUserLoginInfo(this.state.username);  // send log in info to parent component
  }

  // Google Sign In callback: gets info from user after log in
  handleCredentialResponse(response) {
    // using user info, get account data associated w/ user
    var token = response.credential;
    var tokenDecoded = jwt_decode(token);

    // redirect to home page
    this.setState({ username: tokenDecoded.email }); // set username
    this.props.updateUserLoginInfo(this.state.username); 
  }

  render() {
    console.log(this.state.username);
    window.handleCredentialResponse = this.handleCredentialResponse; // set google sign in callback function

    return (
      <div className="Login">
        <h1>Welcome</h1>

        {/* Google sign in button */}
        <div id="g_id_onload"
             data-client_id="82664365493-qm3h7p8dsqkri7f4mbuc0jmjk02ednv7.apps.googleusercontent.com"
             data-context="signin"
             data-ux_mode="popup"
             data-callback="handleCredentialResponse"
             data-auto_prompt="false">
        </div>
        <div className="g_id_signin"
             data-type="standard"
             data-shape="pill"
             data-theme="outline"
             data-text="signin_with"
             data-size="large"
             data-logo_alignment="left">
        </div>

        {/* Multipurpose Login & SignUp Form */}

        <form onSubmit={this.authenticate} className="signup-login-form">
            {/* Username & password input fields */}
            <div>
              <input type="text" placeholder="Username"
              value={this.state.username}
              onChange = {e => this.setState({ username: e.target.value })}
                  />
            </div>
            <div>
            <input type="text" placeholder="Password"
              value={this.state.password}
              onChange = {e => this.setState({ password: e.target.value })}
                  />
            </div>

            {/* Login & Signup buttons */}
            <button type="submit">Sign Up </button>
            <button type="submit">Log In </button>
        </form>

      </div>
    );
  }
}


export default Login;
