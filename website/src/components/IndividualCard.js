import React, { useState, useEffect } from 'react';
import '../css/Home.css';

// shows info for one individual
export default function IndividualCard(props) {
  return (
    <div className="IndividualCard">
      <div className="nameAndPic">
        <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
        <p>{props.individual.username}</p>
      </div>
      <div>

      { /* viewable list of skills */
        props.individual.skills && props.individual.skills.length > 0 ?
          props.individual.skills.map(skillStr => <p>{skillStr}</p>) : <></>
      }

      { props.type === "open" ?
          <button className="inviteBtn colorFadeEffect" onClick = {() => {
              // tells Individuals List to remove this card
              props.updateList(props.individual);
          }}>Invite</button>
          : <></>}
      </div>
    </div>
  );
}
