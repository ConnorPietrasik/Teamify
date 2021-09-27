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
      username: "",
      password: "",
    };
  }

  updateUserInfo = username => {
      console.log(this.username);
      this.setState({
        username: username,
      });
  };

render() {
  console.log(this.username);
  return(
    <div className="App">
        { /*  redirects to either Home or Login page depending on whether user is signed in*/
          this.state.username !== "" ?
          <Home username={this.state.username}/>
          : <Login updateUserLoginInfo={this.updateUserInfo}/>}
    </div>
    );
}

}
export default App;
