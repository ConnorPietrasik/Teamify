import { render, screen } from '@testing-library/react';
import App from '../pages/App';
import Login from '../pages/Login';
import Home from '../pages/Home';
import ProfileSettings from '../components/ProfileSettings';
import IndividualsList from '../components/IndividualsList';
import IndividualCard from '../components/IndividualCard';
import TeamCard from '../components/TeamCard';
import ProfileSkillsSelect from '../components/inputs/ProfileSkillsSelect.js';

test('renders login page', () => {
  render(<Login />);
  const linkElement = screen.getByText(/Welcome/i);
  expect(linkElement).toBeInTheDocument();
});

test('renders home page', () => {
  render(<Home />);
  const linkElement = screen.getByText(/Hello/i);
  expect(linkElement).toBeInTheDocument();
});

// checks if list is being rendered
test('renders list of profile cards', () => {
  const result = render(<Home />);
  const renderedList = result.container.querySelector('.IndividualsList');
  expect(renderedList).toBeInTheDocument();
});

// checks if inputs fields have correct values
test('test a typical set of login inputs', () => {
  const loginInfo = { username: 'username_test', password: 'pass_test' }; // test inputs

  const result = render(<Login />);
  const usernameInput = result.container.querySelector('#usernameInputField');
  usernameInput.value = loginInfo.username;
  expect(usernameInput.value).toBe('username_test');  // check username

  const passwordInput = result.container.querySelector('#passwordInputField');
  passwordInput.value = loginInfo.password;
  expect(passwordInput.value).toBe('pass_test'); // check password
});

test('renders profile settings with correct user username and bio', () => {
  const testUser = { username: 'username_test', bio: 'test bio' }; // test inputs
  render(<ProfileSettings user={testUser}/>);

  const elementWithUsername = screen.getByText(testUser.username);
  expect(elementWithUsername).toBeInTheDocument();

  const elementWithBio = screen.getByText(testUser.bio, {exact: false}); // any element containing test bio string
  expect(elementWithBio).toBeInTheDocument();
});

test('renders individual list component', () => {
  const testArrayIndividuals = ['sally', 'joe'];
  const result = render(<IndividualsList />);
});

test('renders individual card component with open individual', () => {
  const testUser = {username: 'lucie'}
  const result = render(<IndividualCard individual={testUser} type='open'/>);
  const renderedCard = result.container.querySelector('.IndividualCard');
  expect(renderedCard).toBeInTheDocument();

  // makes sure username is displayed
  const elementWithUsername = screen.getByText(testUser.username);
  expect(elementWithUsername).toBeInTheDocument();

  // makes sure invite button is displayed
  const inviteButton = result.container.querySelector('.inviteBtn');
  expect(inviteButton).toBeInTheDocument();
});

test('renders team card component for user\'s team', () => {
  const testCreatedTeam = {name: "Test Created Team",
    members: [{user: {username: 'penelope'}}, {user: {username: 'anna'}}, {user: {username: 'stephen'}}]}
  const result = render(<TeamCard team={testCreatedTeam}/>);

  const renderedCard = result.container.querySelector('.IndividualCard');
  expect(renderedCard).toBeInTheDocument();

  // makes sure all team member's names are displayed
  for (var memberIndex = 0; memberIndex < testCreatedTeam.members.length; memberIndex++) {
      var memberName = testCreatedTeam.members[memberIndex].user.username;

      const elementWithMemberName = screen.getByText(memberName);
      expect(elementWithMemberName).toBeInTheDocument();
  }

  // makes sure team name is displayed
  const elementWithTeamName = screen.getByText(testCreatedTeam.name);
  expect(elementWithTeamName).toBeInTheDocument();
});

test('renders team card component for team that invited user', () => {
  const testInterestedTeam = {name: "Interested Team"}
  const result = render(<TeamCard team={testInterestedTeam} status='invited'/>);

  const renderedCard = result.container.querySelector('.IndividualCard');
  expect(renderedCard).toBeInTheDocument();

  // makes sure team name is displayed
  const elementWithTeamName = screen.getByText(testInterestedTeam.name);
  expect(elementWithTeamName).toBeInTheDocument();

  // makes sure "Accept" button is displayed
  const acceptBtn = result.container.querySelector('.acceptBtn');
  expect(acceptBtn).toBeInTheDocument();

  // makes sure "Deny" button is displayed
  const denyBtn = result.container.querySelector('.denyBtn');
  expect(denyBtn).toBeInTheDocument();
});

test('renders team card component for open team to join', () => {
  const testAvailableTeam = {name: "Test Open Team"}
  const result = render(<TeamCard team={testAvailableTeam} status='open'/>);

  const renderedCard = result.container.querySelector('.IndividualCard');
  expect(renderedCard).toBeInTheDocument();

  // makes sure team name is displayed
  const elementWithTeamName = screen.getByText(testAvailableTeam.name);
  expect(elementWithTeamName).toBeInTheDocument();

  // makes sure "Request to Join" button is displayed
  const requestButton = result.container.querySelector('.inviteBtn');
  expect(requestButton).toBeInTheDocument();
});

test('Profile Skills Select rendering', () => {
  const result = render(
      <ProfileSkillsSelect
          placeholder={'Test Placeholder'}
          stateValue={[{value: 1, label: 'string being displayed'}]}
          stateSetter={null}
          />);

  const skillsSelect = result.container.querySelector('.profileSkillsSelect');
  expect(skillsSelect).toBeInTheDocument();
});
