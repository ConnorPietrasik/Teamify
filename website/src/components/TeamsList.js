import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';
import TeamCard from './TeamCard';

// show list of teams if user has no team, user's team if they have one
export default function TeamsList(props) {
    const [openTeams, setOpenTeams] = useState([]);

    const [myTeam, setMyTeam] = useState(null);

    useEffect(() => { // initialize based on parameter values
      setOpenTeams([{name: 'openTeam1'}, {name: 'openTeam2'}, {name: 'openTeam3'}, {name: 'openTeam4'}]);
    }, [props]); // runs when parameter is received

    // makes new team with user as first team member, saves to API
    function createTeam(teamName) {
        // record new team in database

        // update on TeamsList component
        setMyTeam({
            name: teamName,
        });
        // update parent Home component
        props.updateTeam(1);
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
                    <div className="IndividualsList" >                        
                        {/* list of teams open */
                          openTeams.map((team) =>
                            <TeamCard team={team} status='open'/>
                              )}

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
