import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';

// shows info for one team
export default function TeamCard(props) {
  return (
    <div className="IndividualCard">
        <p>{props.team.name}</p>

        { /* Request to Join button appears if team is open and user hasn't requested to join yet */
          props.status === 'open' ?
            <button className="inviteBtn colorFadeEffect" onClick = {() => {
                // move team card from Open Teams List to Requests Sent List
                props.updateSentRequestsList(props.team);
            }}>Request to Join</button>
            : <></>}

    </div>
  );
}
