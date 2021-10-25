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
              // accept this candidate into team of user currently logged in, if user is leader
              fetch(`https://api.teamify.pietrasik.top/team/${props.myTeamId}/accept/${props.candidate.user_id}`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                  'Content-Type': 'application/json'
                },
                }).then()
                .then(data => {
                }).catch(console.error);
          }}>Accept</button>

          <button className="inviteBtn colorFadeEffect" onClick = {() => {
              // reject this candidate
              fetch(`https://api.teamify.pietrasik.top/team/${props.myTeamId}/deny/${props.candidate.user_id}`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                  'Content-Type': 'application/json'
                },
                }).then()
                .then(data => {
                }).catch(console.error);
          }}>Reject</button>
      </div>
    </div>
  );
}
