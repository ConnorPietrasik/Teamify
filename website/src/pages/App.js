import logo from '../logo.svg';
import '../css/App.css';
import Login from './Login';
import Home from './Home';
import Config from '../components/Config';
import React, { useState } from 'react';
import {Routes, Route} from "react-router-dom";

import Box from '@mui/material/Box';
import Toolbar from '@mui/material/Toolbar';
import Button from '@mui/material/Button';

import Drawer from '@mui/material/Drawer';
import IconButton from '@mui/material/IconButton';

import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemText from '@mui/material/ListItemText';
import RoomPreferencesRoundedIcon from '@mui/icons-material/RoomPreferencesRounded';

class App extends React.Component{
  constructor(props) {
    super(props);
    this.state = {
      // no user logged in yet
      userId: -1,
      user: {},
      envId: 1,
      drawerIsOpen: false,  // whether or not Sidebar is shown
    };
    this.logout = this.logout.bind(this);
    this.changeEnvironment = this.changeEnvironment.bind(this);
  }

  // updates user info and logged in status after user logs in
  updateUserInfo = (id) => {
      // get user data using userID
      fetch(Config.API + `/user/${id}`)
        .then(res => res.json())
        .then(userData => {
          if (userData.status === "error")
            console.log(userData);
          else
            // update state with user data to be displayed
            this.setState({
              userId: id,
              user: userData,
            });
         }).catch(console.error);
  };

  logout() {
    // log out from server
    fetch(Config.API + `/logout`, {
      method: 'POST',
      credentials: 'include',
        headers: {
            'Content-Type': 'application/json'
          },
    }).then(
        // change page display & clear data
        this.setState({
          userId: -1,
          user: {},
          envId: 1,
        }) // resetting user_id will make parent App render Login instead of Home
    ).catch(console.error);
  }

  componentDidMount() {
    // find user if already logged in, and gets user data if already logged in
    fetch(Config.API + `/checkauth`, {
     method: 'GET',
     credentials: 'include',
     headers: {
       'Content-Type': 'application/json',
     }, })
     .then(res => res.json())
     .then(data => {
       if (data)
         console.log(data);

       if (data.user_id)
         // get user data using userID
         fetch(Config.API + `/user/${data.user_id}`)
           .then(res => res.json())
           .then(userData => {
             if (userData.status === "error")
               console.log(userData);
             else
               // update state with user data to be displayed
               this.setState({
                 userId: data.user_id,
                 user: userData,
               });
            }).catch(console.error);
     }).catch(console.error);
  }

  // when user creates, joins, switches environment
  changeEnvironment(newEnvId) {
      if (this.state.envId !== newEnvId) {
          // render new env info
          this.setState({
              envId: newEnvId,
          });
      }
  }

  render() {
    return(
    <div className="App">
        { /*  redirects to either Home or Login page depending on whether user is signed in*/
          this.state.userId > -1 ?
          <Box sx={{ display: 'flex' }}>

            {/* Sidebar */}
            <Box  sx={{ flexGrow: 1 }}>
              <Drawer variant="permanent" open={this.state.drawerIsOpen} anchor="left"
              sx={{'& .MuiDrawer-paper': {
                    border: 'none',
                    boxShadow: '0 0 10px 3px rgba(0, 0, 0, .125)',

                    /* side bar slides open with animation */
                    width: `${this.state.drawerIsOpen ? '250px' : '0'}`,
                    transition: 'width .1s ease-in-out',
                  },
                }}>

                <Toolbar>
                  {/* button to close Sidebar*/}
                  <IconButton sx={{width: 50}} style={{color: 'grey'}}
                    onClick={() => this.setState({drawerIsOpen: false})}>
                    {'<'}</IconButton>
                  </Toolbar>

                {/* list of environments */}
                <List>
                  {this.state.user.environments.map((environmentObj) => (
                    <ListItem button key={environmentObj.env_id}
                        style={{color: `${this.state.envId === environmentObj.env_id ? '#2F4664' : 'grey'}` /* current env marked in diff color */ }}
                        onClick={() => this.changeEnvironment(environmentObj.env_id)}>
                      <ListItemText primary={environmentObj.env_id} /> </ListItem>))
                    }
                </List>

                </Drawer>
              </Box>

            {/* App bar, dashboard */}
            <Box sx={{
                /* app bar & dashboard width shrinks as sidebar slides open */
                width: `${this.state.drawerIsOpen ? `${100-((255/window.innerWidth)*100)}%` : '100%'}`,
                                                /* width % = % of viewport width - % of viewport width drawer takes up */
                transition: 'width .1s ease-in-out'}}>

                {/* App bar */}
                <Toolbar sx={{mt: '-40px', ml: '-40px', mr: '-40px', /* undo default margins on all sides except bottom margin */
                            boxShadow: '0 0 10px 3px rgba(0, 0, 0, .125)',}}>
                    {this.state.drawerIsOpen ? <></> :
                        /* button to open Sidebar */
                        <IconButton edge="start" sx={{width: 50}} style={{color: 'grey'}}
                            onClick={() => this.setState({drawerIsOpen: true})}>
                            <RoomPreferencesRoundedIcon fontSize="large"/></IconButton>}

                    <Box sx={{ flexGrow: 1 }}><h3 align="left">Teamify</h3></Box>
                    <Button variant="text" onClick={this.logout} style={{color: '#2F4664'}}>Logout</Button>
                    </Toolbar>

                {/* dashboard */}
                <Home updateUserLoginInfo={this.updateUserInfo} envId={this.state.envId}
                    userId={this.state.userId} user={this.state.user}/>
            </Box>
          </Box>
          : <Login updateUserLoginInfo={this.updateUserInfo}/>}
    </div>
    );
  }
}
export default App;
