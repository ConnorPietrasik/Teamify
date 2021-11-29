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
      teamId: -1,
      teamMemberRole: -1,
      refreshTeamCard: false, // to notify child Team List component to refresh
    };
    this.updateProfile = this.updateProfile.bind(this);
    this.updateTeam = this.updateTeam.bind(this);
    this.refreshTeamCard = this.refreshTeamCard.bind(this);
  }

  componentDidMount() {
    console.log(this.props.userId);

      this.setState({
        user: this.props.user,
      });

     // get team_id and team member status for environmenmt
     fetch(`https://api.teamify.pietrasik.top/env/${this.props.envId}/user/${this.props.userId}`)
       .then(res => res.json())
       .then(userData => {
         if (userData.status === "error")
           console.log(userData);
         if (userData.team)
           this.setState({
             teamId: userData.team,
             teamMemberRole: userData.status,
           });
       }).catch(console.error);
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
      {() => this.componentDidMount() /* get user info again in case environment changes */}
        <div>
          <p className="mediumText">Hello {this.state.user.username}</p>
          <ProfileSettings
            user={this.state.user} /* for user to see their current data and decide to change it */

            /* method passed from this component to Settings component,
                to allow this component to update user data for display
                after user info gets updated in backend by Settings component  */
            updateProfile={this.updateProfile}
            envId={this.props.envId}
            />
        </div>

        {this.props.envId ? /* check env_id so lists may rerender */
        <>
        <TeamsList updateTeam={this.updateTeam} myTeamId={this.state.teamId}

            /* Team List listens to this state for changes
                when change is detected, Team List will refresh Team Card */
            refreshTeamCard={this.state.refreshTeamCard}
            envId={this.props.envId}
            />

        <IndividualsList
            /* Individuals List may tell Home that Team Card needs to be refreshed
                (when user accepts a candidate, candidate needs to be added to team) */
            refreshTeamCard={this.refreshTeamCard}

            myTeamId={this.state.teamId} /* user (as team member) may look for individuals on behalf of team */
            envId={this.props.envId}
            teamMemberRole={this.state.teamMemberRole}
            />
            </> : <></>}
      </div>
    );
  }
}



export default Home;
