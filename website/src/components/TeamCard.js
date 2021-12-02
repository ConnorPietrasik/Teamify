import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';
import LineInput from './Input.js';
import Config from './Config';

// shows info for one team
export default function TeamCard(props) {
  const [members, setMembers] = useState([]);

  const[messageToTeam, setMessageToTeam] = useState(''); // a message user can send to team upon applying

  useEffect(() => {
      // get team members from parameter
      setMembers(props.team.members);
  }, [props.team, props.team.members]);

  return (
    <div className="IndividualCard showInnerElementOnHover">
        <h3>{props.team.name}</h3>

        {props.status === 'invited' ?
            <p>"{props.messageFromTeam}"</p>
            : <></>}

        { /* show list of team members */
            members && members.length > 0 ?
            <div>
                <h4>Members:</h4>
                {members.map((member) => <p>{member.user.username}</p>)}
            </div>
            : <></>
        }

        { /* Request to Join button appears if team is open and user hasn't requested to join yet */
          props.status === 'open' ?
            <div>
                <form>
                    <LineInput placeholder={`message ${props.team.name}`} stateValue={messageToTeam} stateSetter={setMessageToTeam}/>
                </form>

                <button className="inviteBtn colorFadeEffect" onClick = {() => {
                    // send request for currently logged in user to join this team
                    fetch(Config.API + `/team/${props.team.team_id}/request`, {
                      method: 'POST',
                      credentials: 'include',
                      headers: {
                        'Content-Type': 'application/json'
                      },
                      body: JSON.stringify({
                        message: messageToTeam,
                      })
                    }).then()
                      .then(data => {
                        if (data)
                          console.log(data);
                        console.log("requested to join", props.team);
                        // move team card from Open Teams List to Requests Sent List
                        props.updateSentRequestsList({team: props.team, message: messageToTeam});
                      }).catch(console.error);

                }}>Request to Join</button>
            </div>
            : <></>}

        {props.status === 'invited' ?
            <>
            <button className="inviteBtn colorFadeEffect acceptBtn" onClick = {() => {
                // current user accepts team's invite to join them
                fetch(Config.API + `/team/${props.team.team_id}/accept`, {
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                    }).then()
                    .then(data => {
                            if (data)
                              console.log(data);
                    }).catch(console.error);

                // display info of user's new team
                props.joinTeam(props.team);
                }}>Accept</button>

            {props.invitationStatus === 2 /*disable Deny button if user already denied this team*/? 
                <button className="disabledBtn">Denied</button> :
                <button className="inviteBtn colorFadeEffect denyBtn" onClick = {() => {
                    // current user rejects team's invite to join them
                    fetch(Config.API + `/team/${props.team.team_id}/deny`, {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                        }).then()
                        .then(data => {
                                if (data)
                                  console.log(data);
                        }).catch(console.error);
                    }}>Deny</button>
                }

            </>
            : <></>}

    </div>
  );
}
