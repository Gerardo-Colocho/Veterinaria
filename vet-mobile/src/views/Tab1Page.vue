<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar class="custom-toolbar">
        <ion-title class="fw-bold">🐾 Mis Mascotas</ion-title>
        <ion-buttons slot="end">
          <ion-button class="add-btn" @click="openModal">
            <ion-icon :icon="addOutline" slot="icon-only"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding main-bg">
      <!-- Lista de Mascotas Registradas -->
      <div v-if="mascotas.length > 0" class="pet-list-container">
        <ion-card v-for="mascota in mascotas" :key="mascota.id" class="pet-card clickable-card"
          @click="openHistorialModal(mascota)">
          <ion-card-content class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="pet-avatar">
                <span class="pet-icon">{{ getPetIcon(mascota.especie) }}</span>
              </div>
              <div class="pet-info ms-3">
                <h3 class="pet-name">{{ mascota.nombre }}</h3>
                <p class="pet-species">{{ mascota.especie }} • {{ mascota.raza || 'Sin raza' }}</p>
                <div class="pet-badges mt-2">
                  <span class="badge-chip" v-if="mascota.edad_meses || mascota.edad">⏳ {{ mascota.edad_meses ||
                    mascota.edad }} meses</span>
                  <span class="badge-chip" v-if="mascota.peso">⚖️ {{ mascota.peso }} kg</span>
                </div>
              </div>
            </div>
            <div class="text-end">
              <ion-icon :icon="medicalOutline" class="history-icon"></ion-icon>
              <small class="d-block text-teal font-weight-bold" style="font-size: 10px;">Ver Historial</small>
            </div>
          </ion-card-content>
        </ion-card>
      </div>

      <!-- Estado Vacío -->
      <div v-else class="empty-state">
        <div class="empty-icon">🐶</div>
        <h4>No tienes mascotas registradas</h4>
        <p>Presiona el botón <strong>+</strong> para agregar a tu compañero.</p>
      </div>

      <!-- Botón Flotante (FAB) -->
      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button class="custom-fab" @click="openModal">
          <ion-icon :icon="addOutline"></ion-icon>
        </ion-fab-button>
      </ion-fab>

      <!-- Modal de Registro de Mascota -->
      <ion-modal :is-open="isModalOpen" @didDismiss="closeModal" class="custom-modal">
        <ion-header class="ion-no-border">
          <ion-toolbar class="modal-toolbar">
            <ion-title>Registrar Mascota</ion-title>
            <ion-buttons slot="end">
              <ion-button class="close-btn" @click="closeModal">CANCELAR</ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>

        <ion-content class="ion-padding modal-body">
          <div class="form-header text-center mb-3">
            <h4 class="fw-bold text-dark m-0">Nueva Mascota</h4>
            <small class="text-muted">Ingresa la información requerida</small>
          </div>

          <form @submit.prevent="saveMascota" class="form-container">
            <div class="form-group mb-2">
              <label class="form-label"><ion-icon :icon="pawOutline" class="me-1"></ion-icon> Nombre</label>
              <ion-input v-model="form.nombre" placeholder="Ej. Copito" class="custom-input"></ion-input>
            </div>

            <div class="form-group mb-2">
              <label class="form-label"><ion-icon :icon="listOutline" class="me-1"></ion-icon> Especie</label>
              <ion-select v-model="form.especie" placeholder="Seleccionar Especie" class="custom-select"
                interface="popover">
                <ion-select-option value="Perro">🐶 Perro</ion-select-option>
                <ion-select-option value="Gato">🐱 Gato</ion-select-option>
                <ion-select-option value="Ave">🦜 Ave</ion-select-option>
                <ion-select-option value="Otro">✨ Otro</ion-select-option>
              </ion-select>
            </div>

            <div v-if="form.especie === 'Otro'" class="form-group mb-2 highlight-group">
              <label class="form-label text-teal fw-bold"><ion-icon :icon="createOutline" class="me-1"></ion-icon>
                Especifica la especie</label>
              <ion-input v-model="form.otraEspecie" placeholder="Ej. Conejo, Hurón..."
                class="custom-input highlight-input"></ion-input>
            </div>

            <div class="form-group mb-2">
              <label class="form-label"><ion-icon :icon="ribbonOutline" class="me-1"></ion-icon> Raza</label>
              <ion-input v-model="form.raza" placeholder="Ej. Mestizo, Golden..." class="custom-input"></ion-input>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <div class="form-group">
                  <label class="form-label"><ion-icon :icon="timeOutline" class="me-1"></ion-icon> Edad (meses)</label>
                  <ion-input type="number" v-model="form.edad" placeholder="18" class="custom-input"></ion-input>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label class="form-label"><ion-icon :icon="fitnessOutline" class="me-1"></ion-icon> Peso (kg)</label>
                  <ion-input type="number" step="0.1" v-model="form.peso" placeholder="10.0"
                    class="custom-input"></ion-input>
                </div>
              </div>
            </div>

            <ion-button expand="block" type="submit" :disabled="loading" class="save-btn my-2">
              {{ loading ? 'Guardando...' : 'GUARDAR MASCOTA' }}
            </ion-button>
          </form>
        </ion-content>
      </ion-modal>

      <!-- Modal de Historial Médico (Evaluación) -->
      <ion-modal :is-open="isHistorialModalOpen" @didDismiss="isHistorialModalOpen = false" class="custom-modal">
        <ion-header class="ion-no-border">
          <ion-toolbar class="modal-toolbar">
            <ion-title>📋 Historial Clínico</ion-title>
            <ion-buttons slot="end">
              <ion-button class="close-btn" @click="isHistorialModalOpen = false">CERRAR</ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>

        <ion-content class="ion-padding modal-body">
          <div v-if="selectedPet" class="text-center mb-3">
            <h3 class="fw-bold text-dark m-0">🐾 {{ selectedPet.nombre }}</h3>
            <p class="text-muted small m-0">{{ selectedPet.especie }} • {{ selectedPet.raza || 'Sin raza' }}</p>
          </div>

          <div v-if="loadingHistorial" class="text-center my-4">
            <p class="text-muted">Cargando evaluaciones médicas...</p>
          </div>

          <div v-else-if="historiales.length > 0">
            <div v-for="item in historiales" :key="item.id" class="historial-card p-3 mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-teal text-white">🩺 Evaluación Médica</span>
                <small class="text-muted">{{ formatDate(item.fecha_atencion || item.created_at) }}</small>
              </div>
              <p class="mb-1"><strong>Diagnóstico:</strong> {{ item.diagnostico }}</p>
              <p class="mb-0" v-if="item.tratamiento"><strong>Tratamiento:</strong> {{ item.tratamiento }}</p>
            </div>
          </div>

          <div v-else class="text-center my-4">
            <p class="text-muted">No hay evaluaciones registradas para esta mascota aún.</p>
          </div>
        </ion-content>
      </ion-modal>

      <!-- Toast de Notificaciones -->
      <ion-toast :is-open="showToast" :message="toastMessage" :duration="3000" :color="toastColor"
        @didDismiss="showToast = false"></ion-toast>
    </ion-content>
  </ion-page>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent, IonCard, IonCardContent,
  IonButtons, IonButton, IonIcon, IonFab, IonFabButton, IonModal,
  IonInput, IonSelect, IonSelectOption, IonToast
} from '@ionic/vue';
import {
  addOutline, pawOutline, listOutline, createOutline,
  ribbonOutline, timeOutline, fitnessOutline, medicalOutline
} from 'ionicons/icons';

