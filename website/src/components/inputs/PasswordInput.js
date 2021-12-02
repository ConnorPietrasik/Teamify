import React, { useState, useEffect } from 'react';

import Input from '@mui/material/Input';
import FormControl from '@mui/material/FormControl';
import InputLabel from '@mui/material/InputLabel';
import InputAdornment from '@mui/material/InputAdornment';
import Visibility from '@mui/icons-material/Visibility';
import VisibilityOff from '@mui/icons-material/VisibilityOff';
import IconButton from '@mui/material/IconButton';

// input field for password that lets uer hide
export default function PasswordInput(props) {
	const [passwordVisible, setPasswordVisible] = useState(false);

	return(
	    <FormControl sx={{ width: '100%' }} variant="standard">
          <InputLabel>Password</InputLabel>
          <Input id="text"
            type={passwordVisible ? 'text' : 'password'}

            value={props.stateValue /* user's password value */ }

            onChange={e => {props.parentInputChangeHandler(e, false) /* current password input stored in update parent's state value */}}

            endAdornment={
              <InputAdornment position="end">
                <IconButton
                  style={{color: 'grey'}}
                  onClick={() => setPasswordVisible(!passwordVisible) /* user can change password visibility setting */}
                  onMouseDown={ e => e.preventDefault()}
                  >
                  { /* show eyeball icon with or without slash depending on visibility choice*/
                  	passwordVisible ? <VisibilityOff /> : <Visibility />}
                </IconButton>
              </InputAdornment>
            }
          />
        </FormControl>
	);
}