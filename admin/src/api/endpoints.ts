import api from './client'

// ── Auth ──────────────────────────────────────────
export const authApi = {
  login:  (email: string, password: string) =>
    api.post('/admin/login', { email, password }),
  logout: () => api.post('/admin/logout'),
  me:     () => api.get('/admin/me'),
}

// ── Dashboard ─────────────────────────────────────
export const dashboardApi = {
  stats: () => api.get('/admin/dashboard'),
}

// ── Products ──────────────────────────────────────
export const productsApi = {
  list:   (params?: Record<string, unknown>) => api.get('/admin/products', { params }),
  get:    (id: number) => api.get(`/admin/products/${id}`),
  // Ne PAS forcer Content-Type — Axios le génère automatiquement avec le boundary correct
  create: (data: FormData) => api.post('/admin/products', data),
  update: (id: number, data: FormData) => api.post(`/admin/products/${id}`, data),
  delete: (id: number) => api.delete(`/admin/products/${id}`),
  toggle: (id: number, is_published: boolean) =>
    api.patch(`/admin/products/${id}/toggle`, { is_published }),
  categories: (type?: string) => api.get('/admin/categories', { params: type ? { type } : {} }),
}

// ── Orders ────────────────────────────────────────
export const ordersApi = {
  list:         (params?: Record<string, unknown>) => api.get('/admin/orders', { params }),
  get:          (id: number) => api.get(`/admin/orders/${id}`),
  updateStatus: (id: number, status: string) =>
    api.patch(`/admin/orders/${id}/status`, { status }),
}

// ── Posts ─────────────────────────────────────────
export const postsApi = {
  list:   (params?: Record<string, unknown>) => api.get('/admin/posts', { params }),
  get:    (id: number) => api.get(`/admin/posts/${id}`),
  create: (data: FormData) => api.post('/admin/posts', data),
  update: (id: number, data: FormData) => api.post(`/admin/posts/${id}`, data),
  delete: (id: number) => api.delete(`/admin/posts/${id}`),
  categories: (type?: string) => api.get('/admin/categories', { params: type ? { type } : {} }),
}

// ── Quotes ────────────────────────────────────────
export const quotesApi = {
  list:         (params?: Record<string, unknown>) => api.get('/admin/quotes', { params }),
  get:          (id: number) => api.get(`/admin/quotes/${id}`),
  updateStatus: (id: number, status: string, notes?: string) =>
    api.patch(`/admin/quotes/${id}/status`, { status, admin_notes: notes }),
}

// ── Users ─────────────────────────────────────────
export const usersApi = {
  list:       (params?: Record<string, unknown>) => api.get('/admin/users', { params }),
  create:     (data: { first_name: string; last_name: string; email: string; password: string; role: string }) =>
    api.post('/admin/users', data),
  updateRole: (id: number, role: string) =>
    api.patch(`/admin/users/${id}/role`, { role }),
}

// ── Formations ────────────────────────────────────
export const formationsApi = {
  list:        (params?: Record<string, unknown>) => api.get('/admin/formations', { params }),
  get:         (id: number) => api.get(`/admin/formations/${id}`),
  create:      (data: FormData) => api.post('/admin/formations', data),
  update:      (id: number, data: FormData) => api.post(`/admin/formations/${id}`, data),
  delete:      (id: number) => api.delete(`/admin/formations/${id}`),
  toggle:      (id: number, is_published: boolean) => api.patch(`/admin/formations/${id}/toggle`, { is_published }),
  registrations:        (id: number, params?: Record<string, unknown>) => api.get(`/admin/formations/${id}/inscriptions`, { params }),
  updateRegistration:   (formationId: number, regId: number, status: string) =>
    api.patch(`/admin/formations/${formationId}/inscriptions/${regId}`, { status }),
}

// ── Profile ───────────────────────────────────────
export const profileApi = {
  changePassword: (data: {
    current_password: string
    password: string
    password_confirmation: string
  }) => api.patch('/admin/profile/password', data),
}
