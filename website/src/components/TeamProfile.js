import React, { useState, useEffect } from 'react';
import '../css/Components.css';
import IndividualCard from './IndividualCard.js';
import MultiSelect from './inputs/MultiSelect.js';
import Config from './Config';

import Chip from '@mui/material/Chip';
import Stack from '@mui/material/Stack';


export default function TeamProfile(props) {

	const [editMode, setEditMode] = useState(false);
	const [skillsLookingFor, setSkillsLookingFor] = useState([]);

	const [teamMemberRole, setTeamMemberRole] = useState(-1);

    useEffect(() => {
        // find user and get member status       
        var currentUser = props.team.members.filter(member => member.user.user_id === props.userId) 
        setTeamMemberRole(currentUser[0].status);
    }, [props.userId, props.team]);

	useEffect(() => { 
		// turn into object [] format for library component
		setSkillsLookingFor(strArrayToObjArray(props.team.looking_for));
	}, [props.team.looking_for, editMode, props.envId]);

	function strArrayToObjArray(strArray) {
		var prefilledOptionsMaker = [];

        for (var i = 0; i < strArray.length; i++) {
            var skillOption = {};
            var skillStr = strArray[i];
            skillOption.value = skillStr;
            skillOption.label = skillStr;
            prefilledOptionsMaker[i] = skillOption;
        }
		return prefilledOptionsMaker;
	}

	// when user wants to start or stop editing
    function toggleEditMode() {
      if (editMode) { // quit editing
          setEditMode(false);

          // restore original values
      }
      else // allow editing
        setEditMode(true);
  }

  
	function updateTeamInfo() {

		// update to API
		fetch(Config.API + `/team/${props.team.team_id}`, {
		  method: 'PUT',
		  credentials: 'include',
		  headers: {
		    'Content-Type': 'application/json'
		  },
		  body: JSON.stringify({
		    looking_for: skillsLookingFor.map(skillObj => skillObj.label),
		  })
		}).then()
		  .then(data => {
		      if (data.status === 200) {
			      // update on frontend
			      props.updateTeam({
			        ...props.team, /* keep unchanged values */
			        looking_for: skillsLookingFor.map(skillObj => skillObj.label),
			      });
			      setEditMode(false);		      	
		      }
		}).catch(console.error);
	}


	return (
		<div style={{display: 'block'}}>
			<div className="Card">
				<div className={teamMemberRole === 1 ? 'editBtn' : ''}>
	             {teamMemberRole === 1 ? 
	             	<button  onClick={toggleEditMode}>{editMode ? "Discard Edits" : "Edit"}</button>
	             	: <></>}
	            </div>

	             <div className={teamMemberRole === 1 ? 'shiftRight' : ''}>
	               <h3>{props.team.name}</h3>
	             </div>

	            <div className="multiSelect">
	            <p>Looking for Candidates with Skills:</p>
	            {editMode ?
	                /* editable list of skills */
	                <MultiSelect className="searchInputContainer"
	                    placeholder={'Looking for candidates skilled in . . .'}
	                    stateValue={skillsLookingFor} // fed to MultiSelect to display as options chosen
	                    stateSetter={setSkillsLookingFor} // where MultiSelect will report user's changes to
	                    />

	                : /* non editable list */
	                <Stack direction="row" style={{flexWrap: 'wrap'}}>
	                    {props.team.looking_for.map(skillStr => <Chip style={{marginRight: '10px', marginBottom: '10px'}} label={skillStr} />)}
	                </Stack>
	            }</div>

	            {editMode ? <button onClick = {() => {
	            		updateTeamInfo();
			        }}> Update </button> : <></>}

			</div>

			<div className="IndividualsList" >
				{ props.team.members ? 
		            props.team.members.map((memberData) =>
		            <IndividualCard key={memberData} individual={memberData.user}
		                myTeamId={props.team_id}
		                />)
	                : <></>}
	        </div>
		</div>
	);
}