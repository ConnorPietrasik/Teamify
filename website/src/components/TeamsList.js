import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';
import TeamCard from './TeamCard';
import TeamProfile from './TeamProfile';
import StringInput from './inputs/StringInput.js';
import Config from './Config';

// show list of teams if user has no team, user's team if they have one
export default function TeamsList(props) {
    const [teamRequestsSent, setTeamRequestsSent] = useState([]);
    const [teamRequestsReceived, setTeamRequestsReceived] = useState([]);
    const [openTeams, setOpenTeams] = useState([]);

    const [myTeam, setMyTeam] = useState(null);

    useEffect(() => { // initialize based on parameter values
      // setOpenTeams([{name: 'openTeam1', team_id: '-1'}, {name: 'openTeam2', team_id: '-2'}, {name: 'openTeam3', team_id: '-3'}, {name: 'openTeam4', team_id: '-4'}]);
        console.log("props.envId", props.envId)
        console.log("myTeamId", props.myTeamId)
      // get team data using team id parameter
      if (props.myTeamId[0] > -1) {
          console.log("fetch data for myTeamId", props.myTeamId)
          fetch(Config.API + `/team/${props.myTeamId}`)
            .then(res => res.json())
            .then(teamData => {
                console.log("team: ", teamData);

                setMyTeam(teamData);
            }).catch(console.error);
        }
        // show available teams
        else {
            getAndDisplayTeams();
        }
    }, [props.myTeamId, props.refreshTeamCard]); // runs when parameter is received

    // if user isn't in team, show list of teams available
    function getAndDisplayTeams() {
        // get requests sent and received
        getTeamRequests();

        // list of open individuals who aren't in requests sent / received
        const idsOfTeamsApplied = teamRequestsSent.map((requestData) => requestData.team.team_id);
        const idsOfTeamsInvitedMe = teamRequestsReceived.map((requestData) => requestData.team.team_id);

        // get teams available
        fetch(Config.API + `/env/${props.envId}/teams`, {
          method: 'GET',
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json'
          }
          }).then(res => res.json())
          .then(teamData => {
            // add filter to get only available teams that current user hasn't applied to yet
            // or teams that haven't invited current user
            setOpenTeams(teamData);
        }).catch(console.error);
        setMyTeam(null);
    }

    function getTeamRequests() {
        // get teams that have invited me
        fetch(Config.API + `/user/invites`, {
            method: 'GET',
            credentials: 'include',
            headers: {'Content-Type': 'application/json'}
            }).then(res => res.json())
            .then(teamData => {
                setTeamRequestsReceived(teamData.filter(invitation => invitation.team.env_id === props.envId));
            }).catch(console.error);

        // get teams I've applied to
        fetch(Config.API + `/user/requests`, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'Content-Type': 'application/json'
            },
          })
        .then(res => res.json())
        .then(teamData => {
            setTeamRequestsSent(teamData.filter(joinRequest => joinRequest.team.env_id === props.envId));
        }).catch(console.error);
    }

    // makes new team with user as first team member, saves to API
    function createTeam(teamName) {
        // record new team in database
        fetch(Config.API + `/env/${props.envId}/createteam`, {
          method: 'POST',
          credentials: 'include',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            name: teamName,
          })
        }).then(res => res.json())
          .then(data => {
            if (data)
              console.log(data);

            if (data.team_id) { // successfully created and returned id
                // update on TeamsList component
                setMyTeam({
                    name: teamName,
                });
                // update parent Home component
                props.updateTeam(data.team_id);
            }
          }).catch(console.error);
    }

    function afterApplyingToTeam(teamAppliedTo) {
        // api

        // remove from Open Teams List
        const remainingOpenTeams = openTeams.filter(otherTeam => otherTeam.team_id !== teamAppliedTo.team.team_id);
        setOpenTeams(remainingOpenTeams);

        // add team to Requests Sent List
        var newTeamRequestsSent = [...teamRequestsSent] // copy values
        newTeamRequestsSent.push(teamAppliedTo)
        setTeamRequestsSent(newTeamRequestsSent);
    }

    return (
        <div> 
            {myTeam ?
                <div>
                    <h2>My Team</h2>
                    <div className="IndividualsList" >
                        <TeamProfile team={myTeam}
                            updateTeam={setMyTeam}
                            userId={props.userId}
                            />
                    </div>
                </div>
                :
                <div>
                    <h2>Find Teams</h2>

                    { teamRequestsReceived ?
                        <>
                            <h3>Teams That Invited Me</h3>
                            <div className="IndividualsList" >
                                {/* list of teams that invited user to join */
                                  teamRequestsReceived.map((teamData) =>
                                    <TeamCard team={teamData.team} status='invited'
                                        messageFromTeam={teamData.message}
                                        invitationStatus={teamData.status}
                                        joinTeam={setMyTeam} // method to be called (to update UI) if user decides to join team
                                        />
                                    )}
                            </div>
                        </>
                        : <></>}

                    { teamRequestsSent ?
                        <>
                            <h3>Teams Applied To</h3>
                            <div className="IndividualsList" >
                                {console.log("displayed team requests", teamRequestsSent)}
                                {/* list of teams that user requested to join */
                                  teamRequestsSent.map((teamData) =>
                                    <TeamCard team={teamData.team} status='applied'/>
                                    )}
                            </div>
                        </>
                        : <></>}

                    { openTeams ?
                        <>
                            <h3>Teams Open</h3>
                            <div className="IndividualsList" >
                                {/* list of teams open */
                                  openTeams.map((team) =>
                                    <TeamCard team={team} status='open'

                                      /* when user wants to apply to team */
                                      updateSentRequestsList={afterApplyingToTeam}
                                      />)}
                            </div>
                        </>
                        : <></> }

                        <div className="IndividualsList" >
                            {/* show button to start a team if user doesn't have one */}
                            <CreateTeamCard submitNewTeam={createTeam} />
                        </div>
                </div>}
          </div>
    );
}

// to take in user input for creating a new team name
function CreateTeamCard(props) {
    const[newTeamName, setNewTeamName] = useState('');

    return (
        <div className="IndividualCard showInnerElementOnHover">
            Create a Team
            <form>
                <StringInput 
                  inputFieldStyle={{fullWidth: 'true', 
                    placeholder: "team name",
                    style: { marginTop: '0', marginBottom: '10px' }
                  }}
                  
                  stateValue={newTeamName}
                  updateStateFunction={setNewTeamName} />

                <button onClick={e => {
                    e.preventDefault();
                    props.submitNewTeam(newTeamName) /* send new name input to parent's update function*/
                }}>Create</button>
            </form>
        </div>
    );
}
