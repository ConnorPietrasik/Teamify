import React, { useState, useEffect } from 'react';
import '../css/Home.css';

// show info for one candidate who applied to team
export default function CandidateCard(props) {
  return (
    <div className="IndividualCard">
      <div className="nameAndPic">
        <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
        <p>{props.candidate.user_id}</p>
      </div>
      <div>
          <button className="inviteBtn colorFadeEffect" onClick = {() => {
          }}>Accept</button>

          <button className="inviteBtn colorFadeEffect" onClick = {() => {
          }}>Reject</button>
      </div>
    </div>
  );
}
