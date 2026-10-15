<template>
  <div class="dashboard-wrapper">
    <!-- Navbar -->
    <nav class="top-nav">
      <div class="nav-brand">
        <span class="brand-tag">GEAR INVENTORY</span>
        <span class="nav-title">Equipment Management System</span>
      </div>
      <div class="nav-user">
        <span class="user-greeting">Masuk sebagai: <strong>{{ userName }}</strong></span>
        <button @click="handleLogout" class="btn btn-sm btn-outline">Keluar</button>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="dashboard-content">
      <!-- Section Form Tambah / Edit -->
      <section class="card form-section">
        <div class="section-header">
          <h3>{{ isEditing ? 'Edit Data Alat' : 'Tambah Alat Produksi Baru' }}</h3>
          <span v-if="isEditing" class="editing-indicator">Mode Edit: ID #{{ editingId }}</span>
        </div>

        <div v-if="formMessage" :class="['alert', formMessage.type === 'error' ? 'alert-error' : 'alert-success']">
          {{ formMessage.text }}
        </div>

        <form @submit.prevent="handleSubmit" class="gear-form">
          <div class="form-grid">
            <div class="form-group">
              <label>Nama Alat</label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Contoh: Sony FX3 Cinema Line"
                required
              />
            </div>

            <div class="form-group">
              <label>Kategori</label>
              <select v-model="form.category" required>
                <option value="" disabled>Pilih Kategori</option>
                <option value="Camera">Camera</option>
                <option value="Lens">Lens</option>
                <option value="Lighting">Lighting</option>
                <option value="Audio">Audio</option>
                <option value="Stabilizer">Stabilizer</option>
                <option value="Drone">Drone</option>
              </select>
            </div>

            <div class="form-group">
              <label>Nomor Seri</label>
              <input
                v-model="form.serial_number"
                type="text"
                placeholder="Contoh: CAM-FX3-100"
                required
              />
            </div>

            <div class="form-group">
              <label>Tarif Harian (Rp)</label>
              <input
                v-model.number="form.daily_rate"
                type="number"
                min="0"
                step="50000"
                placeholder="850000"
                required
              />
            </div>

            <div class="form-group">
              <label>Status Ketersediaan</label>
              <select v-model="form.status" required>
                <option value="available">Available (Tersedia)</option>
                <option value="in_use">In Use (Sedang Dipakai)</option>
                <option value="maintenance">Maintenance (Perawatan)</option>
              </select>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary" :disabled="submitting">
              {{ submitting ? 'Menyimpan...' : (isEditing ? 'Simpan Perubahan' : 'Tambah Gear') }}
            </button>
            <button v-if="isEditing" type="button" @click="cancelEdit" class="btn btn-secondary">
              Batal
            </button>
          </div>
        </form>
      </section>

      <!-- Section Tabel Data -->
      <section class="card table-section">
        <div class="section-header">
          <h3>Daftar Inventaris Gear ({{ gears.length }} Unit)</h3>
          <button @click="loadGears" class="btn btn-sm btn-outline" :disabled="loading">
            {{ loading ? 'Memuat...' : 'Refresh Data' }}
          </button>
        </div>

        <div v-if="loading" class="empty-state">
          Mengambil data dari server backend...
        </div>

        <div v-else-if="gears.length === 0" class="empty-state">
          Belum ada data gear. Silakan tambah data melalui form di atas.
        </div>

        <div v-else class="table-responsive">
          <table class="gear-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama Alat</th>
                <th>Kategori</th>
                <th>No. Seri</th>
                <th>Tarif / Hari</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="gear in gears" :key="gear.id">
                <td>#{{ gear.id }}</td>
                <td class="gear-name">{{ gear.name }}</td>
                <td><span class="cat-pill">{{ gear.category }}</span></td>
                <td class="serial-code"><code>{{ gear.serial_number }}</code></td>
                <td>Rp {{ Number(gear.daily_rate).toLocaleString('id-ID') }}</td>
                <td>
                  <span :class="['status-badge', 'status-' + gear.status]">
                    {{ formatStatus(gear.status) }}
                  </span>
                </td>
                <td class="action-buttons">
                  <button @click="startEdit(gear)" class="btn-action btn-edit">Edit</button>
                  <button @click="handleDelete(gear.id, gear.name)" class="btn-action btn-delete">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import apiClient from '../api/axios';

const router = useRouter();
const userName = ref(localStorage.getItem('user_name') || 'Abel Producer');
const gears = ref([]);
const loading = ref(false);
const submitting = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const formMessage = ref(null);

const form = ref({
  name: '',
  category: '',
  serial_number: '',
  daily_rate: 500000,
  status: 'available',
});

const formatStatus = (st) => {
  if (st === 'available') return 'AVAILABLE';
  if (st === 'in_use') return 'IN USE';
  if (st === 'maintenance') return 'MAINTENANCE';
  return st;
};

const loadGears = async () => {
  loading.value = true;
  try {
    const res = await apiClient.get('/gears');
    gears.value = res.data.data || [];
  } catch (err) {
    formMessage.value = {
      type: 'error',
      text: err.response?.data?.message || 'Gagal memuat daftar gear dari backend.',
    };
  } finally {
    loading.value = false;
  }
};

