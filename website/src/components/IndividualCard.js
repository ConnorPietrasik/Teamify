import React, { useState, useEffect } from 'react';
import '../css/Home.css';

// shows info for one individual
export default function IndividualCard(props) {
  return (
    <div className="IndividualCard">
      <div className="nameAndPic">
        <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
        <p>{props.name}</p>
      </div>
      <div>
      <button className="inviteBtn colorFadeEffect" onClick = {() => {
          // tells Individuals List to remove this card
          props.updateList(props.name);
      }}>Invite</button>
      </div>
    </div>
  );
}
