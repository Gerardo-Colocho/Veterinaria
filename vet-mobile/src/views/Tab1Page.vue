<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar style="--background: #0D9488; --color: #FFFFFF;">
        <ion-title>Mis Mascotas</ion-title>
        <ion-buttons slot="end">
          <ion-button @click="isAddModalOpen = true">
            <ion-icon slot="icon-only" :icon="addOutline"></ion-icon>
          </ion-button>
        </ion-buttons>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding" style="--background: #F8FAFC;">
      <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <div v-if="loading" class="ion-text-center ion-padding">
        <ion-spinner color="teal"></ion-spinner>
      </div>

      <div v-else-if="mascotas.length === 0" class="empty-state">
        <ion-icon :icon="pawOutline" class="empty-icon"></ion-icon>
        <h3>No tienes mascotas registradas</h3>
        <p>Toca el botón (+) o el botón flotante para agregar tu primera mascota.</p>
      </div>

      <!-- Tarjetas de Mascotas -->
      <div v-else>
        <ion-card v-for="mascota in mascotas" :key="mascota.id" class="pet-card" @click="openModal(mascota)">
          <ion-card-header>
            <div class="card-header-flex">
              <div>
                <ion-card-title class="pet-name">{{ mascota.nombre }}</ion-card-title>
                <ion-card-subtitle>{{ mascota.especie }} • {{ mascota.raza || 'Sin raza' }}</ion-card-subtitle>
              </div>
              <ion-badge color="success" class="badge-custom">Saludable</ion-badge>
            </div>
          </ion-card-header>

          <ion-card-content>
            <div class="pet-details">
              <p><strong>Edad:</strong> {{ mascota.edad_meses ? `${mascota.edad_meses} meses` : 'N/D' }}</p>
              <p><strong>Peso:</strong> {{ mascota.peso ? `${mascota.peso} kg` : 'N/D' }}</p>
            </div>
            <p class="click-hint">👉 Toca para ver Historial Médico</p>
          </ion-card-content>
        </ion-card>
      </div>

      <!-- Botón Flotante para Agregar Mascota -->
      <ion-fab vertical="bottom" horizontal="end" slot="fixed">
        <ion-fab-button style="--background: #0D9488;" @click="isAddModalOpen = true">
          <ion-icon :icon="addOutline"></ion-icon>
        </ion-fab-button>
      </ion-fab>

      <!-- Modal 1: Historial Médico -->
      <ion-modal :is-open="isModalOpen" @didDismiss="isModalOpen = false">
        <ion-header class="ion-no-border">
          <ion-toolbar style="--background: #0D9488; --color: #FFFFFF;">
            <ion-title>Historial de {{ selectedMascota?.nombre }}</ion-title>
            <ion-buttons slot="end">
              <ion-button @click="isModalOpen = false">Cerrar</ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>

        <ion-content class="ion-padding" style="--background: #F8FAFC;">
          <div v-if="loadingHistorial" class="ion-text-center ion-padding">
            <ion-spinner color="teal"></ion-spinner>
          </div>

          <div v-else-if="historiales.length === 0" class="empty-state">
            <ion-icon :icon="medkitOutline" class="empty-icon"></ion-icon>
            <h3>Sin historiales registrados</h3>
            <p>Aún no hay consultas o diagnósticos para esta mascota.</p>
          </div>

          <div v-else>
            <ion-card v-for="h in historiales" :key="h.id" class="historial-card">
              <ion-card-header>
                <ion-card-title class="historial-fecha">📅 {{ h.created_at ? h.created_at.substring(0, 10) : 'Fecha N/D'
                }}</ion-card-title>
              </ion-card-header>
              <ion-card-content>
                <p class="field-title">🔍 Diagnóstico:</p>
                <p class="field-desc">{{ h.diagnostico }}</p>

                <p class="field-title ion-margin-top">💊 Tratamiento:</p>
                <p class="field-desc">{{ h.tratamiento || 'Ninguno prescrito' }}</p>
              </ion-card-content>
            </ion-card>
          </div>
        </ion-content>
      </ion-modal>

      <!-- Modal 2: Formulario para Registrar Nueva Mascota -->
      <ion-modal :is-open="isAddModalOpen" @didDismiss="isAddModalOpen = false">
        <ion-header class="ion-no-border">
          <ion-toolbar style="--background: #0D9488; --color: #FFFFFF;">
            <ion-title>Registrar Mascota</ion-title>
            <ion-buttons slot="end">
              <ion-button @click="isAddModalOpen = false">Cancelar</ion-button>
            </ion-buttons>
          </ion-toolbar>
        </ion-header>

        <ion-content class="ion-padding" style="--background: #F8FAFC;">
          <div class="form-card">
            <h3 class="form-title">Nueva Mascota</h3>

            <ion-item lines="full" class="custom-input">
              <ion-label position="stacked">Nombre</ion-label>
              <ion-input v-model="nuevoNombre" placeholder="Ej. Firulais, Michi..."></ion-input>
            </ion-item>

            <ion-item lines="full" class="custom-input ion-margin-top">
              <ion-label position="stacked">Especie</ion-label>
              <ion-select v-model="nuevaEspecie" placeholder="Selecciona la especie">
                <ion-select-option value="Perro">Perro</ion-select-option>
                <ion-select-option value="Gato">Gato</ion-select-option>
                <ion-select-option value="Ave">Ave</ion-select-option>
                <ion-select-option value="Reptil">Reptil</ion-select-option>
                <ion-select-option value="Otro">Otro</ion-select-option>
              </ion-select>
            </ion-item>

            <ion-item lines="full" class="custom-input ion-margin-top">
              <ion-label position="stacked">Raza</ion-label>
              <ion-input v-model="nuevaRaza" placeholder="Ej. Golden Retriever, Criollo..."></ion-input>
            </ion-item>

            <ion-item lines="full" class="custom-input ion-margin-top">
              <ion-label position="stacked">Edad (en meses)</ion-label>
              <ion-input v-model="nuevaEdad" type="number" placeholder="Ej. 12"></ion-input>
            </ion-item>

            <ion-item lines="full" class="custom-input ion-margin-top">
              <ion-label position="stacked">Peso (kg)</ion-label>
              <ion-input v-model="nuevoPeso" type="number" step="0.1" placeholder="Ej. 8.5"></ion-input>
            </ion-item>

            <ion-button expand="block" class="btn-primary ion-margin-top" @click="handleGuardarMascota">
              Guardar Mascota
            </ion-button>
          </div>
        </ion-content>
      </ion-modal>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
  IonBadge, IonSpinner, IonRefresher, IonRefresherContent, IonIcon,
  IonModal, IonButton, IonButtons, IonFab, IonFabButton, IonItem,
  IonLabel, IonInput, IonSelect, IonSelectOption, toastController
} from '@ionic/vue';
import { pawOutline, medkitOutline, addOutline } from 'ionicons/icons';
import api from '../services/api';

