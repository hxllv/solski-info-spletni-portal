<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddUser from '@/Pages/Admin/Partials/AddUser.vue';
import ShowUsers from '@/Pages/Admin/Partials/ShowUsers.vue';

const props = defineProps({
    roles: Array,
    classes: Array,
    users: Object,
    params: Object,
    permission: Array,
});
</script>

<template>
    <AdminLayout title="Uporabniki" v-slot="layout" :permission="permission">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('users.invite')">
            <div>
                <AddUser :roles="roles" :classes="classes"/>   
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('users.view')">
            <div>
                <ShowUsers :users="users" :roles="roles" :classes="classes" :params="params" :buffer="layout.buffer" :permission="permission"/>
            </div>
        </div>
    </AdminLayout>
</template>