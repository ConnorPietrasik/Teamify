import React, { useState, useEffect } from 'react';
import '../css/Home.css';
import '../css/Components.css';

// shows info for one team
export default function TeamCard(props) {
  return (
    <div className="IndividualCard">
        {props.team.name}
    </div>
  );
}
