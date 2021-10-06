import React, { useState } from 'react';
import '../css/Home.css';
import ProfileSettings from '../components/ProfileSettings.js';
import IndividualsList from '../components/IndividualsList.js';

// user's dashboard
class Home extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
      // gets user info from parent App component
      user: {},
    };
    this.logout = this.logout.bind(this);
    this.updateProfile = this.updateProfile.bind(this);
  }

  logout() {
    // log out from server
    fetch(`https://api.teamify.pietrasik.top/logout`)
      .then(res => res.json())
      .then(data => {
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

          // update state with user data to be displayed
          this.setState({
            user: userData,
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

        <IndividualsList openIndividuals={individuals} />

          {/* cite resource */}
          <div>Icons made by <a href="https://www.flaticon.com/authors/becris" title="Becris">Becris</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>
      </div>
    );
  }
}



export default Home;
