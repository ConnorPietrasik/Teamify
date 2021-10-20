import React, { useState } from 'react';
import '../css/Home.css';
import ProfileSettings from '../components/ProfileSettings.js';
import IndividualsList from '../components/IndividualsList.js';
import TeamsList from '../components/TeamsList';

// user's dashboard
class Home extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
      // gets user info from parent App component
      user: {},
      teamId: null,
    };
    this.logout = this.logout.bind(this);
    this.updateProfile = this.updateProfile.bind(this);
    this.updateTeam = this.updateTeam.bind(this);
  }

  logout() {
    // log out from server
    fetch(`https://api.teamify.pietrasik.top/logout`, {
      method: 'POST',
      credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
          },
    }).catch(console.error);

    // change page display
    this.props.updateUserLoginInfo(""); // clearing username will make parent App render Login instead of Home
  }

  componentDidMount() {
    console.log("mounted");

    // get user data using userID
    fetch(`https://api.teamify.pietrasik.top/user/${this.props.userId}`)
      .then(res => res.json())
      .then(userData => {
        if (userData)
          console.log(userData);

        // set team id if user has a team
        var teamId = null;
        if (userData.teams.length > 0)
            teamId = userData.teams[0];

          // update state with user data to be displayed
          this.setState({
            user: userData,
            teamId: teamId, /* get team id of user */
          });
        }
      ).catch(console.error);
  }

  // receives updated user data to display on screen
  updateProfile(newUserData) {
    console.log(newUserData);
    this.setState({
      user: newUserData,
    });
  }

  // receives updated team id of team user belongs to
  updateTeam(newTeamId) {
    this.setState({
        teamId: newTeamId,
    });
  }

  render() {
    const individuals = ["alice", "bob", "trudy", "belle", "harry"];  // list of people available to team up

    return (
      <div className="Home">
        <div>
          <h1>Hello {this.state.user.username}</h1>
          <p>{this.state.user.bio != null ? `About Me: ${this.state.user.bio}` : ''}</p>
          <ProfileSettings
            user={this.state.user} /* for user to see their current data and decide to change it */

            /* method passed from this component to Settings component,
                to allow this component to update user data for display
                after user info gets updated in backend by Settings component  */
            updateProfile={this.updateProfile}
            />
          <button onClick={this.logout}>Log Out </button>
        </div>

        <TeamsList updateTeam={this.updateTeam} myTeamId={this.state.teamId}/>
        <IndividualsList openIndividuals={individuals}
            myTeamId={this.state.teamId} /* user (as team member) may look for individuals on behalf of team */
            />

          {/* cite resource */}
          <div>Icons made by <a href="https://www.flaticon.com/authors/becris" title="Becris">Becris</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
      </div>
    );
  }
}



export default Home;
