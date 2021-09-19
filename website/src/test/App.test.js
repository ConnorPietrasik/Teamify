import { render, screen } from '@testing-library/react';
import App from '../pages/App';
import Login from '../pages/Login';
import Home from '../pages/Home';

test('renders login page', () => {
  render(<Login />);
  const linkElement = screen.getByText(/Welcome/i);
  expect(linkElement).toBeInTheDocument();
});

test('renders home page', () => {
  render(<Home />);
  const linkElement = screen.getByText(/Home/i);
  expect(linkElement).toBeInTheDocument();
});
