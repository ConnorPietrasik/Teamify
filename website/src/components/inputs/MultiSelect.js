import React, { useState, useEffect } from 'react';

import makeAnimated from 'react-select/animated';
import CreatableSelect from 'react-select/creatable';

import '../../css/Components.css';

// used when user wants to choose specific skills on profile
export default function MultiSelect(props) {
    const [chosenOptions, setChosenOptions] = useState(null); // for displaying selected choices

    useEffect(() => {
        // prefilled selections based on existing user data passed from parameter
        setChosenOptions(props.stateValue);
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
                placeholder={props.placeholder ? props.placeholder : 'Select . . .'}
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
                    props.stateSetter(selectedData);
                }}
                />
        : <></>}
        </div>
    );
}
