<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar style="--background: #0D9488; --color: #FFFFFF;">
        <ion-title>Mis Citas</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding" style="--background: #F8FAFC;">
      <ion-refresher slot="fixed" @ionRefresh="handleRefresh($event)">
        <ion-refresher-content></ion-refresher-content>
      </ion-refresher>

      <div v-if="loading" class="ion-text-center ion-padding">
        <ion-spinner color="teal"></ion-spinner>
      </div>

      <div v-else-if="citas.length === 0" class="empty-state">
        <ion-icon :icon="calendarOutline" class="empty-icon"></ion-icon>
        <h3>No tienes citas agendadas</h3>
        <p>Agenda una cita para tu mascota en el formulario de abajo.</p>
      </div>

      <!-- Lista de Citas Agendadas -->
      <div v-else>
        <ion-card v-for="cita in citas" :key="cita.id" class="cita-card">
          <ion-card-header>
            <div class="card-header-flex">
              <ion-card-title class="cita-titulo">
                🐾 {{ cita.mascota?.nombre || 'Mascota' }}
              </ion-card-title>
              <ion-badge :color="getBadgeColor(cita.estado)">
                {{ cita.estado }}
              </ion-badge>
            </div>
            <ion-card-subtitle>📅 {{ cita.fecha_hora }}</ion-card-subtitle>
          </ion-card-header>

          <ion-card-content>
            <p><strong>Motivo:</strong> {{ cita.motivo }}</p>
            <p v-if="cita.motivo_cancelacion" class="motivo-cancelado">
              ❌ <strong>Justificación de Cancelación:</strong> {{ cita.motivo_cancelacion }}
            </p>

            <ion-button v-if="cita.estado === 'Pendiente' || cita.estado === 'pendiente'" color="danger" fill="outline"
              size="small" class="ion-margin-top" @click="openCancelarAlert(cita.id)">
              Cancelar Cita
            </ion-button>
          </ion-card-content>
        </ion-card>
      </div>

      <!-- Formulario de Agendamiento Profesional -->
      <div class="form-card ion-margin-top">
        <h3 class="form-title">Agendar Nueva Cita</h3>

        <!-- Selección de Mascota -->
        <ion-item lines="full" class="custom-input">
          <ion-label position="stacked">Mascota</ion-label>
          <ion-select v-model="nuevaCita.mascota_id" placeholder="Selecciona tu mascota">
            <ion-select-option v-for="m in mascotas" :key="m.id" :value="m.id">
              {{ m.nombre }} ({{ m.especie }})
            </ion-select-option>
          </ion-select>
        </ion-item>

        <!-- Paso 1: Mini Calendario Flotante -->
        <div class="section-step ion-margin-top">
          <label class="step-label">1. Selecciona la Fecha</label>
          <div class="calendar-wrapper">
            <input ref="calendarInput" type="text" placeholder="Haz clic para seleccionar fecha..."
              class="custom-bootstrap-input" readonly />
          </div>
        </div>

        <!-- Paso 2: Selección de Horarios en Píldoras -->
        <div class="section-step ion-margin-top">
          <label class="step-label">2. Horarios Disponibles para esta fecha</label>

          <div v-if="loadingHorarios" class="ion-text-center ion-padding">
            <ion-spinner color="teal"></ion-spinner>
          </div>

          <div v-else-if="horariosDisponibles.length === 0" class="no-slots-alert">
            🚫 No hay citas libres para esta fecha. Por favor selecciona otro día.
          </div>

          <!-- Píldoras de Horarios -->
          <div v-else class="time-slots-container">
            <button v-for="hora in todosLosHorarios" :key="hora" type="button" class="time-slot-btn" :class="{
              'selected': horaSeleccionada === hora,
              'disabled': !horariosDisponibles.includes(hora)
            }" :disabled="!horariosDisponibles.includes(hora)" @click="horaSeleccionada = hora">
              🕒 {{ hora }}
            </button>
          </div>
        </div>

        <!-- Paso 3: Motivo de la Consulta -->
        <ion-item lines="full" class="custom-input ion-margin-top">
          <ion-label position="stacked">Motivo de la consulta</ion-label>
          <ion-input v-model="nuevaCita.motivo" placeholder="Ej. Chequeo general, vacunación..."></ion-input>
        </ion-item>

        <ion-button expand="block" class="btn-primary ion-margin-top" @click="handleAgendarCita">
          CONFIRMAR Y AGENDAR
        </ion-button>
      </div>

    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonCard, IonCardHeader, IonCardTitle, IonCardSubtitle, IonCardContent,
  IonBadge, IonSpinner, IonRefresher, IonRefresherContent, IonIcon,
  IonButton, IonItem, IonLabel, IonInput, IonSelect, IonSelectOption,
  toastController, alertController
} from '@ionic/vue';
import { calendarOutline } from 'ionicons/icons';
import api from '../services/api';

