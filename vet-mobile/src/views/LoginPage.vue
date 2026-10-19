<template>
    <ion-page>
        <ion-content class="ion-padding login-container">
            <div class="login-wrapper">
                <!-- Logo y Encabezado -->
                <div class="header-section">
                    <div class="logo-circle">
                        <ion-icon :icon="pawOutline" class="logo-icon"></ion-icon>
                    </div>
                    <h1 class="app-title">VetCare App</h1>
                    <p class="app-subtitle">{{ subtitleText }}</p>
                </div>

                <!-- Formulario -->
                <div class="card-form">
                    <!-- Campo Nombre (solo en Registro) -->
                    <ion-item v-if="isRegister" lines="full" class="custom-input">
                        <ion-label position="stacked">Nombre Completo</ion-label>
                        <ion-input v-model="name" type="text" placeholder="Ej. Juan Pérez"></ion-input>
                    </ion-item>

                    <ion-item lines="full" class="custom-input ion-margin-top">
                        <ion-label position="stacked">Correo Electrónico</ion-label>
                        <ion-input v-model="email" type="email" placeholder="ejemplo@correo.com"></ion-input>
                    </ion-item>

                    <ion-item lines="full" class="custom-input ion-margin-top">
                        <ion-label position="stacked">Contraseña</ion-label>
                        <ion-input v-model="password" type="password" placeholder="••••••••"></ion-input>
                    </ion-item>

                    <!-- Botón Principal -->
                    <ion-button expand="block" class="btn-submit ion-margin-top" :disabled="loading"
                        @click="handleSubmit">
                        <ion-spinner v-if="loading" name="crescent"></ion-spinner>
                        <span v-else>{{ isRegister ? 'CREAR CUENTA' : 'INGRESAR' }}</span>
                    </ion-button>

                    <!-- Enlace para alternar entre Login y Registro -->
                    <div class="toggle-mode ion-margin-top">
                        <p v-if="!isRegister">
                            ¿No tienes una cuenta?
                            <a href="#" @click.prevent="toggleMode">Regístrate aquí</a>
                        </p>
                        <p v-else>
                            ¿Ya tienes cuenta?
                            <a href="#" @click.prevent="toggleMode">Inicia Sesión</a>
                        </p>
                    </div>
                </div>
            </div>
        </ion-content>
    </ion-page>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import {
    IonPage, IonContent, IonItem, IonLabel, IonInput,
    IonButton, IonSpinner, IonIcon, toastController
} from '@ionic/vue';
import { pawOutline } from 'ionicons/icons';
import api from '../services/api';

const router = useRouter();

const isRegister = ref(false);
const loading = ref(false);

const name = ref('');
const email = ref('');
const password = ref('');

// Texto dinámico formateado desde script para evitar saltos de línea en comillas HTML
const subtitleText = computed(() => {
    return isRegister.value
        ? 'Crea tu cuenta para gestionar tus mascotas'
        : 'Inicia sesión para gestionar tus mascotas';
});

const toggleMode = () => {
    isRegister.value = !isRegister.value;
};

const handleSubmit = async () => {
    if (!email.value || !password.value || (isRegister.value && !name.value)) {
        showToast('Por favor completa todos los campos', 'warning');
        return;
    }

    loading.value = true;

    try {
        const endpoint = isRegister.value ? '/register' : '/login';
        const payload = isRegister.value
            ? { name: name.value, email: email.value, password: password.value }
            : { email: email.value, password: password.value };

        const response = await api.post(endpoint, payload);

        if (response.data.token) {
            localStorage.setItem('token', response.data.token);
        }
        if (response.data.user) {
            localStorage.setItem('user', JSON.stringify(response.data.user));
        }

        showToast(isRegister.value ? 'Cuenta creada exitosamente' : 'Bienvenido a VetCare', 'success');

        router.push('/tabs/tab1');
    } catch (error: any) {
        const errorMsg = error.response?.data?.message || 'Error al autenticar. Verifica tus datos.';
        showToast(errorMsg, 'danger');
    } finally {
        loading.value = false;
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
</script>

<style scoped>
.login-container {
    --background: #F8FAFC;
}

.login-wrapper {
    max-width: 400px;
    margin: 40px auto 0 auto;
}

.header-section {
    text-align: center;
    margin-bottom: 24px;
}

.logo-circle {
    width: 72px;
    height: 72px;
    background-color: #0D9488;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px auto;
    box-shadow: 0 4px 10px rgba(13, 148, 136, 0.3);
}

.logo-icon {
    font-size: 38px;
    color: #FFFFFF;
}

.app-title {
    font-size: 26px;
    font-weight: 800;
    color: #0F172A;
    margin: 0;
}

.app-subtitle {
    font-size: 14px;
    color: #64748B;
    margin-top: 4px;
}

.card-form {
    background: #FFFFFF;
    padding: 24px 20px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.btn-submit {
    --background: #0D9488;
    --border-radius: 12px;
    font-weight: 700;
    height: 48px;
}

.toggle-mode {
    text-align: center;
    font-size: 14px;
    color: #64748B;
}

.toggle-mode a {
    color: #0D9488;
    font-weight: 700;
    text-decoration: none;
}
</style>