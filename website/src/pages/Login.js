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
      errorMessage: "", // log in error message
    };
    this.handleInputChange = this.handleInputChange.bind(this);
    this.authenticate = this.authenticate.bind(this);
    this.handleCredentialResponse = this.handleCredentialResponse.bind(this);
  }

  //  input values change as user types one character at a time
  handleInputChange(e, isUsername) {
    if (e.target.value.includes(" ")) { // reject space characters
      e.target.value = e.target.value.replace(/\s/g, "");
    }
    else if (isUsername) // update username
      this.setState({ username: e.target.value });
    else  // update password
      this.setState({ password: e.target.value });
  }

  // after user clicks sign in / sign up button
  authenticate(username, password) {
      console.log(username);
      console.log(password);

      // write to api
      fetch(`https://api.teamify.pietrasik.top/login`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          username: username,
          password: password,
        })
      }).then(res => res.json())
        .then(data => {
          if (data)
            console.log(data);

          // if unsuccessful, show user error message
          if (data.code !== 200)
            this.setState({ errorMessage: data.message });

          // log in if sucessful
          if (data.status !== 'error') {
            console.log("login ok");
            this.props.updateUserLoginInfo(data.user_id);  // send log in info to parent component, go to home screen
          }
        }).catch(console.error);
  }

  // ran after user clicks sign up button
  signUp(username, password) {
    console.log("making new acct");
    // write to api
    fetch(`https://api.teamify.pietrasik.top/register`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: username,
        password: password,
      })
    }).then(res => res.json())
      .then(data => {
        if (data)
          console.log(data);

        // if unsuccessful, show user error message
        if (data.code !== 200)
          this.setState({ errorMessage: data.message });

        // log in
        if (data.status !== 'error') {
            this.authenticate(username, password);
        }

      }).catch(console.error);
    console.log("after api call");

  }

  // Google Sign In callback: gets info from user after log in
  handleCredentialResponse(response) {
    // using user info, get account data associated w/ user
    var token = response.credential;
    var tokenDecoded = jwt_decode(token);

    // redirect to home page
    this.setState({ username: tokenDecoded.email }); // set username
    this.props.updateUserLoginInfo(4);
  }

  render() {
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
        <form className="signup-login-form">
            {/* Username & password input fields */}
            <div>
              <input type="text" placeholder="Username" id="usernameInputField"
              value={this.state.username}
              onChange = {e => {this.handleInputChange(e, true)}}
                  />
            </div>
            <div>
            <input type="text" placeholder="Password" id="passwordInputField"
              value={this.state.password}
              onChange = {e => {this.handleInputChange(e, false)}}
                  />
            </div>

            {/* Login & Signup buttons */}
            <button onClick ={(e) => {
                e.preventDefault(); // prevent page refresh
                if (this.state.username === "" || this.state.password === "") // make sure there're no empty inputs
                  this.setState({ errorMessage: "Please enter a valid username and/or password." });
                else
                  this.signUp(this.state.username, this.state.password);
              }} >Sign Up </button>
            <button id="loginBtn" onClick = {(e) => {
              e.preventDefault();
              if (this.state.username === "" || this.state.password === "") {
                this.setState({ errorMessage: "Please enter a valid username and/or password." });
              } else
                this.authenticate(this.state.username, this.state.password);  // logs user in
            }}>Log In </button>
        </form>
        { /* display error message if login or signup was unsuccessful */
          this.state.errorMessage === "" ? <></> : <p style={{color: "red"}}> {this.state.errorMessage} </p>}
      </div>
    );
  }
}


export default Login;
