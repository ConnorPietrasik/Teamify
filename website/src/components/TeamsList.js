import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';
import TeamCard from './TeamCard';

// show list of teams if user has no team, user's team if they have one
export default function TeamsList(props) {
    const [teamRequestsSent, setTeamRequestsSent] = useState([]);
    const [openTeams, setOpenTeams] = useState([]);

    const [myTeam, setMyTeam] = useState(null);

    useEffect(() => { // initialize based on parameter values
      setOpenTeams([{name: 'openTeam1'}, {name: 'openTeam2'}, {name: 'openTeam3'}, {name: 'openTeam4'}]);
    }, [props]); // runs when parameter is received

    // makes new team with user as first team member, saves to API
    function createTeam(teamName) {
        // record new team in database
        fetch(`https://api.teamify.pietrasik.top/env/1/createteam`, {
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
        const remainingOpenTeams = openTeams.filter(otherTeam => otherTeam !== teamAppliedTo);
        setOpenTeams(remainingOpenTeams);

        // add team to Requests Sent List
        teamRequestsSent.push(teamAppliedTo);
    }

    return (
        <div>
            {myTeam ?
                <div>
                    <h2>My Team</h2>
                    <div className="IndividualsList" >
                        <TeamCard team={myTeam}/>
                    </div>
                </div>
                :
                <div>
                    <h2>Find Teams</h2>

                    { teamRequestsSent.length > 0 ?
                        <>
                            <h3>Teams Applied To</h3>
                            <div className="IndividualsList" >
                                {/* list of teams that user requested to join */
                                  teamRequestsSent.map((team) =>
                                    <TeamCard team={team} status='applied'/>
                                    )}
                            </div>
                        </>
                        : <></>}

                    { openTeams.length ?
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
        <div id="createTeamCard" className="IndividualCard">
            Create a Team
            <form>
                <input type="text" placeholder="team name" onChange={e => setNewTeamName(e.target.value)}/>
                <button onClick={e =>
                    props.submitNewTeam(newTeamName) /* send new name input to parent's update function*/
                }>Create</button>
            </form>
        </div>
    );
}
