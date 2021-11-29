import React, { useState, useEffect } from 'react';

import IndividualCard from './IndividualCard.js';
import MultiSelect from './inputs/MultiSelect.js';

import '../css/Components.css';

// list of people available to team up
export default function AvailableList(props) {
    const [searchInput, setSearchInput] = useState(null); // array of skills to search (in library's format)

    const [listToDisplay, setListToDisplay] = useState(null); // list of people's data

    useEffect(() => {
        setSearchInput([]);
        setListToDisplay(props.openIndividuals);
    }, [props.openIndividuals, props.envId]);

    // when user searches individuals list for specific skills
    function search() {
        // display new results based on search query
        if (searchInput.length > 0)
            fetch(`https://api.teamify.pietrasik.top/env/${props.envId}/open/skill`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                skills: searchInput.map(searchedSkillObj => searchedSkillObj.label),
                })
                }).then(res => res.json())
                .then(individualsData => {
                    if (individualsData)
                      setListToDisplay(individualsData); // change list of individuals displayed
            }).catch(console.error);
    }

    if (!listToDisplay || !searchInput)
        return <div>loading</div>;
    return(
        <>
        <h3>People Available</h3>

        <div className="SearchInputAndBar">
            {/* Search bar input field and search query submit button*/}
            <div className="searchInputContainer">
                <MultiSelect className="searchInputContainer"
                    placeholder={'Search for people skilled in . . .'}
                    stateValue={searchInput} // fed to MultiSelect to display as options chosen
                    stateSetter={setSearchInput} // where MultiSelect will report user's changes to
                    />
                    </div>
            <button className="searchBtn" onClick={search}>Search</button>
        </div>

        <div className="IndividualsList" >
          { listToDisplay.length > 0 ?
            listToDisplay.map((individual) =>
            <IndividualCard key={individual} individual={individual}
                myTeamId={props.myTeamId}
                type={props.myTeamId ? "open" : ""} /* determines whether or not invite button shows */

                /* Individuals List passes function to Individual Card child component
                    to let Card notify List when List needs to be updated */
                updateList={props.updateList}
                />)
            : <div>No Results</div>}
          </div>
          </>
    );
}
