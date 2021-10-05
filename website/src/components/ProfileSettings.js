import React, { useState, useEffect } from 'react';
import '../css/Components.css';

// allowing user to view and edit their data
export default function ProfileSettings(props) {
  // keeping track of how user data values change
  const [username, setUsername] = useState('');
  const [bio, setBio] = useState('');

  useEffect(() => { // initialize user data text input value based on parameter values
    setUsername(props.user.username + '');
    setBio(`${props.user.bio != null ? props.user.bio : ''}`);
  }, [props.user]); // runs when user parameter is received

  // after user clicks button to update user info changes
  function updateUserInfo(e) {
    e.preventDefault(); // prevent page refresh

    // update to API
    fetch(`https://api.teamify.pietrasik.top/user`, {
      method: 'PUT',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: username,
        bio: bio,
      })
    }).then(res => res.json())
      .then(data => {
          if (data)
            console.log(data);

          // update on frontend
          props.updateProfile({
            username: username,
            bio: bio,
          })
      }).catch(console.error);
  }

  return (
    <div className="Card">
     <form>
        <h3>Edit Profile</h3>
        <div><label>Username:<input type="text" value={username} onChange = {e => setUsername(e.target.value) }/></label></div>
        <div><label>About Me:<input type="text" value={bio} onChange = {e => setBio(e.target.value) }/></label></div>
     </form>

     <button onClick ={updateUserInfo}> Update </button>

    </div>
  );
}
