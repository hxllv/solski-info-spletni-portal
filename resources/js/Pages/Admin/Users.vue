<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddUser from '@/Pages/Admin/Partials/AddUser.vue';
import ShowUsers from '@/Pages/Admin/Partials/ShowUsers.vue';

const props = defineProps({
    roles: Array,
    users: Array,
    can: Object,
});
</script>

<template>
    <AdminLayout title="Dashboard">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <AddUser :user="$page.props.auth.user" :roles="roles" v-if="props.can.invite"/>   
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <ShowUsers :users="users" :roles="roles" />
            </div>
        </div>
    </AdminLayout>
</template>