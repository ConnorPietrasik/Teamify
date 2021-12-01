import React, { useState, useEffect } from 'react';

import Input from '@mui/material/Input';
import FormControl from '@mui/material/FormControl';
import InputLabel from '@mui/material/InputLabel';

// accepts a string with no spaces
export default function WordInput(props) {

  // when user makes a change, make sure input is still valid
  function handleInputChange(e) {
      if (e.target.value.includes(" ")) { // invalid inputs - reject space characters
        e.target.value = e.target.value.replace(/\s/g, "");
      }
      
      // send spaceless string input to parent component
      props.updateStateFunction(e.target.value);
  }

  return(
    <FormControl sx={{ m: 1, width: '25ch' }} variant="standard">

      <InputLabel>{props.label}</InputLabel>

      <Input {...props.inputFieldStyle}
        value={props.stateValue /* input field content */ }

        onChange={handleInputChange}
      />
    </FormControl>
  );
}