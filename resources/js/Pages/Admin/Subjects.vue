<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AddSubject from '@/Pages/Admin/Partials/AddSubject.vue';
import ShowSubjects from '@/Pages/Admin/Partials/ShowSubjects.vue';

const props = defineProps({
    subjects: Object,
    params: Object,
    permission: Array,
});
</script>

<template>
    <AdminLayout title="Predmeti"  v-slot="layout" :permission="permission">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('subjects.create')">
            <div>
                <AddSubject />
            </div>
        </div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8" v-if="permission.includes('subjects.view')">
            <div>
                <ShowSubjects :subjects="subjects" :params="params" :buffer="layout.buffer" :permission="permission"/>
            </div>
        </div>
    </AdminLayout>
</template>