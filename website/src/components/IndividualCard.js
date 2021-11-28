import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';
import LineInput from './Input.js';

import Avatar from '@mui/material/Avatar';
import Chip from '@mui/material/Chip';
import Stack from '@mui/material/Stack';

// shows info for one individual
export default function IndividualCard(props) {
    const[messageToIndividual, setMessageToIndividual] = useState('');

  return (
    <div className="IndividualCard showInnerElementOnHover">
      <div className="nameAndPic">
        <Avatar className="profilePic" sx={{ bgcolor: '#2F'+`${props.individual.user_id % 10}`+'664' /* users will have different profile avatar colors */ }}>
            {props.individual.username.charAt(0) /* display first letter of username */ }</Avatar>
        <p>{props.individual.username}</p>
      </div>
      <div>

      { /* viewable list of skills */
        props.individual.skills && props.individual.skills.length > 0 ?
            <Stack direction="row" style={{flexWrap: 'wrap'}}>
                {props.individual.skills.map(skillStr => <Chip style={{marginRight: '10px', marginBottom: '10px'}} label={skillStr} />)}
            </Stack>
           : <></>}

      { props.type === "open" ?
        <div>
            <form>
                <LineInput placeholder={`hi ${props.individual.username}...`} stateValue={messageToIndividual} stateSetter={setMessageToIndividual}/>
            </form>

            <button className="inviteBtn colorFadeEffect" onClick = {() => {
                // invite a user to join my team
                fetch(`https://api.teamify.pietrasik.top/team/${props.myTeamId}/invite/${props.individual.user_id}}`, {
                  method: 'POST',
                  credentials: 'include',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({
                    message: messageToIndividual,
                  })
                }).then()
                  .then(data => {
                      if (data)
                        console.log(data);

                      // tells Individuals List to remove this card
                      props.updateList(props.individual);

                  }).catch(console.error);

            }}>Invite</button>
          </div>
          : <></>}
         </div>
    </div>
  );
}
