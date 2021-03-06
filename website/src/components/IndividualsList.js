import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import IndividualCard from './IndividualCard.js';
import CandidateCard from './CandidateCard.js';

import AvailableList from './AvailableList.js';
import Config from './Config';

export default function IndividualsList(props) {
    // if user wants to invite more team members on team's behalf, we need to know which team user is on
    const [myTeamId, setMyTeamId] = useState(-1);

    // list of people that will be displayed
    const [candidates, setCandidates] = useState([]); // people who requested to join my team
    const [invited, setInvited] = useState([]); // people my team invited
    const [openIndividuals, setOpenIndividuals] = useState([]);

    useEffect(() => { // initialize based on parameter values
        console.log("props", props)
      if (props.myTeamId > -1) {
          console.log("fetch data for myTeamId", props.myTeamId)
          setMyTeamId(props.myTeamId);

          // get all requests from individuals who want to join my team
          fetch(Config.API + `/team/${props.myTeamId}/requests`, {
              method: 'GET',
              credentials: 'include',
              })
              .then(res => res.json())
              .then(candidateData => {
                  // if no error code returned, then successfully got data
                  if (!candidateData.code) {
                      setCandidates(candidateData.filter(candidate => candidate.user.team === -1));
                  } else
                    console.log(candidateData.message);
              }).catch(console.error);

          // get people my team has invited
          fetch(Config.API + `/team/${props.myTeamId}/invites`, {
              method: 'GET',
              credentials: 'include',
              headers: {'Content-Type': 'application/json'},
              }).then(res => res.json())
              .then(inviteData => {
                  if (!inviteData.code) {
                      setInvited(inviteData.filter(invited => invited.user.team === -1));
                  } else
                    console.log(inviteData);
              }).catch(console.error);
      } else {
          setCandidates([]);
          setInvited([]);
          setMyTeamId(-1);
      }
      }, [props.myTeamId, props.envId]);

      useEffect(() => {
        getOpenIndividuals();
    }, [candidates, invited]);

    const getOpenIndividuals = function() {
      const idsOfInvited = invited.map((inviteData) => inviteData.user.user_id);

      // get open Individuals
      fetch(Config.API + `/env/${props.envId}/open`)
        .then(res => res.json())
        .then(listOpenIndividuals => {
            setOpenIndividuals( // get open users who haven't applied to current user's team
                listOpenIndividuals.filter(openUser => // get open users who are not candidates
                    candidates.filter(candidate => // if candidate, will return array with candidate data
                       candidate.user.user_id === openUser.user_id
                       ).length === 0 // if not candidate, empty [] returned
                    && !idsOfInvited.includes(openUser.user_id) /* don't include invited users in list */ 
                    && openUser.user_id !== props.user.user_id )
                );
        }).catch(console.error);
    }

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
        var newInvitedList = [...invited] // copy
        newInvitedList.push({user: invitedPerson});
        setInvited(newInvitedList);
    }

    return (
        <div>
            <h2>Find Team Members</h2>

            {candidates && myTeamId > -1?
              <>
                <h3>People Requesting to Join</h3>
                <div className="IndividualsList" >
                  {candidates.map((candidate) =>
                    <CandidateCard key={candidate} candidateData={candidate} myTeamId={myTeamId}
                        teamMemberRole={props.teamMemberRole}
                        accept={updateAfterAccepting}
                        reject={updateAfterRejecting}
                        />)}
                  </div>
                </>
            : <></>}

            {invited && myTeamId > -1 ?
              <>
                <h3>People Invited </h3>
                <div className="IndividualsList" >
                  { /* list of people */
                    invited.map((inviteData) =>
                    <IndividualCard key={inviteData.user} individual={inviteData.user} type="invited"
                        />)}
                  </div>
                </>
            : <></>}

            <AvailableList getOpenIndividuals={getOpenIndividuals} openIndividuals={openIndividuals} updateList={updateAfterInviting} myTeamId={myTeamId}
                envId={props.envId}/>

          </div>
    );
}
