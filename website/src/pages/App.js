import logo from '../logo.svg';
import '../css/App.css';
import Login from './Login';
import Home from './Home';
import React, { useState } from 'react';
import {Routes, Route} from "react-router-dom";

class App extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
      // no user logged in yet
      username: "",
      userId: -1,
    };
  }

  // updates user info and logged in status after user logs in / out
  updateUserInfo = (username, id) => {
      console.log(this.username);
      this.setState({
        username: username,
        userId: id,
      });
  };

  componentDidMount() {
    // find user if already logged in, and gets user data if already logged in
    fetch(`https://api.teamify.pietrasik.top/checkauth`, {
     method: 'GET',
     credentials: 'include',
     headers: {
       'Content-Type': 'application/json',
     }, })
     .then(res => res.json())
     .then(data => {
       if (data)
         console.log(data);

       // loads user data
       if (data.user_id)
           this.setState({
             userId: id,
        });
     }).catch(console.error);
  }

  render() {
    console.log(this.username);
    return(
    <div className="App">
        { /*  redirects to either Home or Login page depending on whether user is signed in*/
          this.state.username !== "" ?
          <Home username={this.state.username} updateUserLoginInfo={this.updateUserInfo} userId={this.state.userId}/>
          : <Login updateUserLoginInfo={this.updateUserInfo}/>}
    </div>
    );
  }
}
export default App;
