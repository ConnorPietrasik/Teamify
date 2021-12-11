import * as React from 'react';

import WordInput from './WordInput.js';

import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';

import Config from '../Config';

// pop up to join a new environment
export default function JoinDialog(props) {
  const [isOopen, setIsOpen] = React.useState(false); // whether or not dialog is being shown

  const [codeInput, setCodeInput] = React.useState(''); // value user enters into input field

  // after user clicks join button
  function onClickJoin() {
    if (codeInput.length > 0)
    fetch(Config.API + `/env/join`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        code: codeInput,
      })
      }).then(res => res.json())
      .then(envReturned => {
        if (envReturned.status !== "error") {
          console.log(envReturned);

          setIsOpen(false);
          setCodeInput('');

          // update UI
          props.addNewEnvironment({
            env_id: envReturned.env_id,
            name: envReturned.name,
          });
        } else
           alert("Error in joining environment. " + envReturned.message);
      }).catch(console.error);

    console.log(codeInput);
  }

  return (
    <div>
      <button onClick={() => setIsOpen(true)}>
        Join Environment
      </button>

      <Dialog open={isOopen} onClose={() => setIsOpen(false)}>
        <DialogTitle>Join Environment</DialogTitle>

        <DialogContent>
          <p>
            If you have an environment invitation code, please enter the code to join.
          </p>

          <WordInput 
            label='Environment Code'
            inputFieldStyle={{fullWidth: 'true'}}
            
            stateValue={codeInput}
            updateStateFunction={setCodeInput} />

        </DialogContent>

        <button onClick={onClickJoin}>Join</button>

      </Dialog>
    </div>
  );
}
