import React, { useState, useEffect } from 'react';
import '../css/Components.css';

// allowing user to view and edit their data
export default function ProfileSettings(props) {
  // keeping track of how user data values change
  const [username, setUsername] = useState('');
  const [bio, setBio] = useState('');

  const [editMode, setEditMode] = useState(false); // whether or not user can edit profile

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
          });
          setEditMode(false);
      }).catch(console.error);
  }

  // when user wants to start or stop editing
  function toggleEditMode() {
      if (editMode) { // quit editing
          setEditMode(false);

          // restore original values
          setUsername(props.user.username);
          setBio(props.user.bio);
      }
      else // allow editing
        setEditMode(true);
  }

  return (
    <div className="Card">
    <div><button className="editBtn" onClick = {toggleEditMode}>{editMode ? "Discard Edits" : "Edit"}</button></div>
         <div className="nameAndPic leftAndCenter">
           <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
           <p>{ editMode ? <input type="text" value={username} onChange = {e => setUsername(e.target.value) }/> : username}</p>
         </div>

        <div>About Me: { editMode ? <input type="text" value={bio} onChange = {e => setBio(e.target.value) }/> : bio}</div>

     {editMode ? <button onClick = {updateUserInfo}> Update </button> : <></>}

    </div>
  );
}
