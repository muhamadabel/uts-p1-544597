<template>
  <div class="login-wrapper">
    <div class="login-card">
      <div class="brand-header">
        <div class="brand-badge">HONG FILMS</div>
        <h2>Production Gear Management</h2>
        <p class="subtitle">Sistem Inventaris Alat Produksi (UTS KEPL 544597)</p>
      </div>

      <form @submit.prevent="handleLogin" class="auth-form">
        <div v-if="errorMessage" class="alert alert-error">
          {{ errorMessage }}
        </div>

        <div class="form-group">
          <label for="email">Alamat Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="admin@hongfilms.com"
            required
            autocomplete="email"
          />
        </div>

        <div class="form-group">
          <label for="password">Kata Sandi</label>
          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="password123"
            required
            autocomplete="current-password"
          />
        </div>

        <button type="submit" class="btn btn-primary" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '../api/axios';

const router = useRouter();
const email = ref('admin@hongfilms.com');
const password = ref('password123');
const errorMessage = ref('');
const loading = ref(false);

const handleLogin = async () => {
  errorMessage.value = '';
  loading.value = true;
  try {
    const res = await apiClient.post('/login', {
      email: email.value,
      password: password.value,
    });

    if (res.data && res.data.access_token) {
      // Simpan token ke localStorage (Soal 2a)
      localStorage.setItem('auth_token', res.data.access_token);
      localStorage.setItem('user_name', res.data.user?.name || 'User');
      router.push('/');
    }
  } catch (err) {
    if (err.response && err.response.data && err.response.data.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Gagal terhubung ke server Laravel.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-wrapper {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
}

.login-card {
  width: 100%;
  max-width: 420px;
  background: #ffffff;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
  padding: 2rem;
}

.brand-header {
  text-align: center;
  margin-bottom: 1.8rem;
}

.brand-badge {
  display: inline-block;
  background: #0E0E12;
  color: #ffffff;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  padding: 0.25rem 0.6rem;
  border-radius: 4px;
  margin-bottom: 0.75rem;
}

.brand-header h2 {
  font-size: 1.3rem;
  color: #0E0E12;
  margin: 0 0 0.4rem 0;
}

.subtitle {
  font-size: 0.85rem;
  color: #64748b;
  margin: 0;
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  text-align: left;
}

.form-group label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
}

.form-group input {
  padding: 0.65rem 0.85rem;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.15s;
}

.form-group input:focus {
  border-color: #E53935;
}

.btn {
  padding: 0.75rem 1rem;
  border-radius: 6px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: background 0.15s;
}

.btn-primary {
  background: #E53935;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background: #c62828;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.alert-error {
  background: #fee2e2;
  border: 1px solid #fca5a5;
  color: #b91c1c;
  padding: 0.65rem 0.85rem;
  border-radius: 6px;
  font-size: 0.85rem;
}
</style>
