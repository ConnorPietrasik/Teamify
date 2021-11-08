import React, { useState, useEffect } from 'react';

import IndividualCard from './IndividualCard.js';

// list of people available to team up
export default function AvailableList(props) {
    if (!props.openIndividuals)
        return <div>loading</div>;
    return(
        <>
        <h3>People Available</h3>
        <div className="IndividualsList" >
          { /* list of people */
            props.openIndividuals.map((individual) =>
            <IndividualCard key={individual} individual={individual}
                type={props.myTeamId ? "open" : ""} /* determines whether or not invite button shows */

                /* Individuals List passes function to Individual Card child component
                    to let Card notify List when List needs to be updated */
                updateList={props.updateList}
                />)}
          </div>
          </>
    );
}
