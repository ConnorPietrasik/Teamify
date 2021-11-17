import React, { useState, useEffect } from 'react';

import MultiSelect from './MultiSelect.js';

/* component to process user skill data between parent component and component form external library
  props:
    skills[]: [{env_id: int, skill: string}]
  library's expectation:
    options[]: [{value: a unique value, label: string being displayed}]
*/
export default function ProfileSkillsSelect(props) {
    const [prefilledOptions, setPrefilledOptions] = useState(null);

    // get data from parent to be displayed
    // process data read from database > library's expectation
    useEffect(() => {
        // turn array of objects from database's format into format expected by library
        var prefilledOptionsMaker = [];
        for (var i = 0; i < props.stateValue.length; i++) {
            var userChoice = {};
            var skillStr = props.stateValue[i].skill;
            userChoice.value = skillStr;
            userChoice.label = skillStr;
            prefilledOptionsMaker[i] = userChoice;
        }
        setPrefilledOptions(prefilledOptionsMaker);
    }, [props.stateValue]);

    // updating parent after user interacts with skills select feature
    // process library's repsonse > storeable format for database
    function fromLibToDatabase(libraryResponse) {
        // convert array format to match database storage format
        var intoDatabase = [];
        for (var i = 0; i < libraryResponse.length; i++) {
            var skillObj = {};
            skillObj.skill = libraryResponse[i].label;
            skillObj.env_id = 0;
            intoDatabase[i] = skillObj;
        }
        props.stateSetter(intoDatabase);
    }

    return(
        <div className="profileSkillsSelect">
            { prefilledOptions ?
                <MultiSelect
                    placeholder={'Add my skills'}
                    stateValue={prefilledOptions} // what is given to MultiSelect to display on default
                    stateSetter={fromLibToDatabase} // where MultiSelect will report user's changes to
                    />
                : <></>
            }
        </div>
    );
}
