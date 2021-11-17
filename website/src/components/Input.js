export default function LineInput(props) {
    // when user types and input field values need to change
    function handleInputChange(e, updateStateFunction) {
        if (props.noSpaces && e.target.value.includes(" ")) { // invalid username inputs - reject space characters
          e.target.value = e.target.value.replace(/\s/g, "");
        }
        else if (e.target.value.charAt(0) === " ") // invalid bio input - if first char is a space, delete it
          e.target.value = e.target.value.substring(1);
        else // valid inputs - update state variable
          updateStateFunction(e.target.value);
    }

    return(
        <input type="text" value={props.stateValue} placeholder={props.placeholder}
           style={{width: '80%'}}
           onChange = {e => handleInputChange(e, props.stateSetter) }/>
    );
}
