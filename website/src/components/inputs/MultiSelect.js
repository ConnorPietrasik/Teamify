import React, { useState, useEffect } from 'react';

import makeAnimated from 'react-select/animated';
import CreatableSelect from 'react-select/creatable';

import '../../css/Components.css';

// used when user wants to choose specific skills on profile
export default function MultiSelect(props) {
    const [chosenOptions, setChosenOptions] = useState(null); // for displaying selected choices

    useEffect(() => {
        // prefilled selections based on existing user data passed from parameter
        // turn array of strings (from user's retrieved database info given as string[]) into array of objects (format expected by library)
        var prefilledOptions = [];
        for (var i = 0; i < props.stateValue.length; i++) {
            var userChoice = {};
            userChoice.value = props.stateValue[i];
            userChoice.label = props.stateValue[i];
            prefilledOptions[i] = userChoice;
        }
        setChosenOptions(prefilledOptions);
    }, [props.stateValue]);

    return(
        <div>
        { props.stateValue && chosenOptions /* wait for data to be ready before rendering */ ?
            <CreatableSelect
                closeMenuOnSelect={false} isClearable={false} isMulti components={makeAnimated()}
                theme={(theme) => ({...theme,
                  borderRadius: 0,
                  colors: {
                    ...theme.colors,
                    primary: 'black',
                  },
                })}
                defaultValue={chosenOptions} // prefilled options selected
                options={[ // hardcoded suggestions for user's options, shown in drop down
                    {value: 'Multithreading', label: "Multithreading"},
                    {value: 'Synchronization', label: "Synchronization"},
                    {value: 'Parallel Processing', label: "Parallel Processing"},
                    {value: 'CPU Scheduling', label: "CPU Scheduling"},
                ]}

                // when user adds or deletes an option
                onChange={(selectedData) => {
                    // record user's choices to parent component, in case user decides to update info in parent
                    // turn given array of objects into array of strings to be match database storage format
                    props.stateSetter(selectedData.map(selectedOption => selectedOption.label));
                }}
                />
        : <></>}
        </div>
    );
}