import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Spanish } from 'flatpickr/dist/l10n/es';

const citas = ref<any[]>([]);
const mascotas = ref<any[]>([]);
const loading = ref(true);
const loadingHorarios = ref(false);

const calendarInput = ref<HTMLInputElement | null>(null);
const fechaMinima = new Date().toISOString().split('T')[0];
const fechaSeleccionada = ref(fechaMinima);
const horaSeleccionada = ref('');
const horariosDisponibles = ref<string[]>([]);

const todosLosHorarios = [
  '08:00', '09:00', '10:00', '11:00',
  '13:00', '14:00', '15:00', '16:00'
];

const nuevaCita = ref({
  mascota_id: '',
  motivo: ''
});

const fetchCitasYMascotas = async () => {
  try {
    const [resCitas, resMascotas] = await Promise.all([
      api.get('/citas'),
      api.get('/mascotas')
    ]);
    citas.value = resCitas.data;
    mascotas.value = resMascotas.data;
  } catch (error) {
    console.error('Error al cargar datos:', error);
  } finally {
    loading.value = false;
  }
};

const cargarHorariosDisponibles = async () => {
  if (!fechaSeleccionada.value) return;
  loadingHorarios.value = true;
  horaSeleccionada.value = '';

  try {
    const response = await api.get(`/citas/disponibilidad?fecha=${fechaSeleccionada.value}`);
    horariosDisponibles.value = response.data.horarios_disponibles;
  } catch (error) {
    console.error('Error al consultar horarios:', error);
    horariosDisponibles.value = [...todosLosHorarios];
  } finally {
    loadingHorarios.value = false;
  }
};

const handleAgendarCita = async () => {
  if (!nuevaCita.value.mascota_id || !fechaSeleccionada.value || !horaSeleccionada.value || !nuevaCita.value.motivo) {
    showToast('Por favor selecciona la mascota, fecha, hora y motivo', 'warning');
    return;
  }

  const fechaHoraCompleta = `${fechaSeleccionada.value} ${horaSeleccionada.value}:00`;

  try {
    await api.post('/citas', {
      mascota_id: nuevaCita.value.mascota_id,
      fecha_hora: fechaHoraCompleta,
      motivo: nuevaCita.value.motivo
    });

    showToast('¡Cita agendada con éxito!', 'success');
    nuevaCita.value = { mascota_id: '', motivo: '' };
    horaSeleccionada.value = '';
    fetchCitasYMascotas();
    cargarHorariosDisponibles();
  } catch (error) {
    showToast('Error al agendar la cita', 'danger');
  }
};

const openCancelarAlert = async (citaId: number) => {
  const alert = await alertController.create({
    header: 'Cancelar Cita',
    subHeader: 'Por favor ingresa el motivo de la cancelación',
    inputs: [
      {
        name: 'motivo_cancelacion',
        type: 'textarea',
        placeholder: 'Ej. Emergencia personal, cambio de horario...'
      }
    ],
    buttons: [
      {
        text: 'Volver',
        role: 'cancel'
      },
      {
        text: 'Confirmar Cancelación',
        handler: async (data) => {
          if (!data.motivo_cancelacion || data.motivo_cancelacion.trim() === '') {
            showToast('Debes ingresar una justificación para cancelar', 'warning');
            return false;
          }
          await cancelarCita(citaId, data.motivo_cancelacion);
        }
      }
    ]
  });

  await alert.present();
};

