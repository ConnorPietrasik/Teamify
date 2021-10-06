import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import IndividualCard from './IndividualCard.js';

export default function IndividualsList(props) {
    const [openIndividuals, setOpenIndividuals] = useState([]);

    useEffect(() => { // initialize based on parameter values
      setOpenIndividuals(props.openIndividuals);
    }, [props.openIndividuals]); // runs when parameter is received

    // update list of people to be displayed
    function updateIndividualsLists(removeMe) {
        // remove an individual from list and update state
        const updatedAvailableIndividuals = openIndividuals.filter(otherUser => otherUser !== removeMe);
        setOpenIndividuals(updatedAvailableIndividuals);
    }

    return (
        <div>
            <h2>Find Team Members</h2>
            <h3>People Available to Team Up </h3>
            <div className="IndividualsList" >
              { /* list of people */
                openIndividuals.map((individual) =>
                <IndividualCard key={individual} name={individual}

                    /* Individuals List passes function to Individual Card child component
                        to let Card notify List when List needs to be updated */
                    updateList={updateIndividualsLists}
                    />)}
              </div>
          </div>
    );
}
