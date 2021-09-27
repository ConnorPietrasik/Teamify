// user's dashboard
function Home(props) {

  return (
    <div>
      <h1>Hello {props.username}</h1>
    </div>
  );
}

function Profile(props) {
  return (
    <div>
      <h1>Name</h1>
    </div>
  );
}

export default Home;
