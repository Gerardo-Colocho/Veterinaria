<template>
  <ion-page>
    <ion-header class="ion-no-border">
      <ion-toolbar style="--background: #0D9488; --color: #FFFFFF;">
        <ion-title>Mi Perfil</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content class="ion-padding" style="--background: #F8FAFC;">
      <div class="profile-card">
        <div class="avatar-circle">
          <ion-icon :icon="person" class="avatar-icon"></ion-icon>
        </div>
        <h2>{{ user?.name || 'Usuario' }}</h2>
        <p class="role-badge">{{ user?.rol?.toUpperCase() || 'CLIENTE' }}</p>

        <ion-list lines="inset" class="info-list">
          <ion-item>
            <ion-label>
              <h3>Correo Electrónico</h3>
              <p>{{ user?.email }}</p>
            </ion-label>
          </ion-item>
          <ion-item>
            <ion-label>
              <h3>Teléfono</h3>
              <p>{{ user?.telefono || 'No registrado' }}</p>
            </ion-label>
          </ion-item>
        </ion-list>

        <ion-button expand="block" color="danger" class="btn-logout" @click="handleLogout">
          Cerrar Sesión
        </ion-button>
      </div>
    </ion-content>
  </ion-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import {
  IonPage, IonHeader, IonToolbar, IonTitle, IonContent,
  IonList, IonItem, IonLabel, IonButton, IonIcon
} from '@ionic/vue';
import { person } from 'ionicons/icons';

const router = useRouter();
const user = ref<any>(null);

onMounted(() => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  }
});

const handleLogout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  router.push('/login');
};
</script>

<style scoped>
.profile-card {
  background: #FFFFFF;
  border-radius: 16px;
  padding: 24px 16px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.avatar-circle {
  width: 72px;
  height: 72px;
  background: #E2E8F0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px auto;
}

.avatar-icon {
  font-size: 36px;
  color: #64748B;
}

.profile-card h2 {
  margin: 0;
  font-size: 20px;
  color: #0F172A;
}

.role-badge {
  display: inline-block;
  background: #E0F2FE;
  color: #0284C7;
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 700;
  margin-top: 6px;
}

.info-list {
  margin-top: 20px;
  text-align: left;
}

.btn-logout {
  --border-radius: 12px;
  margin-top: 24px;
}
</style>