const mascotas = ref<any[]>([]);
const historiales = ref<any[]>([]);
const loading = ref(true);
const loadingHistorial = ref(false);

// Modales
const isModalOpen = ref(false);
const isAddModalOpen = ref(false);
const selectedMascota = ref<any>(null);

// Formulario de nueva mascota
const nuevoNombre = ref('');
const nuevaEspecie = ref('Perro');
const nuevaRaza = ref('');
const nuevaEdad = ref('');
const nuevoPeso = ref('');

const fetchMascotas = async () => {
  try {
    const response = await api.get('/mascotas');
    mascotas.value = response.data;
  } catch (error) {
    console.error('Error al cargar mascotas:', error);
  } finally {
    loading.value = false;
  }
};

const openModal = async (mascota: any) => {
  selectedMascota.value = mascota;
  isModalOpen.value = true;
  loadingHistorial.value = true;
  historiales.value = [];

  try {
    const response = await api.get('/historiales');
    historiales.value = response.data.filter((h: any) => h.mascota_id === mascota.id);
  } catch (error) {
    console.error('Error al cargar historial médico:', error);
  } finally {
    loadingHistorial.value = false;
  }
};

const handleGuardarMascota = async () => {
  if (!nuevoNombre.value || !nuevaEspecie.value) {
    showToast('Por favor completa el nombre y la especie', 'warning');
    return;
  }

  try {
    await api.post('/mascotas', {
      nombre: nuevoNombre.value,
      especie: nuevaEspecie.value,
      raza: nuevaRaza.value || null,
      edad_meses: nuevaEdad.value ? parseInt(nuevaEdad.value) : null,
      peso: nuevoPeso.value ? parseFloat(nuevoPeso.value) : null
    });

    showToast('Mascota registrada exitosamente', 'success');

    nuevoNombre.value = '';
    nuevaEspecie.value = 'Perro';
    nuevaRaza.value = '';
    nuevaEdad.value = '';
    nuevoPeso.value = '';
    isAddModalOpen.value = false;

    fetchMascotas();
  } catch (error) {
    showToast('Error al registrar la mascota', 'danger');
  }
};

const handleRefresh = async (event: any) => {
  await fetchMascotas();
  event.target.complete();
};

const showToast = async (message: string, color: string) => {
  const toast = await toastController.create({
    message,
    duration: 2500,
    color
  });
  await toast.present();
};

onMounted(() => {
  fetchMascotas();
});
</script>

<style scoped>
.pet-card,
.historial-card,
.form-card {
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  margin-bottom: 16px;
  background: #FFFFFF;
}

.pet-card {
  cursor: pointer;
}

.form-card {
  padding: 20px 16px;
}

.card-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pet-name,
.form-title {
  font-size: 20px;
  font-weight: 700;
  color: #0F172A;
}

.form-title {
  margin-top: 0;
  margin-bottom: 20px;
}

.badge-custom {
  border-radius: 8px;
  padding: 6px 10px;
}

.pet-details p {
  margin: 4px 0;
  color: #475569;
}

.click-hint {
  font-size: 12px;
  color: #0D9488;
  font-weight: 600;
  margin-top: 10px;
  margin-bottom: 0;
}

.empty-state {
  text-align: center;
  padding: 40px 16px;
  color: #64748B;
}

.empty-icon {
  font-size: 64px;
  color: #CBD5E1;
}

.historial-fecha {
  font-size: 16px;
  font-weight: 700;
  color: #0F172A;
}

.field-title {
  font-weight: 700;
  color: #334155;
  margin: 0;
}

.field-desc {
  color: #64748B;
  margin: 4px 0 0 0;
}

.btn-primary {
  --background: #0D9488;
  --border-radius: 12px;
  font-weight: 600;
  height: 48px;
  margin-top: 24px;
}
</style>