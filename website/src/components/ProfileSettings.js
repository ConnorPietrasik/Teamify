import React, { useState, useEffect } from 'react';
import '../css/Components.css';
import LineInput from './Input.js';
import MultiSelect from './inputs/MultiSelect.js';

// allowing user to view and edit their data
export default function ProfileSettings(props) {
  // keeping track of how user data values change
  const [username, setUsername] = useState('');
  const [bio, setBio] = useState('');
  const [skills, setSkills] = useState([]);

  const [editMode, setEditMode] = useState(false); // whether or not user can edit profile

  useEffect(() => { // initialize user data text input value based on parameter values
    setUsername(props.user.username + '');
    setBio(`${props.user.bio != null ? props.user.bio : ''}`);

    console.log(props.user);
    if (typeof(props.user.skills) !== 'undefined' && props.user.skills != null) {
        console.log("setting skills data: " + props.user.skills);
        setSkills(props.user.skills);
    }
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
        skills: skills,
      })
    }).then()
      .then(data => {
          if (data)
            console.log(data);

          // update on frontend
          props.updateProfile({
            username: username,
            bio: bio,
            skills: skills,
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
          if (typeof(props.user.skills) !== 'undefined' && props.user.skills != null) {
              console.log("quit edit mode: " + props.user.skills);
              setSkills(props.user.skills);
          }
      }
      else // allow editing
        setEditMode(true);
  }

  return (
    <div className="Card">
        <div>
             <button className="editBtn" onClick = {toggleEditMode}>{editMode ? "Discard Edits" : "Edit"}</button>
             <div className="nameAndPic leftAndCenter">
               <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
               <p>{ editMode ?
                    <LineInput stateValue={username} stateSetter={setUsername} noSpaces={true}/>
                    : username}
               </p>
             </div>
         </div>

        <div className="shiftRight">{`About Me: `}
            { editMode ?
             <LineInput stateValue={bio} stateSetter={setBio}/>
             : bio
            } </div>

        <div className="multiSelect">
            {editMode ?
                /* editable list of skills */
                <MultiSelect stateValue={skills} stateSetter={setSkills}/>

                : /* non editable list */
                skills.map(skill => <p>{skill}</p>)
            }</div>

        {editMode ? <button onClick = {(e) => {
         if (username !== "" && bio !== "") // only non empty inputs go through
            updateUserInfo(e);
         else
             alert("Please make sure inputs are nonempty");
        }}> Update </button> : <></>}

    </div>
  );
}
