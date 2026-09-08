import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: { 'Accept': 'application/json' },
  withCredentials: false,
})

// Injecter le token + Content-Type correct à chaque requête
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('admin_token')
  if (token) config.headers.Authorization = `Bearer ${token}`

  // Ne PAS forcer application/json si c'est un FormData — Axios gère le boundary
  if (!(config.data instanceof FormData)) {
    config.headers['Content-Type'] = 'application/json'
  }
  // Si FormData, on supprime Content-Type pour laisser Axios le générer avec le bon boundary
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type']
  }

  return config
})

// Redirection auto sur 401
api.interceptors.response.use(
  (res) => res,
  (err) => {
    if (err.response?.status === 401) {
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')
      window.location.href = '/admin/login'
    }
    return Promise.reject(err)
  },
)

export default api