const cancelarCita = async (id: number, motivo_cancelacion: string) => {
  try {
    await api.put(`/citas/${id}`, {
      estado: 'Cancelada',
      motivo_cancelacion: motivo_cancelacion
    });
    showToast('La cita ha sido cancelada', 'success');
    fetchCitasYMascotas();
    cargarHorariosDisponibles();
  } catch (error) {
    showToast('Error al cancelar la cita', 'danger');
  }
};

const getBadgeColor = (estado: string) => {
  switch (estado.toLowerCase()) {
    case 'pendiente': return 'warning';
    case 'completada': return 'success';
    case 'cancelada': return 'danger';
    default: return 'medium';
  }
};

const handleRefresh = async (event: any) => {
  await fetchCitasYMascotas();
  await cargarHorariosDisponibles();
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

onMounted(async () => {
  await fetchCitasYMascotas();
  await cargarHorariosDisponibles();

  if (calendarInput.value) {
    flatpickr(calendarInput.value, {
      locale: Spanish,
      minDate: 'today',
      dateFormat: 'Y-m-d',
      defaultDate: 'today',
      disableMobile: true,
      onChange: (selectedDates, dateStr) => {
        fechaSeleccionada.value = dateStr;
        cargarHorariosDisponibles();
      }
    });
  }
});
</script>

<style scoped>
.cita-card,
.form-card {
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
  background: #FFFFFF;
}

.form-card {
  padding: 24px 20px;
  border: 1px solid #E2E8F0;
}

.card-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cita-titulo,
.form-title {
  font-size: 18px;
  font-weight: 700;
  color: #0F172A;
}

.form-title {
  margin-top: 0;
  margin-bottom: 20px;
}

.step-label {
  font-size: 13px;
  font-weight: 700;
  color: #0D9488;
  display: block;
  margin-bottom: 8px;
}

.calendar-wrapper {
  margin-top: 4px;
}

.custom-bootstrap-input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #CBD5E1;
  border-radius: 12px;
  background-color: #FFFFFF;
  color: #0F172A;
  font-size: 15px;
  font-weight: 600;
  outline: none;
  cursor: pointer;
  transition: border-color 0.2s ease;
}

.custom-bootstrap-input:focus {
  border-color: #0D9488;
  box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
}

.custom-input {
  --background: #FFFFFF !important;
  --color: #1E293B !important;
  --placeholder-color: #94A3B8 !important;
  --highlight-color-focused: #0D9488 !important;
  border: 1px solid #CBD5E1;
  border-radius: 12px;
  background: #FFFFFF !important;
  color: #1E293B !important;
}

.custom-input ion-label {
  color: #0D9488 !important;
  font-weight: 600 !important;
  font-size: 13px !important;
}

.custom-input ion-input,
.custom-input ion-select {
  --color: #0F172A !important;
  color: #0F172A !important;
  font-weight: 500;
}

.time-slots-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 8px;
}

.time-slot-btn {
  padding: 10px 14px;
  border-radius: 12px;
  border: 1px solid #CBD5E1;
  background: #F8FAFC;
  color: #334155;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 4px;
}

.time-slot-btn:hover:not(:disabled) {
  border-color: #0D9488;
  color: #0D9488;
}

.time-slot-btn.selected {
  background: #0D9488 !important;
  color: #FFFFFF !important;
  border-color: #0D9488 !important;
  box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
}

.time-slot-btn.disabled {
  background: #F1F5F9;
  color: #94A3B8;
  border-color: #E2E8F0;
  text-decoration: line-through;
  cursor: not-allowed;
  opacity: 0.6;
}

.no-slots-alert {
  background: #FEF2F2;
  color: #DC2626;
  padding: 12px;
  border-radius: 10px;
  font-size: 13px;
  border: 1px solid #FCA5A5;
  margin-top: 8px;
}

.motivo-cancelado {
  color: #DC2626;
  margin-top: 8px;
  font-size: 13px;
  background: #FEF2F2;
  padding: 10px 12px;
  border-radius: 8px;
  border: 1px solid #FCA5A5;
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

.btn-primary {
  --background: #0D9488;
  --background-hover: #0F766E;
  --border-radius: 12px;
  font-weight: 600;
  height: 48px;
  margin-top: 24px;
}
</style>