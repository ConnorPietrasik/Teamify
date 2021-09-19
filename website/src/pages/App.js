import logo from '../logo.svg';
import '../css/App.css';
import Login from './Login';
import Home from './Home';

import {Routes, Route} from "react-router-dom";

function App() {
  return (
    <div className="App">
        <Routes>
        <Route path="/" element={<Login />} />
          <Route path="/home" element={<Home />} />
          <Route path="/login" element={<Login />} />
        </Routes>
    </div>
  );
}

export default App;