const API_URL = 'http://localhost:8000/api';
const mascotas = ref([]);
const historiales = ref([]);
const selectedPet = ref(null);

const isModalOpen = ref(false);
const isHistorialModalOpen = ref(false);
const loading = ref(false);
const loadingHistorial = ref(false);

const showToast = ref(false);
const toastMessage = ref('');
const toastColor = ref('danger');

const form = ref({
  nombre: '',
  especie: 'Perro',
  otraEspecie: '',
  raza: '',
  edad: null,
  peso: null
});

onMounted(() => {
  fetchMascotas();
});

const getPetIcon = (especie) => {
  const esp = (especie || '').toLowerCase();
  if (esp.includes('perro')) return '🐶';
  if (esp.includes('gato')) return '🐱';
  if (esp.includes('ave') || esp.includes('pájaro')) return '🦜';
  return '🐾';
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'short', day: 'numeric' });
};

const openModal = () => {
  form.value = { nombre: '', especie: 'Perro', otraEspecie: '', raza: '', edad: null, peso: null };
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

const openHistorialModal = async (mascota) => {
  selectedPet.value = mascota;
  isHistorialModalOpen.value = true;
  loadingHistorial.value = true;
  historiales.value = [];

  const token = localStorage.getItem('token') || localStorage.getItem('vet_token');
  try {
    const res = await fetch(`${API_URL}/mascotas/${mascota.id}/historial`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    if (res.ok) {
      historiales.value = await res.json();
    }
  } catch (err) {
    console.error('Error al cargar historial:', err);
  } finally {
    loadingHistorial.value = false;
  }
};

const triggerToast = (msg, color = 'danger') => {
  toastMessage.value = msg;
  toastColor.value = color;
  showToast.value = true;
};

const fetchMascotas = async () => {
  const token = localStorage.getItem('token') || localStorage.getItem('vet_token');
  if (!token) return;

  try {
    const res = await fetch(`${API_URL}/mascotas`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    });
    if (res.ok) {
      mascotas.value = await res.json();
    }
  } catch (err) {
    console.error('Error al cargar mascotas:', err);
  }
};

const saveMascota = async () => {
  const especieFinal = form.value.especie === 'Otro' ? form.value.otraEspecie : form.value.especie;

  if (!form.value.nombre || !especieFinal) {
    triggerToast('El nombre y la especie son obligatorios');
    return;
  }

  const token = localStorage.getItem('token') || localStorage.getItem('vet_token');
  if (!token) {
    triggerToast('Sesión no válida. Por favor inicia sesión de nuevo.');
    return;
  }

  loading.value = true;

  try {
    const res = await fetch(`${API_URL}/mascotas`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        nombre: form.value.nombre,
        especie: especieFinal,
        raza: form.value.raza || null,
        edad: form.value.edad ? parseInt(form.value.edad) : null,
        peso: form.value.peso ? parseFloat(form.value.peso) : null
      })
    });

    if (res.ok) {
      triggerToast('Mascota registrada con éxito', 'success');
      closeModal();
      fetchMascotas();
    } else {
      const errData = await res.json();
      triggerToast(errData.message || 'Error al registrar la mascota');
    }
  } catch (err) {
    triggerToast('Error de conexión con el servidor');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.custom-toolbar {
  --background: #0D9488;
  --color: #ffffff;
}

.main-bg {
  --background: #F8FAFC;
}

.add-btn {
  --color: #ffffff;
}

.clickable-card {
  cursor: pointer;
  transition: transform 0.2s ease;
}

.clickable-card:active {
  transform: scale(0.98);
}

.pet-card {
  border-radius: 14px;
  background: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
  margin-bottom: 10px;
  border: 1px solid #E2E8F0;
}

.pet-avatar {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #CCFBF1;
  display: flex;
  align-items: center;
  justify-content: center;
}

.pet-icon {
  font-size: 24px;
}

.history-icon {
  font-size: 24px;
  color: #0D9488;
}

.pet-name {
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
  margin: 0;
}

.pet-species {
  font-size: 13px;
  color: #64748B;
  margin: 2px 0 0 0;
}

.badge-chip {
  background: #F1F5F9;
  color: #334155;
  font-size: 11px;
  font-weight: 600;
  padding: 3px 6px;
  border-radius: 6px;
  margin-right: 4px;
}

.bg-teal {
  background-color: #0D9488 !important;
}

.text-teal {
  color: #0D9488 !important;
}

.historial-card {
  background: #F8FAFC;
  border-left: 4px solid #0D9488;
  border-radius: 8px;
}

.empty-state {
  text-align: center;
  margin-top: 50px;
  color: #64748B;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 8px;
}

.custom-fab {
  --background: #0D9488;
  --color: #ffffff;
}

.modal-toolbar {
  --background: #0D9488;
  --color: #ffffff;
}

.close-btn {
  --color: #ffffff;
  font-weight: 700;
}

.modal-body {
  --background: #ffffff;
}

.form-label {
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  margin-bottom: 4px;
  display: block;
}

.custom-input,
.custom-select {
  --background: #F8FAFC;
  --color: #0F172A !important;
  color: #0F172A !important;
  --padding-start: 10px;
  --padding-end: 10px;
  border: 1px solid #CBD5E1;
  border-radius: 8px;
  font-weight: 600;
  min-height: 42px;
}

.highlight-group {
  background: #F0FDFA;
  padding: 8px;
  border-radius: 10px;
  border: 1px solid #99F6E4;
}

.highlight-input {
  --background: #ffffff;
  border-color: #0D9488;
}

.save-btn {
  --background: #0D9488;
  --color: #ffffff;
  --border-radius: 10px;
  font-weight: 700;
  height: 44px;
  box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
}
</style>