import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import Config from './Config';

// show info for one candidate who applied to team
export default function CandidateCard(props) {
  return (
    <div className="IndividualCard">
      <div className="nameAndPic">
        <img className="profilePic" src="https://cdn-icons-png.flaticon.com/512/847/847969.png" />
        <p>{props.candidateData.user.username}</p>
      </div>

      { props.teamMemberRole === 1 /* display accept/reject buttons only if user is team leader */ ?
         <div>
          <p>{props.candidateData.message ? props.candidateData.message : ''}</p>
          <button className="inviteBtn colorFadeEffect" onClick = {() => {
              // accept this candidate into team of user currently logged in, if user is leader
              fetch(Config.API + `/team/${props.myTeamId}/accept/${props.candidateData.user.user_id}`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                  'Content-Type': 'application/json'
                },
                }).then()
                .then(data => {
                    props.accept(props.candidateData);
                }).catch(console.error);

          }}>Accept</button>

          <button className="inviteBtn colorFadeEffect" onClick = {() => {
              // reject this candidate
              fetch(Config.API + `/team/${props.myTeamId}/deny/${props.candidateData.user.user_id}`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                  'Content-Type': 'application/json'
                },
                }).then()
                .then(data => {
                }).catch(console.error);
              props.reject(props.candidateData);

          }}>Reject</button>
      </div>
    : <></>}

    </div>
  );
}
