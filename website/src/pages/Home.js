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
      refreshTeamCard: false, // to notify child Team List component to refresh
    };
    this.logout = this.logout.bind(this);
    this.updateProfile = this.updateProfile.bind(this);
    this.updateTeam = this.updateTeam.bind(this);
    this.refreshTeamCard = this.refreshTeamCard.bind(this);
  }

  logout() {
    // log out from server
    fetch(`https://api.teamify.pietrasik.top/logout`, {
      method: 'POST',
      credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
          },
    }).then(
        // change page display
        this.props.updateUserLoginInfo(-1) // resetting user_id will make parent App render Login instead of Home
    ).catch(console.error);
  }

  componentDidMount() {
    console.log(this.props.userId);

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

  refreshTeamCard() {
    this.setState({
        // when value changed, Team Card component would be notified to refresh
        refreshTeamCard: !this.state.refreshTeamCard,
    });
  }

  render() {
    return (
      <div className="Home">
        <div>
          <h1>Hello {this.state.user.username}</h1>
          <button onClick={this.logout}>Log Out </button>
          <ProfileSettings
            user={this.state.user} /* for user to see their current data and decide to change it */

            /* method passed from this component to Settings component,
                to allow this component to update user data for display
                after user info gets updated in backend by Settings component  */
            updateProfile={this.updateProfile}
            />
        </div>

        <TeamsList updateTeam={this.updateTeam} myTeamId={this.state.teamId}

            /* Team List listens to this state for changes
                when change is detected, Team List will refresh Team Card */
            refreshTeamCard={this.state.refreshTeamCard}
            />

        <IndividualsList
            /* Individuals List may tell Home that Team Card needs to be refreshed
                (when user accepts a candidate, candidate needs to be added to team) */
            refreshTeamCard={this.refreshTeamCard}

            myTeamId={this.state.teamId} /* user (as team member) may look for individuals on behalf of team */
            />

          {/* cite resource */}
          <div>Icons made by <a href="https://www.flaticon.com/authors/becris" title="Becris">Becris</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
      </div>
    );
  }
}



export default Home;
