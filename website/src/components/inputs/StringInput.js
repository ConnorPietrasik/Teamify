import React, { useState, useEffect } from 'react';

import Input from '@mui/material/Input';
import FormControl from '@mui/material/FormControl';
import InputLabel from '@mui/material/InputLabel';

// accepts a string with spaces
export default function WordInput(props) {

  // when user makes a change, make sure input is still valid
  function handleInputChange(e) {
      // first char shouldn't be a space
      if (e.target.value.charAt(0) === " ") 
          e.target.value = e.target.value.substring(1);
      
      // send string input to parent component
      props.updateStateFunction(e.target.value);
  }

  return(
    <FormControl sx={{ width: '100%' }} variant="standard">

      <InputLabel>{props.label}</InputLabel>

      <Input {...props.inputFieldStyle}
        value={props.stateValue /* input field content */ }

        onChange={handleInputChange}
      />
    </FormControl>
  );
}