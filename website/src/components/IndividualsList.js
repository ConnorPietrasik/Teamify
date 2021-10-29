import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import IndividualCard from './IndividualCard.js';
import CandidateCard from './CandidateCard.js';

export default function IndividualsList(props) {
    // if user wants to invite more team members on team's behalf, we need to know which team user is on
    const [myTeamId, setMyTeamId] = useState(null);

    // list of people that will be displayed
    const [candidates, setCandidates] = useState([]); // people who requested to join my team
    const [invited, setInvited] = useState([]); // people my team invited
    const [openIndividuals, setOpenIndividuals] = useState([]);

    useEffect(() => { // initialize based on parameter values
      if (props.myTeamId != null) {
          setMyTeamId(props.myTeamId);

          // get all requests from individuals who want to join my team
          fetch(`https://api.teamify.pietrasik.top/team/${props.myTeamId}/requests`, {
              method: 'GET',
              credentials: 'include',
              })
              .then(res => res.json())
              .then(candidateData => {
                  // if no error code returned, then successfully got data
                  if (!candidateData.code) {
                      setCandidates(candidateData);
                  } else
                    console.log(candidateData.message);
              }).catch(console.error);
      }

        // get open Individuals
        fetch(`https://api.teamify.pietrasik.top/env/1/open`)
          .then(res => res.json())
          .then(listOpenIndividuals => {
              if (listOpenIndividuals)
                console.log(listOpenIndividuals);

              if(listOpenIndividuals.length > 0) { // if there are available people, set their data to be displayed
                  setOpenIndividuals( // get open users who haven't applied to current user's team
                      listOpenIndividuals.filter(openUser => // get open users who are not candidates
                          candidates.filter(candidate => // if candidate, will return array with candidate data
                             candidate.user.user_id === openUser.user_id
                         ).length === 0) // if not candidate, empty [] returned
                      );
              }
          }).catch(console.error);

    }, [props.myTeamId]);

    // update list after team leader accepted candidate
    function updateAfterAccepting(acceptedCandidate) {
        // remove from list of candidates
        setCandidates(candidates.filter(candidate => candidate !== acceptedCandidate));

        // add to list of team members on team card
        props.refreshTeamCard();
    }

    function updateAfterRejecting(rejectedCandidate) {
        // remove from list of candidates
        setCandidates(candidates.filter(candidate => candidate !== rejectedCandidate));

        // add to list of open individuals
        openIndividuals.push(rejectedCandidate.user);
    }

    // update list of people to be displayed after user invites an individual to user's team
    function updateAfterInviting(invitedPerson) {
        // remove an individual from list and update state
        const updatedAvailableIndividuals = openIndividuals.filter(otherUser => otherUser !== invitedPerson);
        setOpenIndividuals(updatedAvailableIndividuals);

        // add to list of invited people
        invited.push(invitedPerson);
    }

    return (
        <div>
            <h2>Find Team Members</h2>

            {candidates.length > 0 && myTeamId ?
              <>
                <h3>People Requesting to Join</h3>
                <div className="IndividualsList" >
                  {candidates.map((candidate) =>
                    <CandidateCard key={candidate} candidateData={candidate} myTeamId={myTeamId}
                        accept={updateAfterAccepting}
                        reject={updateAfterRejecting}
                        />)}
                  </div>
                </>
            : <></>}

            {invited.length > 0 && myTeamId ?
              <>
                <h3>People Invited </h3>
                <div className="IndividualsList" >
                  { /* list of people */
                    invited.map((individual) =>
                    <IndividualCard key={individual} individual={individual} type="invited"
                        />)}
                  </div>
                </>
            : <></>}

            {openIndividuals.length > 0 ?
                <>
                <h3>People Available</h3>
                <div className="IndividualsList" >
                  { /* list of people */
                    openIndividuals.map((individual) =>
                    <IndividualCard key={individual} individual={individual}
                        type={myTeamId ? "open" : ""} /* determines whether or not invite button shows */

                        /* Individuals List passes function to Individual Card child component
                            to let Card notify List when List needs to be updated */
                        updateList={updateAfterInviting}
                        />)}
                  </div>
                  </>
              : <></>}
          </div>
    );
}
