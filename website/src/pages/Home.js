import React, { useState } from 'react';

// user's dashboard
class Home extends React.Component{
  constructor(props) {
    super(props);
    this.logout = this.logout.bind(this);
  }

  logout() {
    this.props.updateUserLoginInfo(""); // clearing username will make parent App render Login instead of Home
  }

  render() {
    return (
      <div>
        <h1>Hello {this.props.username}</h1>
        <button onClick={this.logout}>Log Out </button>
      </div>
    );
  }
}

function Profile(props) {
  return (
    <div>
      <h1>Name</h1>
    </div>
  );
}

export default Home;
