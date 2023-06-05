<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddUser from '@/Pages/Admin/Partials/AddUser.vue';
import ShowUsers from '@/Pages/Admin/Partials/ShowUsers.vue';

const props = defineProps({
    roles: Array,
    users: Object,
    params: Object,
    middleware: Array,
});
</script>

<template>
    <AdminLayout title="Dashboard" v-slot="layout">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="middleware.includes('users.invite')">
            <div>
                <AddUser :user="$page.props.auth.user" :roles="roles"/>   
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="middleware.includes('users.view')">
            <div>
                <ShowUsers :users="users" :roles="roles" :params="params" :buffer="layout.buffer" :middleware="middleware"/>
            </div>
        </div>
    </AdminLayout>
</template>