const handleSubmit = async () => {
  formMessage.value = null;
  submitting.value = true;
  try {
    if (isEditing.value) {
      // Update data via PUT (Soal 2b)
      await apiClient.put(`/gears/${editingId.value}`, form.value);
      formMessage.value = { type: 'success', text: 'Data gear berhasil diperbarui.' };
    } else {
      // Tambah data via POST (Soal 2b)
      await apiClient.post('/gears', form.value);
      formMessage.value = { type: 'success', text: 'Data gear baru berhasil ditambahkan.' };
    }
    cancelEdit();
    await loadGears();
  } catch (err) {
    const msg = err.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.';
    formMessage.value = { type: 'error', text: msg };
  } finally {
    submitting.value = false;
  }
};

const startEdit = (gear) => {
  isEditing.value = true;
  editingId.value = gear.id;
  form.value = {
    name: gear.name,
    category: gear.category,
    serial_number: gear.serial_number,
    daily_rate: Number(gear.daily_rate),
    status: gear.status,
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEdit = () => {
  isEditing.value = false;
  editingId.value = null;
  form.value = {
    name: '',
    category: '',
    serial_number: '',
    daily_rate: 500000,
    status: 'available',
  };
};

const handleDelete = async (id, name) => {
  if (!confirm(`Hapus alat "${name}"?`)) return;

  try {
    // Hapus data via DELETE (Soal 2b)
    await apiClient.delete(`/gears/${id}`);
    formMessage.value = { type: 'success', text: `Gear "${name}" berhasil dihapus.` };
    await loadGears();
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal menghapus data.');
  }
};

const handleLogout = async () => {
  try {
    await apiClient.post('/logout');
  } catch (err) {
    // Abaikan jika token sudah hangus
  } finally {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_name');
    router.push('/login');
  }
};

onMounted(() => {
  loadGears();
});
</script>

<style scoped>
.dashboard-wrapper {
  min-height: 100vh;
  background-color: #f8fafc;
  color: #1e293b;
}

.top-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 2rem;
  background: #ffffff;
  border-bottom: 1px solid #e2e8f0;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-tag {
  background: #0E0E12;
  color: #ffffff;
  font-weight: 700;
  font-size: 0.75rem;
  letter-spacing: 0.1em;
  padding: 0.25rem 0.6rem;
  border-radius: 4px;
}

.nav-title {
  font-weight: 700;
  font-size: 1.1rem;
  color: #0E0E12;
}

.nav-user {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-greeting {
  font-size: 0.9rem;
  color: #475569;
}

.dashboard-content {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 0.75rem;
}

.section-header h3 {
  margin: 0;
  font-size: 1.15rem;
  color: #0E0E12;
}

.editing-indicator {
  background: #fef3c7;
  color: #92400e;
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.2rem 0.6rem;
  border-radius: 4px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.form-group label {
  font-size: 0.82rem;
  font-weight: 600;
  color: #475569;
}

.form-group input,
.form-group select {
  padding: 0.6rem 0.8rem;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 0.9rem;
  outline: none;
  background: #ffffff;
}

.form-group input:focus,
.form-group select:focus {
  border-color: #E53935;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  margin-top: 1.25rem;
}

.btn {
  padding: 0.6rem 1.1rem;
  font-size: 0.9rem;
  font-weight: 600;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: all 0.15s;
}

.btn-sm {
  padding: 0.35rem 0.75rem;
  font-size: 0.8rem;
}

.btn-primary {
  background: #E53935;
  color: #ffffff;
}

.btn-primary:hover:not(:disabled) {
  background: #c62828;
}

.btn-secondary {
  background: #e2e8f0;
  color: #334155;
}

.btn-secondary:hover {
  background: #cbd5e1;
}

.btn-outline {
  background: transparent;
  border: 1px solid #cbd5e1;
  color: #475569;
}

.btn-outline:hover {
  border-color: #94a3b8;
  background: #f8fafc;
}

.alert {
  padding: 0.75rem 1rem;
  border-radius: 6px;
  font-size: 0.88rem;
  margin-bottom: 1rem;
}

.alert-success {
  background: #dcfce7;
  border: 1px solid #86efac;
  color: #166534;
}

.alert-error {
  background: #fee2e2;
  border: 1px solid #fca5a5;
  color: #b91c1c;
}

.table-responsive {
  overflow-x: auto;
}

.gear-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.gear-table th {
  background: #f8fafc;
  color: #64748b;
  font-weight: 600;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e2e8f0;
}

.gear-table td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid #f1f5f9;
  font-size: 0.9rem;
}

.gear-name {
  font-weight: 600;
  color: #0E0E12;
}

.cat-pill {
  background: #f1f5f9;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  color: #475569;
}

.serial-code code {
  background: #f8fafc;
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  border: 1px solid #e2e8f0;
  font-size: 0.82rem;
  color: #0f172a;
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.6rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.05em;
}

.status-available {
  background: #dcfce7;
  color: #15803d;
}

.status-in_use {
  background: #dbeafe;
  color: #1d4ed8;
}

.status-maintenance {
  background: #fef3c7;
  color: #b45309;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.btn-action {
  padding: 0.3rem 0.6rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 600;
  border: 1px solid transparent;
  cursor: pointer;
}

.btn-edit {
  background: #eff6ff;
  color: #2563eb;
  border-color: #bfdbfe;
}

.btn-edit:hover {
  background: #dbeafe;
}

.btn-delete {
  background: #fef2f2;
  color: #dc2626;
  border-color: #fecaca;
}

.btn-delete:hover {
  background: #fee2e2;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
  color: #94a3b8;
  font-size: 0.95rem;
}
</style>
