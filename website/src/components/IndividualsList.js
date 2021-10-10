import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import IndividualCard from './IndividualCard.js';

export default function IndividualsList(props) {
    // list of people that will be displayed in categories of open, been invited
    const [openIndividuals, setOpenIndividuals] = useState([]);
    const [invited, setInvited] = useState([]);

    useEffect(() => { // initialize based on parameter values
      setOpenIndividuals(props.openIndividuals);
    }, [props.openIndividuals]); // runs when parameter is received

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

            {invited.length > 0 ?
              <>
                <h3>People I've Invited </h3>
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
                <h3>People Available to Team Up </h3>
                <div className="IndividualsList" >
                  { /* list of people */
                    openIndividuals.map((individual) =>
                    <IndividualCard key={individual} name={individual} type="open"

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
