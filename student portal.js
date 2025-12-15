import React, { useState } from "react";

// Fake user DB for demo
const fakeUsers = [
  { username: "student1", password: "pass1", id: 1, name: "Alice" },
  { username: "student2", password: "pass2", id: 2, name: "Bob" }
];

function Login({ onLogin }) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  function handleSubmit(e) {
    e.preventDefault();
    const user = fakeUsers.find(
      (u) => u.username === username && u.password === password
    );
    if (user) {
      onLogin(user);
    } else {
      setError("Invalid username or password.");
    }
  }

  return (
    <form onSubmit={handleSubmit} style={{ maxWidth: 300, margin: "100px auto" }}>
      <h2>Student Login</h2>
      <input
        value={username}
        onChange={e => setUsername(e.target.value)}
        placeholder="Username"
        required
        style={{ display: "block", marginBottom: 10, width: "100%" }}
      />
      <input
        value={password}
        onChange={e => setPassword(e.target.value)}
        type="password"
        placeholder="Password"
        required
        style={{ display: "block", marginBottom: 10, width: "100%" }}
      />
      <button type="submit" style={{ width: "100%", padding: 8 }}>Login</button>
      {error && <div style={{ color: "red", marginTop: 10 }}>{error}</div>}
    </form>
  );
}

export default Login;

