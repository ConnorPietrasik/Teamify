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
    const individuals = ["alice", "bob", "trudy", "belle", "harry"];  // list of people available to team up

    return (
      <div className="Home">
        <div>
          <h1>Hello {this.props.username}</h1>
          <button onClick={this.logout}>Log Out </button>
        </div>

        <h2>Find Team Members</h2>
        <div className="IndividualsList" >
          { /* list of people */
            individuals.map((individual) =>
            <ProfileCard key={individual} name={individual} />)}
          </div>

          {/* cite resource */}
          <div>Icons made by <a href="https://www.flaticon.com/authors/becris" title="Becris">Becris</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
      </div>
    );
  }
}

// shows info for one individual
function ProfileCard(props) {
  return (
    <div className="IndividualCard">
      <div className="nameAndPic">
        <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
        <p>{props.name}</p>
      </div>
    </div>
  );
}

export default Home;
