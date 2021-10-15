import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import IndividualCard from './IndividualCard.js';

export default function IndividualsList(props) {
    // if user wants to invite more team members on team's behalf, we need to know which team user is on
    const [myTeamId, setMyTeamId] = useState(null);

    // list of people that will be displayed
    const [candidates, setCandidate] = useState([]); // people who requested to join my team
    const [invited, setInvited] = useState([]); // people my team invited
    const [openIndividuals, setOpenIndividuals] = useState([]);

    useEffect(() => { // initialize based on parameter values
      setOpenIndividuals(props.openIndividuals);
      setMyTeamId(props.myTeamId);
    }, [props]); // runs when parameter is received

    // update list of people to be displayed
    function updateAfterInviting(invitedPerson) {
        // remove an individual from list and update state
        const updatedAvailableIndividuals = openIndividuals.filter(otherUser => otherUser !== invitedPerson);
        setOpenIndividuals(updatedAvailableIndividuals);

        // add to list of invited people
        invited.push(invitedPerson);
    }

    return (
        <div>
            <h2>Find Team Members</h2>

            {invited.length > 0 && myTeamId ?
              <>
                <h3>People Invited </h3>
                <div className="IndividualsList" >
                  { /* list of people */
                    invited.map((individual) =>
                    <IndividualCard key={individual} name={individual} type="invited"
                        />)}
                  </div>
                </>
            : <></>}

            {openIndividuals.length > 0 ?
                <>
                <h3>People Available</h3>
                <div className="IndividualsList" >
                  { /* list of people */
                    openIndividuals.map((individual) =>
                    <IndividualCard key={individual} name={individual}
                        type={myTeamId ? "open" : ""} /* determines whether or not invite button shows */

                        /* Individuals List passes function to Individual Card child component
                            to let Card notify List when List needs to be updated */
                        updateList={updateAfterInviting}
                        />)}
                  </div>
                  </>
              : <></>}
          </div>
    );
}