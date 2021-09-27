import React, { useState } from 'react';
import '../css/Home.css';

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
    const individuals = ["alice", "bob", "trudy"];  // list of people available to team up

    return (
      <div className="Home">
        <div>
          <h1>Hello {this.props.username}</h1>
          <button onClick={this.logout}>Log Out </button>
        </div>

        <div className="IndividualsList" style={{display : 'inline-block'}}>
          <h2>Find Team Members</h2>
          { /* list of people */
            individuals.map((individual) =>
            <ProfileCard name={individual} />)}
          </div>
      </div>
    );
  }
}

// shows info for one individual
function ProfileCard(props) {
  return (
    <div className="IndividualCard">
      <p>{props.name}</p>
    </div>
  );
}

export default Home;
