import { useState } from 'react'

function App() {
  const [password, setPassword] = useState('')
  const [result, setResult] = useState(null)
  const [error, setError] = useState(null)
  const [loading, setLoading] = useState(false)

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!password) return

    setLoading(true)
    setError(null)
    setResult(null)

    try {
      const API_BASE_URL = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000';
      
      const response = await fetch(`${API_BASE_URL}/api/hash`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ password }),
      })

      const responseText = await response.text()
      let data = {}
      try {
        data = responseText ? JSON.parse(responseText) : {}
      } catch (e) {
        console.error("Non-JSON response from server:", responseText)
        throw new Error(`Error del servidor (no JSON): ${response.status} ${response.statusText}. Respuesta: ${responseText.substring(0, 100)}`)
      }

      if (!response.ok) {
        throw new Error(data.error || `Error HTTP: ${response.status} ${response.statusText}`)
      }

      setResult(data)
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="container">
      <h1>Secure Hash</h1>
      <p className="subtitle">Genera un hash seguro usando Argon2id</p>

      <form onSubmit={handleSubmit}>
        <div className="input-group">
          <label htmlFor="password">Contraseña a calcular</label>
          <input
            id="password"
            type="text"
            placeholder="Escribe tu contraseña..."
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            disabled={loading}
            autoComplete="off"
          />
        </div>
        
        <button type="submit" disabled={!password || loading}>
          {loading ? 'Generando...' : 'Generar Hash Seguro'}
        </button>
      </form>

      {error && (
        <div className="error-message">
          {error}
        </div>
      )}

      {result && (
        <div className="result-card">
          <h3>Hash Generado ({result.algorithm})</h3>
          <div className="hash-output">{result.hash}</div>
        </div>
      )}
    </div>
  )
}

export default App
