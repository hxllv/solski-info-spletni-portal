<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import EditHours from '@/Pages/Admin/Partials/EditHours.vue';
import Table from '@/Components/Table.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    hours: Object,
    permission: Array,
});
</script>

<template>
    <AdminLayout title="Ostale nastavitve" v-slot="layout" :permission="permission">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('users.invite')">
            <div>
                <EditHours :hours="hours"/>   
            </div>
        </div>
    </AdminLayout>
</template>