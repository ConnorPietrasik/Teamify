import React, { useState, useEffect } from 'react';
import '../css/Components.css';
import IndividualCard from './IndividualCard.js';
import Config from './Config';


export default function ProfileSettings(props) {

	const [editMode, setEditMode] = useState(false);

	// when user wants to start or stop editing
    function toggleEditMode() {
      if (editMode) { // quit editing
          setEditMode(false);

          // restore original values
      }
      else // allow editing
        setEditMode(true);
  }


	return (
		<div style={{display: 'block'}}>
			<div className="Card">
				{console.log(props.team)}
	             <button className="editBtn" onClick={toggleEditMode}>{editMode ? "Discard Edits" : "Edit"}</button>
	             <div className="shiftRight">
	               <h3>{props.team.name}</h3>
	             </div>

	            {editMode ? <button onClick = {(e) => {
	            		setEditMode(false);
			        }}> Update </button> : <></>}

			</div>

			<div className="IndividualsList" >
				{console.log(props.team.members)}
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