import React, { useState, useEffect } from 'react';
import '../css/Components.css';
import LineInput from './Input.js';
import ProfileSkillsSelect from './inputs/ProfileSkillsSelect.js';

import Avatar from '@mui/material/Avatar';

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

    if (typeof(props.user.skills) !== 'undefined' && props.user.skills != null) {
        setSkills(props.user.skills); // array of skill objects
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
               <Avatar className="profilePic" sx={{ bgcolor: '#2F4664' }}>{props.user.username ? props.user.username.charAt(0) : 'me'}</Avatar>
               <p>{ editMode ?
                    <LineInput stateValue={username} stateSetter={setUsername} noSpaces={true}/>
                    : username}
               </p>
             </div>
         </div>

        <div className="shiftRight">{`About Me: `}</div>
        <div>
            { editMode ?
             <LineInput stateValue={bio} stateSetter={setBio}/>
             : bio
            } </div>

        <div className="multiSelect">
            {editMode ?
                /* editable list of skills */
                <ProfileSkillsSelect stateValue={skills} stateSetter={setSkills}
                    envId={0}/>

                : /* non editable list */
                skills.map(skillObj => <p>{skillObj.skill}</p>)
            }</div>

        {editMode ? <button onClick = {(e) => {
         if (username !== "") // only non empty inputs go through
            updateUserInfo(e);
         else
             alert("Please make sure inputs are nonempty");
        }}> Update </button> : <></>}

    </div>
  );
}
