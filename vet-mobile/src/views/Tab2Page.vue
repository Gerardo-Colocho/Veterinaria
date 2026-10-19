<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar style="--background: #0D9488; --color: #FFFFFF;">
        <ion-title>Agendar Cita</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding" style="--background: #F8FAFC;">
      <!-- Formulario para agendar -->
      <div class="form-card">
        <h3 class="form-title">Nueva Cita Médica</h3>

        <ion-item lines="full" class="custom-input">
          <ion-label position="stacked">Mascota</ion-label>
          <ion-select v-model="mascota_id" placeholder="Selecciona tu mascota">
            <ion-select-option v-for="m in mascotas" :key="m.id" :value="m.id">
              {{ m.nombre }} ({{ m.especie }})
            </ion-select-option>
          </ion-select>
        </ion-item>

        <ion-item lines="full" class="custom-input ion-margin-top">
          <ion-label position="stacked">Fecha y Hora</ion-label>
          <ion-input v-model="fecha_hora" type="datetime-local"></ion-input>
        </ion-item>

        <ion-item lines="full" class="custom-input ion-margin-top">
          <ion-label position="stacked">Motivo de la Consulta</ion-label>
          <ion-textarea v-model="motivo" placeholder="Ej: Vacunación, revisión general, malestar..."></ion-textarea>
        </ion-item>

        <ion-button expand="block" class="btn-primary ion-margin-top" @click="handleAgendar">
          Confirmar Cita
        </ion-button>
      </div>

      <!-- Listado de Citas Agendadas -->
      <div class="citas-section ion-margin-top">
        <h3 class="section-title">Mis Citas Agendadas</h3>

        <div v-if="loadingCitas" class="ion-text-center ion-padding">
          <ion-spinner color="teal"></ion-spinner>
        </div>

        <div v-else-if="citas.length === 0" class="empty-citas">
          <p>No tienes citas programadas por ahora.</p>
        </div>

        <ion-card v-for="cita in citas" :key="cita.id" class="cita-card">
          <ion-card-header>
            <div class="card-header-flex">
              <ion-card-title class="cita-mascota">
                🐾 {{ cita.mascota ? cita.mascota.nombre : 'Mascota' }}
              </ion-card-title>
              <ion-badge :color="cita.estado === 'completada' ? 'success' : 'warning'">
                {{ cita.estado || 'pendiente' }}
              </ion-badge>
            </div>
          </ion-card-header>
          <ion-card-content>
            <p><strong>📅 Fecha:</strong> {{ cita.fecha_hora }}</p>
            <p><strong>📝 Motivo:</strong> {{ cita.motivo }}</p>
          </ion-card-content>
        </ion-card>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonItem, IonLabel, IonSelect, IonSelectOption, IonInput,
  IonTextarea, IonButton, IonCard, IonCardHeader, IonCardTitle,
  IonCardContent, IonBadge, IonSpinner, toastController
} from '@ionic/vue';
import api from '../services/api';

const mascotas = ref<any[]>([]);
const citas = ref<any[]>([]);
const loadingCitas = ref(true);

const mascota_id = ref('');
const fecha_hora = ref('');
const motivo = ref('');

const fetchMascotas = async () => {
  try {
    const response = await api.get('/mascotas');
    mascotas.value = response.data;
  } catch (error) {
    console.error('Error al cargar mascotas:', error);
  }
};

const fetchCitas = async () => {
  try {
    loadingCitas.value = true;
    const response = await api.get('/citas');
    citas.value = response.data;
  } catch (error) {
    console.error('Error al cargar citas:', error);
  } finally {
    loadingCitas.value = false;
  }
};

const handleAgendar = async () => {
  if (!mascota_id.value || !fecha_hora.value || !motivo.value) {
    showToast('Por favor completa todos los campos', 'warning');
    return;
  }

  try {
    const fechaFormateada = fecha_hora.value.replace('T', ' ') + ':00';

    await api.post('/citas', {
      mascota_id: mascota_id.value,
      fecha_hora: fechaFormateada,
      motivo: motivo.value
    });

    showToast('Cita agendada exitosamente', 'success');
    mascota_id.value = '';
    fecha_hora.value = '';
    motivo.value = '';
    fetchCitas(); // Recarga la lista automáticamente
  } catch (error: any) {
    showToast('Error al agendar la cita', 'danger');
  }
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
  fetchCitas();
});
</script>

<style scoped>
.form-card,
.cita-card {
  background: #FFFFFF;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.form-card {
  padding: 20px 16px;
}

.cita-card {
  margin-bottom: 12px;
}

.form-title,
.section-title {
  margin-top: 0;
  color: #0F172A;
  font-weight: 700;
}

.section-title {
  margin: 24px 0 12px 4px;
  font-size: 18px;
}

.btn-primary {
  --background: #0D9488;
  --border-radius: 12px;
  font-weight: 600;
  height: 48px;
  margin-top: 24px;
}

.card-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cita-mascota {
  font-size: 18px;
  font-weight: 700;
  color: #0F172A;
}

.empty-citas {
  text-align: center;
  color: #64748B;
  padding: 16px;
}
</style>