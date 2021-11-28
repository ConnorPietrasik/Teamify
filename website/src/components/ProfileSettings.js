import React, { useState, useEffect } from 'react';
import '../css/Components.css';
import LineInput from './Input.js';
import ProfileSkillsSelect from './inputs/ProfileSkillsSelect.js';

import Avatar from '@mui/material/Avatar';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch from '@mui/material/Switch';
import Chip from '@mui/material/Chip';
import Stack from '@mui/material/Stack';

// allowing user to view and edit their data
export default function ProfileSettings(props) {
  // keeping track of how user data values change
  const [username, setUsername] = useState('');
  const [bio, setBio] = useState('');
  const [skills, setSkills] = useState([]);
  const [isOpen, setIsOpen] = useState(false);

  const [editMode, setEditMode] = useState(false); // whether or not user can edit profile

  useEffect(() => { // initialize user data text input value based on parameter values
    setUsername(props.user.username + '');
    setBio(`${props.user.bio != null ? props.user.bio : ''}`);

    if (typeof(props.user.skills) !== 'undefined' && props.user.skills != null) {
        setSkills(props.user.skills); // array of skill objects
    }
    if (props.user.open_envs)
        setIsOpen(props.user.open_envs.includes(props.envId)); // int[] of env user is available in
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
            ...props.user, /* keep unchanged values */
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

  // record changes to API and UI when user changes choice of availablility
  async function toggleIsOpen() {
      var newIsOpenChoice = !isOpen;
      var listOfEnvOpenIn = props.user.open_envs;

      if(newIsOpenChoice) {
          await fetch(`https://api.teamify.pietrasik.top/env/${props.envId}/open`, {
            method: 'POST',
            credentials: 'include',
            headers: {'Content-Type': 'application/json'}
            }).then()
            .then(data => {
                listOfEnvOpenIn.push(props.envId);
            }).catch(console.error);
      } else {
          await fetch(`https://api.teamify.pietrasik.top/env/${props.envId}/open`,{
            method:'DELETE',
            credentials: 'include',
            }).then().then(data => {
                listOfEnvOpenIn = listOfEnvOpenIn.filter(envId => envId !== props.envId); // remove current evnId
            }).catch(console.error);
      }

      // update parent Home component UI, parent's state update will cause change in current's prop values, and cause current to update
      props.updateProfile({
        ...props.user,
        open_envs: listOfEnvOpenIn,
      });
  }

  return (
    <>
    <div className="Card">
        <div>
             <button className="editBtn" onClick = {toggleEditMode}>{editMode ? "Discard Edits" : "Edit"}</button>
             <div className="nameAndPic leftAndCenter">
               <Avatar className="profilePic" sx={{ bgcolor: '#2F'+`${props.user.user_id % 10}`+'664' }}>{props.user.username ? props.user.username.charAt(0) : 'me'}</Avatar>
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
                <Stack direction="row" style={{flexWrap: 'wrap'}}>
                    {skills.map(skillObj => <Chip style={{marginRight: '10px', marginBottom: '10px'}} label={skillObj.skill} />)}
                </Stack>
            }</div>

        {editMode ? <button onClick = {(e) => {
         if (username !== "") // only non empty inputs go through
            updateUserInfo(e);
         else
             alert("Please make sure inputs are nonempty");
        }}> Update </button> : <></>}

    </div>

    {/* toggleable switch for being added to Open Individuals list*/}
    <FormControlLabel label={`Other users ${isOpen ? 'can' : 'cannot'} see my profile and invite me`}
        control={<Switch
                    style={{color:  `${isOpen ? '#6b96cf' : 'lightgrey'}`}}
                    checked={isOpen}
                    onChange={() => toggleIsOpen()} />} />
    </>
  );
}
