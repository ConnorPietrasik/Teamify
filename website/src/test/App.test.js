import { render, screen } from '@testing-library/react';
import App from '../pages/App';
import Login from '../pages/Login';
import Home from '../pages/Home';
import ProfileSettings from '../components/ProfileSettings';

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
