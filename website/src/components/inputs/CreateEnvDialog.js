import * as React from 'react';

import StringInput from './StringInput.js';
import WordInput from './WordInput.js';

import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';

import Config from '../Config';

// pop up to join a new environment
export default function CreateEnvDialog(props) {
  const [isOopen, setIsOpen] = React.useState(false); // whether or not dialog is being shown

  // values user enters into input field
  const [nameInput, setNameInput] = React.useState('');
  const [codeInput, setCodeInput] = React.useState(''); 

  // after user clicks join button
  function onClickCreate() {
    console.log(nameInput);
    console.log(codeInput);
    if (codeInput.length > 0)
    fetch(Config.API + `/env/create`, {
      method: 'POST',
      credentials: 'include',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        name: nameInput,
        code: codeInput,
      })
      }).then(res => res.json())
      .then(envReturned => {
        if (envReturned.status !== "error") {
          console.log(envReturned);

          setIsOpen(false);
          setNameInput('');
          setCodeInput('');

          // update UI
          props.addNewEnvironment({
            env_id: envReturned.env_id,
            name: nameInput,
          });
        } else
          alert("Error in joining environment. " + envReturned.message);
      }).catch(console.error);
  }

  return (
    <div>
      <button onClick={() => setIsOpen(true)}>
        Create Environment
      </button>

      <Dialog open={isOopen} onClose={() => setIsOpen(false)}>
        <DialogTitle>Create Environment</DialogTitle>

        <DialogContent>
          <p>
            To create a new environment, please give it a name and create an invitation code to give to people you want to invite.
          </p>

          <StringInput 
            label='Environment Name'
            inputFieldStyle={{fullWidth: 'true'}}
            
            stateValue={nameInput}
            updateStateFunction={setNameInput} />

          <WordInput 
            label='Invitation Code'
            inputFieldStyle={{fullWidth: 'true'}}
            
            stateValue={codeInput}
            updateStateFunction={setCodeInput} />

        </DialogContent>

        <button onClick={onClickCreate}>Create</button>

      </Dialog>
    </div>
  );
}
